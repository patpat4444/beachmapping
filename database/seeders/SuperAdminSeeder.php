<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@dagattabai.com',
            'password' => Hash::make('password123'),
            'pin' => '419154',
            'role' => 'super_admin',
            'is_active' => true,
        ]);

        $this->command->info('========================================');
        $this->command->info('Super Admin created successfully!');
        $this->command->info('========================================');
        $this->command->info('PIN: 419154');
        $this->command->info('Login: /admin/login');
        $this->command->info('========================================');
    }
}
