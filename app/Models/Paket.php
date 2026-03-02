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
    ];

    public function pelanggan()
    {
        return $this->hasMany(Pelanggan::class);
    }
}
