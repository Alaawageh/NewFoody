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
        Schema::create('extra_ingredients', function (Blueprint $table) {
            $table->id();
            $table->string('quantity')->nullable();
            $table->double('price_per_piece');
            $table->unsignedBigInteger('repo_id');
            $table->timestamps();
            $table->foreign('repo_id')->references('id')->on('repos')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('extra_ingredients');
    }
};
