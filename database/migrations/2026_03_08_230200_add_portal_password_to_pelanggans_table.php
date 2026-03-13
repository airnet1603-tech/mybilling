<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::table('pelanggan', function (Blueprint $table) {
            $table->string('portal_password')->nullable()->after('password_pppoe');
        });
    }
    public function down(): void {
        Schema::table('pelanggan', function (Blueprint $table) { $table->dropColumn('portal_password'); });
    }
};
