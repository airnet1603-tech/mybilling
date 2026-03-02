<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tagihan', function (Blueprint $table) {
            $table->id();
            $table->string('no_tagihan')->unique();
            $table->foreignId('pelanggan_id')->constrained('pelanggan');
            $table->foreignId('paket_id')->constrained('paket');
            $table->integer('jumlah');
            $table->integer('denda')->default(0);
            $table->integer('diskon')->default(0);
            $table->integer('total');
            $table->date('periode_bulan');
            $table->date('tgl_tagihan');
            $table->date('tgl_jatuh_tempo');
            $table->date('tgl_bayar')->nullable();
            $table->enum('status', ['unpaid', 'paid', 'overdue', 'cancelled'])->default('unpaid');
            $table->enum('metode_bayar', ['cash', 'transfer', 'midtrans', 'xendit'])->nullable();
            $table->string('payment_url')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tagihan');
    }
};
