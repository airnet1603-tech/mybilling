<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1. Fix pembayaran foreign keys
        Schema::table('pembayaran', function (Blueprint $table) {
            $table->dropForeign(['pelanggan_id']);
            $table->dropForeign(['tagihan_id']);

            $table->foreign('pelanggan_id')
                  ->references('id')->on('pelanggan')
                  ->onDelete('cascade');

            $table->foreign('tagihan_id')
                  ->references('id')->on('tagihan')
                  ->onDelete('cascade');
        });

        // 2. Fix tagihan foreign keys
        Schema::table('tagihan', function (Blueprint $table) {
            $table->dropForeign(['pelanggan_id']);
            $table->dropForeign(['paket_id']);

            $table->foreign('pelanggan_id')
                  ->references('id')->on('pelanggan')
                  ->onDelete('cascade');

            $table->foreign('paket_id')
                  ->references('id')->on('paket')
                  ->onDelete('cascade');
        });

        // 3. Fix pelanggan foreign key
        Schema::table('pelanggan', function (Blueprint $table) {
            $table->dropForeign(['paket_id']);

            $table->foreign('paket_id')
                  ->references('id')->on('paket')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('pembayaran', function (Blueprint $table) {
            $table->dropForeign(['pelanggan_id']);
            $table->dropForeign(['tagihan_id']);
            $table->foreign('pelanggan_id')->references('id')->on('pelanggan');
            $table->foreign('tagihan_id')->references('id')->on('tagihan');
        });

        Schema::table('tagihan', function (Blueprint $table) {
            $table->dropForeign(['pelanggan_id']);
            $table->dropForeign(['paket_id']);
            $table->foreign('pelanggan_id')->references('id')->on('pelanggan');
            $table->foreign('paket_id')->references('id')->on('paket');
        });

        Schema::table('pelanggan', function (Blueprint $table) {
            $table->dropForeign(['paket_id']);
            $table->foreign('paket_id')->references('id')->on('paket');
        });
    }
};
