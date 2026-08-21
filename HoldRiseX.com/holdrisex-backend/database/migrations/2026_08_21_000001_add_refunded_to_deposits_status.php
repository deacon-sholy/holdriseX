<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            DB::statement("ALTER TABLE deposits RENAME COLUMN status TO status_old");
            DB::statement("ALTER TABLE deposits ADD COLUMN status TEXT DEFAULT 'pending' CHECK (status IN ('pending','completed','failed','refunded'))");
            DB::statement("UPDATE deposits SET status = status_old");
            DB::statement("ALTER TABLE deposits DROP COLUMN status_old");
        } else {
            DB::statement("ALTER TABLE deposits MODIFY COLUMN status ENUM('pending','completed','failed','refunded') DEFAULT 'pending'");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            DB::statement("UPDATE deposits SET status = 'failed' WHERE status = 'refunded'");
            DB::statement("ALTER TABLE deposits RENAME COLUMN status TO status_old");
            DB::statement("ALTER TABLE deposits ADD COLUMN status ENUM('pending','completed','failed') DEFAULT 'pending'");
            DB::statement("UPDATE deposits SET status = status_old");
            DB::statement("ALTER TABLE deposits DROP COLUMN status_old");
        } else {
            DB::statement("UPDATE deposits SET status = 'failed' WHERE status = 'refunded'");
            DB::statement("ALTER TABLE deposits MODIFY COLUMN status ENUM('pending','completed','failed') DEFAULT 'pending'");
        }
    }
};
