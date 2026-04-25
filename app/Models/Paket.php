<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Paket extends Model
{
    protected $table = 'paket';

    protected $fillable = [
        'router_id',
        'nama_paket',
        'harga',
        'kecepatan_download',
        'kecepatan_upload',
        'radius_profile',
        'masa_aktif',
        'jenis',
        'deskripsi',
        'is_active',
        'burst_limit_download',
        'burst_limit_upload',
        'burst_threshold_download',
        'burst_threshold_upload',
        'burst_time',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function router()
    {
        return $this->belongsTo(Router::class);
    }

    public function pelanggan()
    {
        return $this->hasMany(Pelanggan::class);
    }
}
