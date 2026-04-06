<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('routers', function (Blueprint $table) {
            $table->boolean('use_wireguard')->default(false);
            $table->string('wg_public_key')->nullable();
            $table->string('wg_private_key')->nullable();
            $table->string('wg_ip')->nullable();
            $table->integer('wg_port')->default(51820);
        });
    }

    public function down(): void
    {
        Schema::table('routers', function (Blueprint $table) {
            $table->dropColumn(['use_wireguard','wg_public_key','wg_private_key','wg_ip','wg_port']);
        });
    }
};
