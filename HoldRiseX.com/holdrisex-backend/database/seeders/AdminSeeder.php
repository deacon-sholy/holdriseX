<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            'name' => 'Admin',
            'email' => 'admin@holdrisex.com',
            'password' => Hash::make('123456789'),
            'role' => 'admin',
            'is_active' => true,
            'balance' => 0,
            'country' => 'US',
            'kyc_status' => 'verified',
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
