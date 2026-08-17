<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('symbol');
            $table->enum('type', ['buy', 'sell']);
            $table->enum('asset_type', ['forex', 'crypto', 'stocks', 'commodities']);
            $table->decimal('entry_price', 15, 6);
            $table->decimal('current_price', 15, 6)->nullable();
            $table->decimal('lot_size', 10, 4);
            $table->decimal('pnl', 15, 2)->default(0);
            $table->enum('status', ['open', 'closed', 'pending'])->default('open');
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trades');
    }
};
