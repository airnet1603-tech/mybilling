<?php
namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use App\Models\Tagihan;
use App\Models\Pembayaran;
use App\Models\Pelanggan;
use App\Services\DuitkuService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DuitkuPaymentController extends Controller
{
    private DuitkuService $duitku;

    public function __construct(DuitkuService $duitku)
    {
        $this->duitku = $duitku;
    }

    private function getPelanggan()
    {
        return Pelanggan::findOrFail(session('pelanggan_id'));
    }

    public function show(string $noTagihan)
    {
        $pelanggan = $this->getPelanggan();
        $tagihan = Tagihan::where('no_tagihan', $noTagihan)
            ->where('pelanggan_id', $pelanggan->id)
            ->where('status', 'unpaid')
            ->firstOrFail();
        return view('pelanggan.payment.show', compact('tagihan', 'pelanggan'));
    }

    public function createVa(string $noTagihan)
    {
        $pelanggan = $this->getPelanggan();
        $tagihan = Tagihan::where('no_tagihan', $noTagihan)
            ->where('pelanggan_id', $pelanggan->id)
            ->where('status', 'unpaid')
            ->firstOrFail();

        if ($tagihan->bri_va_number && $tagihan->bri_expired_at && now()->lt($tagihan->bri_expired_at)) {
            return response()->json([
                'success'     => true,
                'type'        => 'va',
                'va_number'   => $tagihan->bri_va_number,
                'payment_url' => $tagihan->duitku_payment_url,
                'amount'      => $tagihan->total,
                'expired_at'  => \Carbon\Carbon::parse($tagihan->bri_expired_at)->format('d/m/Y H:i'),
            ]);
        }

        try {
            $result = $this->duitku->createTransaction([
                'no_tagihan'     => $noTagihan,
                'amount'         => $tagihan->total,
                'nama_pelanggan' => $pelanggan->nama,
                'email'          => $pelanggan->email ?? 'pelanggan@isp.com',
                'product_details'=> 'Tagihan Internet ' . $tagihan->periode,
                'payment_method' => 'BR',
                'expiry_period'  => 1440,
            ]);

            $tagihan->update([
                'metode_bayar'       => 'duitku_va',
                'bri_va_number'      => $result['vaNumber'] ?? null,
                'bri_expired_at'     => now()->addHours(24),
                'bri_ref_no'         => $result['reference'] ?? null,
                'duitku_payment_url' => $result['paymentUrl'] ?? null,
            ]);

            return response()->json([
                'success'     => true,
                'type'        => 'va',
                'va_number'   => $result['vaNumber'] ?? null,
                'payment_url' => $result['paymentUrl'] ?? null,
                'amount'      => $tagihan->total,
                'expired_at'  => now()->addHours(24)->format('d/m/Y H:i'),
            ]);
        } catch (\Exception $e) {
            Log::error('Duitku Create VA Error', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function createQris(string $noTagihan)
    {
        $pelanggan = $this->getPelanggan();
        $tagihan = Tagihan::where('no_tagihan', $noTagihan)
            ->where('pelanggan_id', $pelanggan->id)
            ->where('status', 'unpaid')
            ->firstOrFail();

        try {
            $result = $this->duitku->createTransaction([
                'no_tagihan'     => $noTagihan,
                'amount'         => $tagihan->total,
                'nama_pelanggan' => $pelanggan->nama,
                'email'          => $pelanggan->email ?? 'pelanggan@isp.com',
                'product_details'=> 'Tagihan Internet ' . $tagihan->periode,
                'payment_method' => 'QR',
                'expiry_period'  => 30,
            ]);

            $tagihan->update([
                'metode_bayar'       => 'duitku_qris',
                'bri_expired_at'     => now()->addMinutes(30),
                'bri_ref_no'         => $result['reference'] ?? null,
                'duitku_payment_url' => $result['paymentUrl'] ?? null,
            ]);

            return response()->json([
                'success'     => true,
                'type'        => 'qris',
                'payment_url' => $result['paymentUrl'] ?? null,
                'expired_at'  => now()->addMinutes(30)->format('H:i'),
            ]);
        } catch (\Exception $e) {
            Log::error('Duitku Create QRIS Error', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function checkStatus(string $noTagihan)
    {
        $pelanggan = $this->getPelanggan();
        $tagihan = Tagihan::where('no_tagihan', $noTagihan)
            ->where('pelanggan_id', $pelanggan->id)
            ->firstOrFail();

        if ($tagihan->status === 'paid') {
            return response()->json(['success' => true, 'paid' => true]);
        }

        try {
            $result = $this->duitku->checkTransaction($noTagihan);
            $isPaid = ($result['statusCode'] ?? '') === '00';

            if ($isPaid) {
                DB::transaction(function () use ($tagihan) {
                    $tagihan->update([
                        'status'    => 'paid',
                        'tgl_bayar' => now()->toDateString(),
                    ]);
                    Pembayaran::create([
                        'no_pembayaran' => 'DUITKU-' . now()->format('YmdHis') . '-' . $tagihan->id,
                        'tagihan_id'    => $tagihan->id,
                        'pelanggan_id'  => $tagihan->pelanggan_id,
                        'jumlah_bayar'  => $tagihan->total,
                        'metode'        => $tagihan->metode_bayar,
                        'catatan'       => 'Dibayar via Duitku',
                    ]);
                });
                return response()->json(['success' => true, 'paid' => true]);
            }

            return response()->json(['success' => true, 'paid' => false]);
        } catch (\Exception $e) {
            Log::error('Duitku Check Status Error', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function webhook(Request $request)
    {
        Log::info('Duitku Webhook', $request->all());

        $merchantOrderId = $request->merchantOrderId;
        $amount          = (int) $request->amount;
        $statusCode      = $request->statusCode;
        $signature       = $request->signature;

        if (!$this->duitku->verifyCallbackSignature($merchantOrderId, $amount, $statusCode, $signature)) {
            Log::warning('Duitku Webhook: Invalid signature');
            return response('Invalid signature', 400);
        }

        if ($statusCode !== '00') {
            return response('OK', 200);
        }

        $tagihan = Tagihan::where('no_tagihan', $merchantOrderId)->first();
        if (!$tagihan || $tagihan->status === 'paid') {
            return response('OK', 200);
        }

        DB::transaction(function () use ($tagihan, $request) {
            $tagihan->update([
                'status'    => 'paid',
                'tgl_bayar' => now()->toDateString(),
            ]);
            Pembayaran::create([
                'no_pembayaran' => 'DUITKU-WH-' . now()->format('YmdHis') . '-' . $tagihan->id,
                'tagihan_id'    => $tagihan->id,
                'pelanggan_id'  => $tagihan->pelanggan_id,
                'jumlah_bayar'  => $tagihan->total,
                'metode'        => $tagihan->metode_bayar ?? 'duitku',
                'catatan'       => 'Webhook Duitku - ' . ($request->reference ?? '-'),
            ]);
        });

        return response('OK', 200);
    }
}
