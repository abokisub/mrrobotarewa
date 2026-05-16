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
        Schema::create('trades', function (Blueprint $table) {
            $table->id();
            $table->string('symbol');
            $table->string('side'); // Buy or Sell
            $table->decimal('leverage', 8, 2)->default(1);
            $table->decimal('entry_price', 20, 8);
            $table->decimal('take_profit', 20, 8)->nullable();
            $table->decimal('stop_loss', 20, 8)->nullable();
            $table->decimal('quantity', 20, 8);
            $table->decimal('pnl', 20, 8)->nullable(); // Profit and Loss
            $table->decimal('fees', 20, 8)->nullable();
            $table->string('status')->default('OPEN'); // OPEN, CLOSED
            $table->string('strategy_used')->nullable();
            $table->string('exchange_order_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trades');
    }
};
