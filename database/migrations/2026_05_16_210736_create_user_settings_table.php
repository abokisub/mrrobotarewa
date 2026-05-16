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
        Schema::create('user_settings', function (Blueprint $table) {
            $table->id();
            $table->decimal('risk_percentage', 5, 2)->default(10.00); // e.g. 10%
            $table->decimal('default_leverage', 5, 2)->default(10.00);
            $table->string('strategy_mode')->default('hybrid'); // hybrid, aggressive, conservative
            $table->boolean('auto_trading_enabled')->default(true);
            $table->decimal('daily_loss_limit', 5, 2)->default(3.00); // Pause if 3% loss
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_settings');
    }
};
