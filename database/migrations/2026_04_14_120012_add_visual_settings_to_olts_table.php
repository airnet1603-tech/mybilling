<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('olts', function (Blueprint $table) {
            // Warna & ikon OLT
            $table->string('olt_color')->default('#dc3545')->after('model');
            $table->string('olt_icon')->default('dot')->after('olt_color');
            // Warna & ikon SFP (default untuk semua SFP milik OLT ini)
            $table->string('sfp_color')->default('#fd7e14')->after('olt_icon');
            $table->string('sfp_icon')->default('dot')->after('sfp_color');
            // Warna & ikon ODC
            $table->string('odc_color')->default('#6f42c1')->after('sfp_icon');
            $table->string('odc_icon')->default('dot')->after('odc_color');
            // Warna & ikon ODP
            $table->string('odp_color')->default('#fd7e14')->after('odc_icon');
            $table->string('odp_icon')->default('dot')->after('odp_color');
            // Warna garis
            $table->string('line_olt_odc')->default('#6f42c1')->after('odp_icon');
            $table->string('line_odc_odp')->default('#fd7e14')->after('line_olt_odc');
            $table->string('line_odp_odp')->default('#28a745')->after('line_odc_odp');
        });
    }
    public function down(): void {
        Schema::table('olts', function (Blueprint $table) {
            $table->dropColumn([
                'olt_color','olt_icon',
                'sfp_color','sfp_icon',
                'odc_color','odc_icon',
                'odp_color','odp_icon',
                'line_olt_odc','line_odc_odp','line_odp_odp',
            ]);
        });
    }
};
