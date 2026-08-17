<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content');
            $table->enum('type', ['info', 'warning', 'maintenance', 'promotion']);
            $table->enum('target_audience', ['all', 'specific_group']);
            $table->enum('status', ['published', 'draft', 'scheduled'])->default('draft');
            $table->timestamp('scheduled_at')->nullable();
            $table->integer('views_count')->default(0);
            $table->foreignId('admin_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};
