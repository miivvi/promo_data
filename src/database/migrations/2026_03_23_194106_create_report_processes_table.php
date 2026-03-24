<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('report_process', function (Blueprint $table) {
            $table->id('rp_id');
            $table->string('rp_pid')->nullable();
            $table->dateTime('rp_start_datetime')->nullable();
            $table->decimal('rp_exec_time', 10, 2)->nullable();
            $table->tinyInteger('ps_id')->unsigned()->nullable();
            $table->string('rp_file_save_path')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_process');
    }
};
