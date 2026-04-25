<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('paket', function (Blueprint $table) {
            if (!Schema::hasColumn('paket', 'burst_limit_download'))     $table->integer('burst_limit_download')->default(0)->after('kecepatan_upload');
            if (!Schema::hasColumn('paket', 'burst_limit_upload'))       $table->integer('burst_limit_upload')->default(0)->after('burst_limit_download');
            if (!Schema::hasColumn('paket', 'burst_threshold_download')) $table->integer('burst_threshold_download')->default(0)->after('burst_limit_upload');
            if (!Schema::hasColumn('paket', 'burst_threshold_upload'))   $table->integer('burst_threshold_upload')->default(0)->after('burst_threshold_download');
            if (!Schema::hasColumn('paket', 'burst_time'))               $table->integer('burst_time')->default(8)->after('burst_threshold_upload');
        });
    }

    public function down(): void
    {
        Schema::table('paket', function (Blueprint $table) {
            $table->dropColumn([
                'burst_limit_download',
                'burst_limit_upload',
                'burst_threshold_download',
                'burst_threshold_upload',
                'burst_time',
            ]);
        });
    }
};
