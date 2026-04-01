<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Pembayaran extends Model
{
    protected $table = 'pembayaran';
    protected $fillable = [
        'no_pembayaran', 'tagihan_id', 'pelanggan_id',
        'jumlah_bayar', 'metode', 'bukti_bayar', 'catatan', 'created_by',
    ];
    public function tagihan()
    {
        return $this->belongsTo(Tagihan::class);
    }
    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class);
    }
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
