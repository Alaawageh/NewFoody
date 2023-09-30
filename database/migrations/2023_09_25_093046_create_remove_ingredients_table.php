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
        Schema::create('remove_ingredients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_product_id')->constrained()->onDelete('cascade');
            $table->foreignId('ingredient_id')->constrained()->onDelete('cascade');
            // $table->bigInteger('product_ingredient_id')->unsigned();
            // $table->foreign('product_ingredient_id')->references('id')->on('product_ingredient')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('remove_ingredients');
    }
};
