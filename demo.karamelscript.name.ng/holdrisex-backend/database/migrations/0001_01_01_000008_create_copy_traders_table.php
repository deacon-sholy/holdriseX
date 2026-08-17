<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('copy_traders', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('specialty', ['forex', 'crypto', 'mixed']);
            $table->decimal('win_rate', 5, 2);
            $table->decimal('monthly_return', 5, 2);
            $table->integer('total_followers')->default(0);
            $table->decimal('aum', 15, 2)->default(0);
            $table->enum('risk_level', ['low', 'medium', 'high']);
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('copy_traders');
    }
};
