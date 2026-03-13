<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Tagihan extends Model
{
    protected $table = 'tagihan';
    protected $fillable = [
        'no_tagihan', 'pelanggan_id', 'paket_id',
        'jumlah', 'denda', 'diskon', 'total',
        'periode_bulan', 'tgl_tagihan', 'tgl_jatuh_tempo',
        'tgl_bayar', 'status', 'metode_bayar',
        'payment_url', 'catatan',
    ];
    protected $casts = [
        'periode_bulan'   => 'date',
        'tgl_tagihan'     => 'date',
        'tgl_jatuh_tempo' => 'date',
        'tgl_bayar'       => 'date',
    ];
    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class);
    }
    public function pembayaran()
    {
        return $this->hasMany(Pembayaran::class, 'tagihan_id');
    }
    public function paket()
    {
        return $this->belongsTo(Paket::class);
    }
    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }
    public static function generateNomor(): string
    {
        $prefix = 'INV-' . now()->format('Ym') . '-';
        $last   = self::whereMonth('created_at', now()->month)
                      ->whereYear('created_at', now()->year)
                      ->count() + 1;
        return $prefix . str_pad($last, 4, '0', STR_PAD_LEFT);
    }
}
