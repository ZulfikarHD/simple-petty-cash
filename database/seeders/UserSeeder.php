<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Seed sample users untuk testing aplikasi.
     *
     * Format login:
     * - Email: {username}@test.com
     * - Password: {username}123
     *
     * Contoh:
     * - admin@test.com / admin123 (Admin)
     * - budi@test.com / budi123 (User biasa)
     */
    public function run(): void
    {
        // Admin user
        User::factory()
            ->withUsername('admin')
            ->admin()
            ->create();

        // Regular users
        $users = ['budi', 'siti', 'andi', 'dewi'];

        foreach ($users as $username) {
            User::factory()
                ->withUsername($username)
                ->create();
        }

        $this->command->info('Users seeded successfully!');
        $this->command->table(
            ['Name', 'Email', 'Password', 'Role'],
            [
                ['Admin', 'admin@test.com', 'admin123', 'Admin'],
                ['Budi', 'budi@test.com', 'budi123', 'User'],
                ['Siti', 'siti@test.com', 'siti123', 'User'],
                ['Andi', 'andi@test.com', 'andi123', 'User'],
                ['Dewi', 'dewi@test.com', 'dewi123', 'User'],
            ]
        );
    }
}
