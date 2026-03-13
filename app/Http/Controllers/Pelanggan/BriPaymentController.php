<?php
namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use App\Models\Tagihan;
use App\Models\Pembayaran;
use App\Models\Pelanggan;
use App\Services\BriApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BriPaymentController extends Controller
{
    private BriApiService $bri;

    public function __construct(BriApiService $bri)
    {
        $this->bri = $bri;
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

    public function createQris(string $noTagihan)
    {
        $pelanggan = $this->getPelanggan();
        $tagihan = Tagihan::where('no_tagihan', $noTagihan)
            ->where('pelanggan_id', $pelanggan->id)
            ->where('status', 'unpaid')
            ->firstOrFail();

        if ($tagihan->bri_qris_data && $tagihan->bri_expired_at && now()->lt($tagihan->bri_expired_at)) {
            return response()->json([
                'success'    => true,
                'type'       => 'qris',
                'qris_image' => $tagihan->bri_qris_data,
                'expired_at' => \Carbon\Carbon::parse($tagihan->bri_expired_at)->format('H:i'),
            ]);
        }

        try {
            $result = $this->bri->createQris([
                'no_tagihan'      => $noTagihan,
                'amount'          => $tagihan->total,
                'nama_pelanggan'  => $pelanggan->nama,
                'expired_minutes' => 30,
            ]);

            $qrisContent = $result['qrContent']
                ?? $result['qrUrl']
                ?? $result['data']['qrContent']
                ?? null;

            $tagihan->update([
                'metode_bayar'   => 'bri_qris',
                'bri_qris_data'  => $qrisContent,
                'bri_expired_at' => now()->addMinutes(30),
                'bri_ref_no'     => $result['referenceNo'] ?? null,
            ]);

            return response()->json([
                'success'    => true,
                'type'       => 'qris',
                'qris_image' => $qrisContent,
                'expired_at' => now()->addMinutes(30)->format('H:i'),
            ]);
        } catch (\Exception $e) {
            Log::error('Create QRIS Error', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
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
                'success'    => true,
                'type'       => 'va',
                'va_number'  => $tagihan->bri_va_number,
                'amount'     => $tagihan->total,
                'expired_at' => \Carbon\Carbon::parse($tagihan->bri_expired_at)->format('d/m/Y H:i'),
            ]);
        }

        try {
            $result = $this->bri->createVirtualAccount([
                'no_tagihan'     => $noTagihan,
                'amount'         => $tagihan->total,
                'nama_pelanggan' => $pelanggan->nama,
                'expired_hours'  => 24,
            ]);

            $vaNumber = $result['virtualAccountData']['virtualAccountNo']
                ?? $result['virtualAccountNo']
                ?? null;

            $tagihan->update([
                'metode_bayar'   => 'bri_va',
                'bri_va_number'  => $vaNumber,
                'bri_expired_at' => now()->addHours(24),
                'bri_ref_no'     => $result['referenceNo'] ?? null,
            ]);

            return response()->json([
                'success'    => true,
                'type'       => 'va',
                'va_number'  => $vaNumber,
                'amount'     => $tagihan->total,
                'expired_at' => now()->addHours(24)->format('d/m/Y H:i'),
            ]);
        } catch (\Exception $e) {
            Log::error('Create VA Error', ['error' => $e->getMessage()]);
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
            $result = [];
            if ($tagihan->metode_bayar === 'bri_qris') {
                $result = $this->bri->checkQrisStatus($noTagihan);
            } elseif ($tagihan->metode_bayar === 'bri_va') {
                $result = $this->bri->checkVaStatus($noTagihan);
            }

            $responseCode = $result['responseCode'] ?? '';
            $isPaid = in_array($responseCode, ['2005700', '2002600', '00', '2000000']);

            if ($isPaid) {
                DB::transaction(function () use ($tagihan) {
                    $tagihan->update([
                        'status'    => 'paid',
                        'tgl_bayar' => now()->toDateString(),
                    ]);
                    Pembayaran::create([
                        'no_pembayaran' => 'BRI-' . now()->format('YmdHis') . '-' . $tagihan->id,
                        'tagihan_id'    => $tagihan->id,
                        'pelanggan_id'  => $tagihan->pelanggan_id,
                        'jumlah_bayar'  => $tagihan->total,
                        'metode'        => $tagihan->metode_bayar,
                        'catatan'       => 'Dibayar via BRI ' . strtoupper(str_replace('bri_', '', $tagihan->metode_bayar)),
                    ]);
                });
                return response()->json(['success' => true, 'paid' => true]);
            }

            return response()->json(['success' => true, 'paid' => false]);
        } catch (\Exception $e) {
            Log::error('Check Status Error', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function webhook(Request $request)
    {
        Log::info('BRI Webhook', $request->all());
        $data  = $request->json()->all();
        $refNo = $data['originalPartnerReferenceNo'] ?? $data['partnerReferenceNo'] ?? $data['trxId'] ?? null;

        if (!$refNo) return response()->json(['responseCode' => '4000000', 'responseMessage' => 'Bad Request']);

        $tagihan = Tagihan::where('no_tagihan', $refNo)->first();
        if (!$tagihan || $tagihan->status === 'paid') {
            return response()->json(['responseCode' => '2000000', 'responseMessage' => 'Success']);
        }

        DB::transaction(function () use ($tagihan, $data) {
            $metode = isset($data['qrContent']) ? 'bri_qris' : 'bri_va';
            $tagihan->update(['status' => 'paid', 'tgl_bayar' => now()->toDateString(), 'metode_bayar' => $metode]);
            Pembayaran::create([
                'no_pembayaran' => 'BRI-WH-' . now()->format('YmdHis') . '-' . $tagihan->id,
                'tagihan_id'    => $tagihan->id,
                'pelanggan_id'  => $tagihan->pelanggan_id,
                'jumlah_bayar'  => $tagihan->total,
                'metode'        => $metode,
                'catatan'       => 'Webhook BRI - ' . ($data['referenceNo'] ?? '-'),
            ]);
        });

        return response()->json(['responseCode' => '2000000', 'responseMessage' => 'Success']);
    }
}
