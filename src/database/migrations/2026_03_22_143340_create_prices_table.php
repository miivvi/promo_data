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
        Schema::create('price', function (Blueprint $table) {
            $table->id('price_id');
            $table->foreignId('product_id')
                ->constrained(table: 'product', column:'product_id');
            $table->decimal('price', 12, 6)->nullable(false);
            $table->dateTime('price_date')->nullable(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('price');
    }
};
