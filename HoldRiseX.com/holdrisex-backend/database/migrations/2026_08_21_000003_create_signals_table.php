<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('signals', function (Blueprint $table) {
            $table->id();
            $table->string('symbol');
            $table->enum('direction', ['buy', 'sell']);
            $table->decimal('entry_price', 12, 2);
            $table->decimal('take_profit', 12, 2)->nullable();
            $table->decimal('stop_loss', 12, 2)->nullable();
            $table->enum('status', ['active', 'closed', 'pending', 'expired'])->default('active');
            $table->decimal('risk_reward', 4, 2)->nullable();
            $table->text('analysis')->nullable();
            $table->string('analyst')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('signals');
    }
};
