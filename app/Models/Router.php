<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Router extends Model
{
    protected $fillable = [
        'nama', 'ip_address', 'port', 'username', 'password', 'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function pelanggan()
    {
        return $this->hasMany(Pelanggan::class);
    }
}
