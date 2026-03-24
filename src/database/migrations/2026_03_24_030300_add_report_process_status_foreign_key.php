<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::table('report_process', function (Blueprint $table) {
            $table->foreign('ps_id')
                ->references('ps_id')
                ->on('process_status')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('report_process', function (Blueprint $table) {
            $table->dropForeign(['ps_id']);
        });
    }
};
