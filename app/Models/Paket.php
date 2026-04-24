<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Paket extends Model
{
    protected $table = 'paket';

    protected $fillable = [
        'nama_paket', 'harga', 'kecepatan_download',
        'kecepatan_upload', 'radius_profile', 'masa_aktif',
        'jenis', 'deskripsi', 'is_active',
        'burst_limit_download', 'burst_limit_upload',
        'burst_threshold_download', 'burst_threshold_upload',
        'burst_time',
    ];

    public function pelanggan()
    {
        return $this->hasMany(Pelanggan::class);
    }
}
