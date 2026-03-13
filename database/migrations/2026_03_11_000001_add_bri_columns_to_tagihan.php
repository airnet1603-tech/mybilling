<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tagihan', function (Blueprint $table) {
            DB::statement("ALTER TABLE tagihan MODIFY COLUMN metode_bayar ENUM('cash','transfer','midtrans','xendit','bri_qris','bri_va') NULL");
            $table->string('bri_qris_data', 2000)->nullable()->after('payment_url');
            $table->string('bri_va_number', 20)->nullable()->after('bri_qris_data');
            $table->string('bri_ref_no', 100)->nullable()->after('bri_va_number');
            $table->timestamp('bri_expired_at')->nullable()->after('bri_ref_no');
        });

        Schema::table('pembayaran', function (Blueprint $table) {
            DB::statement("ALTER TABLE pembayaran MODIFY COLUMN metode ENUM('cash','transfer','midtrans','xendit','bri_qris','bri_va') NOT NULL");
        });
    }

    public function down(): void
    {
        Schema::table('tagihan', function (Blueprint $table) {
            $table->dropColumn(['bri_qris_data', 'bri_va_number', 'bri_ref_no', 'bri_expired_at']);
            DB::statement("ALTER TABLE tagihan MODIFY COLUMN metode_bayar ENUM('cash','transfer','midtrans','xendit') NULL");
        });
        Schema::table('pembayaran', function (Blueprint $table) {
            DB::statement("ALTER TABLE pembayaran MODIFY COLUMN metode ENUM('cash','transfer','midtrans','xendit') NOT NULL");
        });
    }
};
