<?php

use App\Types\OrderStatus;
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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('status')->default(OrderStatus::BEFOR_PREPARING);
            $table->double('total_price')->default(0000);
            $table->boolean('is_paid')->default(0);
            $table->boolean('is_update')->default(0);
            $table->time('time')->nullable();
            $table->time('time_start')->nullable();
            $table->time('time_end')->nullable();
            $table->time('time_Waiter')->nullable();
            $table->foreignId('table_id')->constrained()->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('branch_id')->constrained()->onDelete('cascade')->onUpdate('cascade');
            $table->integer('serviceRate')->nullable();
            $table->text('feedback')->nullable();
            $table->string('author')->nullable();
            $table->time('estimatedForOrder')->nullable();
            $table->foreignId('bill_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
