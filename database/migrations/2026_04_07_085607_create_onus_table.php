<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('onus', function (Blueprint $table) {
            $table->id();
            $table->string('onu_id');
            $table->string('name')->nullable();
            $table->string('mac_address');
            $table->enum('status', ['Up','Down'])->default('Down');
            $table->string('distance')->nullable();
            $table->string('port')->nullable();
            $table->foreignId('olt_id')->constrained('olts')->cascadeOnDelete();
            $table->foreignId('odp_id')->nullable()->constrained('odps')->nullOnDelete();
            $table->foreignId('pelanggan_id')->nullable()->constrained('pelanggan')->nullOnDelete();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('onus'); }
};
