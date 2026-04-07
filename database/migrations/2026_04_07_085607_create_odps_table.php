<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('odps', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['ODC','ODP'])->default('ODP');
            $table->decimal('lat', 10, 8)->nullable();
            $table->decimal('lng', 11, 8)->nullable();
            $table->foreignId('olt_id')->constrained('olts')->cascadeOnDelete();
            $table->integer('kapasitas')->default(8);
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('odps'); }
};
