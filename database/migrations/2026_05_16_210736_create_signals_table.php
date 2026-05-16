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
        Schema::create('signals', function (Blueprint $table) {
            $table->id();
            $table->string('symbol');
            $table->string('signal_type'); // BUY, SELL, WAIT
            $table->decimal('rsi_value', 8, 4)->nullable();
            $table->decimal('macd_value', 20, 8)->nullable();
            $table->integer('confidence_score')->default(0);
            $table->string('market_condition')->nullable(); // e.g. TRENDING, RANGING
            $table->text('reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('signals');
    }
};
