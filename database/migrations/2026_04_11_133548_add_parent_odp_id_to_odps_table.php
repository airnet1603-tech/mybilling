<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('odps', function (Blueprint $table) {
            $table->unsignedBigInteger('parent_odp_id')->nullable()->after('odc_id');
            $table->foreign('parent_odp_id')->references('id')->on('odps')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('odps', function (Blueprint $table) {
            $table->dropForeign(['parent_odp_id']);
            $table->dropColumn('parent_odp_id');
        });
    }
};
