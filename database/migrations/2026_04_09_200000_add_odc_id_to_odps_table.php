<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('odps', function (Blueprint $table) {
            // odc_id nullable: ODP bisa langsung ke OLT atau lewat ODC
            $table->unsignedBigInteger('odc_id')->nullable()->after('olt_id');
            $table->foreign('odc_id')->references('id')->on('odps')->nullOnDelete();
        });
    }

    public function down(): void {
        Schema::table('odps', function (Blueprint $table) {
            $table->dropForeign(['odc_id']);
            $table->dropColumn('odc_id');
        });
    }
};
