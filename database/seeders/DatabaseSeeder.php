<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Menjalankan semua seeder untuk mengisi database dengan data sample.
     *
     * Urutan seeder:
     * 1. CategorySeeder - Kategori default (Office Supplies, Food, Transportation, etc.)
     * 2. UserSeeder - Sample users (admin dan regular users)
     * 3. TransactionSeeder - Sample transaksi dan cash funds
     *
     * Login credentials:
     * - Admin: admin@test.com / admin123
     * - User: budi@test.com / budi123, siti@test.com / siti123, etc.
     */
    public function run(): void
    {
        $this->call([
            CategorySeeder::class,
            UserSeeder::class,
            TransactionSeeder::class,
        ]);
    }
}
