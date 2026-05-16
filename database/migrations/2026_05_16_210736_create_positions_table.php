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
        Schema::create('positions', function (Blueprint $table) {
            $table->id();
            $table->string('symbol');
            $table->string('side'); // Buy / Sell
            $table->decimal('size', 20, 8);
            $table->decimal('entry_price', 20, 8);
            $table->decimal('mark_price', 20, 8)->nullable();
            $table->decimal('liquidation_price', 20, 8)->nullable();
            $table->decimal('unrealized_pnl', 20, 8)->nullable();
            $table->decimal('leverage', 8, 2)->default(1);
            $table->decimal('margin_used', 20, 8)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('positions');
    }
};
