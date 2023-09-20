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
        Schema::create('order_product_extra_ingredient', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_product_id')->constrained()->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('extra_ingredient_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_product_extra_ingredient');
    }
};
