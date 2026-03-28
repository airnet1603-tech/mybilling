<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Pelanggan extends Authenticatable
{
    use HasApiTokens, SoftDeletes;

    protected $table = 'pelanggan';

    protected $fillable = [
        'id_pelanggan', 'nama', 'username', 'password',
        'no_hp', 'email', 'alamat', 'paket_id',
        'tgl_daftar', 'tgl_expired', 'ip_address',
        'router_name', 'status', 'jenis_layanan',
        'wilayah',
        'latitude',
        'longitude', 'pin', 'fcm_token',
        'router_id', 'password_pppoe', 'maps',
    ];

    protected $hidden = ['password', 'pin'];

    protected $casts = [
        'tgl_daftar'  => 'date',
        'tgl_expired' => 'date',
    ];

    public function paket()
    {
        return $this->belongsTo(Paket::class);
    }

    public function router()
    {
        return $this->belongsTo(\App\Models\Router::class);
    }

    public function tagihan()
    {
        return $this->hasMany(Tagihan::class);
    }

    public function pembayaran()
    {
        return $this->hasMany(Pembayaran::class);
    }
}
