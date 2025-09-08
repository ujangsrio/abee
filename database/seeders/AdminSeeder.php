<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Admin;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin 1
        if (!User::where('email', 'admin@example.com')->exists()) {
            $user1 = User::create([
                'name' => 'Admin Utama',
                'email' => 'admin@example.com',
                'password' => Hash::make('password123'),
                'role' => 'admin',
            ]);

            Admin::create([
                'user_id' => $user1->id,
                'name' => $user1->name,
            ]);
        }

        // Admin 2
        if (!User::where('email', 'admin2@example.com')->exists()) {
            $user2 = User::create([
                'name' => 'Admin Kedua',
                'email' => 'admin2@example.com',
                'password' => Hash::make('admin234'),
                'role' => 'admin',
            ]);

            Admin::create([
                'user_id' => $user2->id,
                'name' => $user2->name,
            ]);
        }
    }

}
