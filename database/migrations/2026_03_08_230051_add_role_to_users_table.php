<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['superadmin', 'admin', 'operator'])->default('operator')->after('name');
        });
        \DB::table('users')->where('id', 1)->update(['role' => 'admin']);
    }
    public function down(): void {
        Schema::table('users', function (Blueprint $table) { $table->dropColumn('role'); });
    }
};
