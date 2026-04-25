<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('paket', function (Blueprint $table) {
            if (!Schema::hasColumn('paket', 'router_id')) {
                $table->foreignId('router_id')->nullable()->constrained('routers')->nullOnDelete()->after('id');
            }
        });
    }
    public function down(): void
    {
        Schema::table('paket', function (Blueprint $table) {
            $table->dropForeign(['router_id']);
            $table->dropColumn('router_id');
        });
    }
};
