<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pelanggan', function (Blueprint $table) {
            $table->id();
            $table->string('id_pelanggan')->unique();
            $table->string('nama');
            $table->string('username')->unique();
            $table->string('password');
            $table->string('no_hp')->nullable();
            $table->string('email')->nullable();
            $table->text('alamat')->nullable();
            $table->foreignId('paket_id')->constrained('paket');
            $table->date('tgl_daftar');
            $table->date('tgl_expired')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('router_name')->nullable();
            $table->enum('status', ['aktif', 'suspend', 'isolir', 'nonaktif'])->default('aktif');
            $table->enum('jenis_layanan', ['pppoe', 'hotspot'])->default('pppoe');
            $table->string('wilayah')->nullable();
            $table->string('pin')->nullable();
            $table->string('fcm_token')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pelanggan');
    }
};
