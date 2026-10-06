<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@financetracker.local'],
            [
                'name'      => 'Administrator',
                'password'  => Hash::make('Admin12345'),
                'role'      => UserRole::Admin,
                'is_active' => true,
            ]
        );

        // Make sure existing row is active + admin
        $admin->update(['is_active' => true, 'role' => UserRole::Admin]);

        Profile::firstOrCreate(
            ['user_id' => $admin->id],
            [
                'full_name'   => 'System Administrator',
                'currency'    => 'PKR',
                'timezone'    => 'Asia/Karachi',
                'date_format' => 'd/m/Y',
            ]
        );

        // Normal user
        $user = User::firstOrCreate(
            ['email' => 'user@financetracker.local'],
            [
                'name'      => 'Demo User',
                'password'  => Hash::make('User12345'),
                'role'      => UserRole::User,
                'is_active' => true,
            ]
        );

        $user->update(['is_active' => true, 'role' => UserRole::User]);

        Profile::firstOrCreate(
            ['user_id' => $user->id],
            [
                'full_name'   => 'Demo User',
                'currency'    => 'PKR',
                'timezone'    => 'Asia/Karachi',
                'date_format' => 'd/m/Y',
            ]
        );
    }
}