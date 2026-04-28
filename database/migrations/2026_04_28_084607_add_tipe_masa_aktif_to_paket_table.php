<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('paket', function (Blueprint $table) {
            $table->enum('tipe_masa_aktif', ['hari', 'minggu', 'bulan', 'tahun'])->default('hari')->after('masa_aktif');
        });
    }

    public function down(): void
    {
        Schema::table('paket', function (Blueprint $table) {
            $table->dropColumn('tipe_masa_aktif');
        });
    }
};
