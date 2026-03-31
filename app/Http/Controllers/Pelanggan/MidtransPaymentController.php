<?php
namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use App\Models\Tagihan;
use App\Models\Pembayaran;
use App\Models\Pelanggan;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MidtransPaymentController extends Controller
{
    private MidtransService $midtrans;

    public function __construct(MidtransService $midtrans)
    {
        $this->midtrans = $midtrans;
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

    public function create(string $noTagihan)
    {
        $pelanggan = $this->getPelanggan();
        $tagihan = Tagihan::where('no_tagihan', $noTagihan)
            ->where('pelanggan_id', $pelanggan->id)
            ->where('status', 'unpaid')
            ->firstOrFail();

        // Kalau sudah ada payment URL, langsung return tanpa buat baru
        if ($tagihan->duitku_payment_url) {
            return response()->json([
                'success'     => true,
                'payment_url' => $tagihan->duitku_payment_url,
            ]);
        }

        try {
            $result = $this->midtrans->createTransaction([
                'no_tagihan'      => $noTagihan,
                'amount'          => $tagihan->total,
                'nama_pelanggan'  => $pelanggan->nama,
                'email'           => $pelanggan->email ?? 'pelanggan@isp.com',
                'phone'           => $pelanggan->no_hp ?? '',
                'product_details' => 'Tagihan Internet ' . $tagihan->periode,
                'expiry_duration' => 24,
            ]);

            $tagihan->update([
                'metode_bayar'       => 'midtrans',
                'bri_expired_at'     => now()->addHours(24),
                'bri_ref_no'         => $result['order_id'] ?? null,
                'duitku_payment_url' => $result['payment_url'] ?? null,
            ]);

            return response()->json([
                'success'     => true,
                'payment_url' => $result['payment_url'] ?? null,
            ]);
        } catch (\Exception $e) {
            Log::error('Midtrans Create Error', ['error' => $e->getMessage()]);
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
            $result = $this->midtrans->checkTransaction($noTagihan);
            $status = $result['transaction_status'] ?? '';
            $isPaid = in_array($status, ['capture', 'settlement']);

            if ($isPaid) {
                DB::transaction(function () use ($tagihan) {
                    $tagihan->update([
                        'status'    => 'paid',
                        'tgl_bayar' => now()->toDateString(),
                    ]);
                    Pembayaran::create([
                        'no_pembayaran' => 'MIDTRANS-' . now()->format('YmdHis') . '-' . $tagihan->id,
                        'tagihan_id'    => $tagihan->id,
                        'pelanggan_id'  => $tagihan->pelanggan_id,
                        'jumlah_bayar'  => $tagihan->total,
                        'metode'        => 'midtrans',
                        'catatan'       => 'Dibayar via Midtrans',
                    ]);
                });
                return response()->json(['success' => true, 'paid' => true]);
            }

            return response()->json(['success' => true, 'paid' => false]);
        } catch (\Exception $e) {
            Log::error('Midtrans Check Status Error', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function webhook(Request $request)
    {
        Log::info('Midtrans Webhook', $request->all());
        $data = $request->all();

        if (!$this->midtrans->verifyWebhook($data)) {
            Log::warning('Midtrans Webhook: Invalid signature');
            return response()->json(['message' => 'Invalid signature'], 400);
        }

        $status = $data['transaction_status'] ?? '';
        $isPaid = in_array($status, ['capture', 'settlement']);

        if (!$isPaid) {
            return response()->json(['message' => 'OK']);
        }

        $tagihan = Tagihan::where('no_tagihan', $data['order_id'])->first();
        if (!$tagihan || $tagihan->status === 'paid') {
            return response()->json(['message' => 'OK']);
        }

        DB::transaction(function () use ($tagihan, $data) {
            $tagihan->update([
                'status'    => 'paid',
                'tgl_bayar' => now()->toDateString(),
            ]);
            Pembayaran::create([
                'no_pembayaran' => 'MIDTRANS-WH-' . now()->format('YmdHis') . '-' . $tagihan->id,
                'tagihan_id'    => $tagihan->id,
                'pelanggan_id'  => $tagihan->pelanggan_id,
                'jumlah_bayar'  => $tagihan->total,
                'metode'        => 'midtrans',
                'catatan'       => 'Webhook Midtrans - ' . ($data['payment_type'] ?? '-'),
            ]);
        });

        return response()->json(['message' => 'OK']);
    }
}
