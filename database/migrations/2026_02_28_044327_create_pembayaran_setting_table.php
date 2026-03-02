<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id();
            $table->string('no_pembayaran')->unique();
            $table->foreignId('tagihan_id')->constrained('tagihan');
            $table->foreignId('pelanggan_id')->constrained('pelanggan');
            $table->integer('jumlah_bayar');
            $table->enum('metode', ['cash', 'transfer', 'midtrans', 'xendit']);
            $table->string('bukti_bayar')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        Schema::create('setting', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });

        Schema::create('wa_log', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pelanggan_id')->constrained('pelanggan');
            $table->string('no_tujuan');
            $table->enum('jenis', ['tagihan', 'jatuh_tempo', 'isolir', 'aktivasi', 'custom']);
            $table->text('pesan');
            $table->enum('status', ['pending', 'sent', 'failed'])->default('pending');
            $table->text('response')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wa_log');
        Schema::dropIfExists('setting');
        Schema::dropIfExists('pembayaran');
    }
};
