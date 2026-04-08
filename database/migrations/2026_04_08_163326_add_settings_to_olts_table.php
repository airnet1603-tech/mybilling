<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('olts', function (Blueprint $table) {
            $table->string('snmp_community')->default('public')->after('password');
            $table->string('api_endpoint')->default('/onuAllPonOnuList.asp')->after('snmp_community');
            $table->integer('sync_interval')->default(60)->after('api_endpoint')->comment('menit');
        });
    }

    public function down(): void
    {
        Schema::table('olts', function (Blueprint $table) {
            $table->dropColumn(['snmp_community', 'api_endpoint', 'sync_interval']);
        });
    }
};
