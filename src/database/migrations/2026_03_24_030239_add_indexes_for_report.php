<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::table('price', function (Blueprint $table) {
            $table->index(['product_id', 'price_date'], 'price_product_date_idx');
        });

        Schema::table('product', function (Blueprint $table) {
            $table->index('category_id', 'product_category_idx');
        });

        Schema::table('report_process', function (Blueprint $table) {
            $table->index('ps_id', 'report_process_status_idx');
        });
    }

    public function down(): void
    {
        Schema::table('price', function (Blueprint $table) {
            $table->dropIndex('price_product_date_idx');
        });

        Schema::table('product', function (Blueprint $table) {
            $table->dropIndex('product_category_idx');
        });

        Schema::table('report_process', function (Blueprint $table) {
            $table->dropIndex('report_process_status_idx');
        });
    }
};
