<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('pelanggan', function (Blueprint $table) {
            $table->foreignId('router_id')->nullable()->after('paket_id')->constrained('routers')->nullOnDelete();
            $table->string('password_pppoe')->nullable()->after('password');
        });
    }

    public function down(): void
    {
        Schema::table('pelanggan', function (Blueprint $table) {
            $table->dropForeign(['router_id']);
            $table->dropColumn(['router_id', 'password_pppoe']);
        });
    }
};
