<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('odps', function (Blueprint $table) {
            $table->foreignId('sfp_id')->nullable()->after('olt_id')->constrained('sfps')->nullOnDelete();
        });
    }
    public function down(): void {
        Schema::table('odps', function (Blueprint $table) {
            $table->dropForeign(['sfp_id']);
            $table->dropColumn('sfp_id');
        });
    }
};
