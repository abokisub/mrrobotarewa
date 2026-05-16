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
        Schema::create('daily_statistics', function (Blueprint $table) {
            $table->id();
            $table->date('date')->unique();
            $table->integer('total_trades')->default(0);
            $table->integer('wins')->default(0);
            $table->integer('losses')->default(0);
            $table->decimal('daily_pnl', 20, 8)->default(0);
            $table->decimal('drawdown_percentage', 5, 2)->default(0);
            $table->decimal('volume_traded', 20, 8)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_statistics');
    }
};
