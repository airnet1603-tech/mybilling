<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('sfps', function (Blueprint $table) {
            $table->string('color')->default('#fd7e14')->after('keterangan');
            $table->string('icon')->default('dot')->after('color');
        });
    }
    public function down(): void {
        Schema::table('sfps', function (Blueprint $table) {
            $table->dropColumn(['color','icon']);
        });
    }
};
