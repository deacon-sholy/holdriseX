<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['name' => 'John Carter', 'email' => 'john.carter@gmail.com', 'country' => 'US', 'balance' => 12500.00, 'kyc_status' => 'verified', 'is_active' => true],
            ['name' => 'Emma Wilson', 'email' => 'emma.wilson@outlook.com', 'country' => 'UK', 'balance' => 8750.50, 'kyc_status' => 'verified', 'is_active' => true],
            ['name' => 'Chidi Okonkwo', 'email' => 'chidi.okonkwo@yahoo.com', 'country' => 'Nigeria', 'balance' => 3200.00, 'kyc_status' => 'verified', 'is_active' => true],
            ['name' => 'Hans Mueller', 'email' => 'hans.mueller@web.de', 'country' => 'Germany', 'balance' => 45000.00, 'kyc_status' => 'verified', 'is_active' => true],
            ['name' => 'Ahmed Al-Rashid', 'email' => 'ahmed.rashid@outlook.com', 'country' => 'UAE', 'balance' => 28000.00, 'kyc_status' => 'verified', 'is_active' => true],
            ['name' => 'Priya Sharma', 'email' => 'priya.sharma@gmail.com', 'country' => 'India', 'balance' => 1800.00, 'kyc_status' => 'pending', 'is_active' => true],
            ['name' => 'Lucas Fernandes', 'email' => 'lucas.fernandes@gmail.com', 'country' => 'Brazil', 'balance' => 5500.00, 'kyc_status' => 'verified', 'is_active' => true],
            ['name' => 'Yuki Tanaka', 'email' => 'yuki.tanaka@icloud.com', 'country' => 'Japan', 'balance' => 35000.00, 'kyc_status' => 'verified', 'is_active' => true],
            ['name' => 'Min-Jun Park', 'email' => 'minjun.park@naver.com', 'country' => 'South Korea', 'balance' => 18500.00, 'kyc_status' => 'verified', 'is_active' => false],
            ['name' => 'Sophie Laurent', 'email' => 'sophie.laurent@orange.fr', 'country' => 'France', 'balance' => 22000.00, 'kyc_status' => 'verified', 'is_active' => true],
            ['name' => 'James Thompson', 'email' => 'james.thompson@proton.me', 'country' => 'Canada', 'balance' => 980.00, 'kyc_status' => 'none', 'is_active' => true],
            ['name' => 'Olivia Brown', 'email' => 'olivia.brown@gmail.com', 'country' => 'Australia', 'balance' => 15200.00, 'kyc_status' => 'verified', 'is_active' => true],
            ['name' => 'Daniel Rossi', 'email' => 'daniel.rossi@libero.it', 'country' => 'France', 'balance' => 7200.00, 'kyc_status' => 'rejected', 'is_active' => false],
            ['name' => 'Fatima Al-Zahra', 'email' => 'fatima.zahra@gmail.com', 'country' => 'UAE', 'balance' => 50000.00, 'kyc_status' => 'verified', 'is_active' => true],
            ['name' => 'Michael Johnson', 'email' => 'michael.j@outlook.com', 'country' => 'US', 'balance' => 2500.00, 'kyc_status' => 'pending', 'is_active' => true],
            ['name' => 'Aisha Bello', 'email' => 'aisha.bello@yahoo.com', 'country' => 'Nigeria', 'balance' => 4500.00, 'kyc_status' => 'verified', 'is_active' => true],
            ['name' => 'Kenji Watanabe', 'email' => 'kenji.watanabe@gmail.com', 'country' => 'Japan', 'balance' => 62000.00, 'kyc_status' => 'verified', 'is_active' => true],
            ['name' => 'Maria Garcia', 'email' => 'maria.garcia@gmail.com', 'country' => 'Brazil', 'balance' => 1200.00, 'kyc_status' => 'none', 'is_active' => true],
            ['name' => 'Liam O\'Brien', 'email' => 'liam.obrien@outlook.com', 'country' => 'UK', 'balance' => 28000.00, 'kyc_status' => 'verified', 'is_active' => true],
            ['name' => 'Ananya Patel', 'email' => 'ananya.patel@proton.me', 'country' => 'India', 'balance' => 500.00, 'kyc_status' => 'pending', 'is_active' => true],
        ];

        $now = now();

        foreach ($users as $index => $user) {
            DB::table('users')->insert([
                'name' => $user['name'],
                'email' => $user['email'],
                'password' => Hash::make('password'),
                'country' => $user['country'],
                'balance' => $user['balance'],
                'kyc_status' => $user['kyc_status'],
                'is_active' => $user['is_active'],
                'role' => 'user',
                'email_verified_at' => $now,
                'last_login_at' => $now->subDays(rand(0, 14))->subHours(rand(0, 23)),
                'created_at' => $now->subDays(rand(30, 90)),
                'updated_at' => $now,
            ]);
        }
    }
}
