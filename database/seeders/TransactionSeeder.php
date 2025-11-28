<?php

namespace Database\Seeders;

use App\Models\CashFund;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    /**
     * Seed sample transactions untuk testing aplikasi.
     *
     * Seeder ini membuat:
     * - Cash fund awal untuk setiap user
     * - Sample transaksi dengan berbagai kategori
     */
    public function run(): void
    {
        $users = User::all();
        $categories = Category::where('is_default', true)->get();

        if ($users->isEmpty() || $categories->isEmpty()) {
            $this->command->warn('Please run UserSeeder and CategorySeeder first!');

            return;
        }

        // Sample transaction descriptions per category
        $transactionTemplates = [
            'Office Supplies' => [
                'Beli ATK kantor',
                'Kertas HVS A4',
                'Tinta printer',
                'Pulpen dan spidol',
                'Amplop dan materai',
                'Stapler dan isi staples',
            ],
            'Food & Beverages' => [
                'Snack meeting',
                'Makan siang tim',
                'Kopi dan teh kantor',
                'Air mineral galon',
                'Snack tamu',
                'Makan siang client',
            ],
            'Transportation' => [
                'Bensin motor operasional',
                'Parkir mall',
                'Ojek online ke client',
                'Toll ke lokasi meeting',
                'Bensin mobil kantor',
                'Parkir bandara',
            ],
            'Miscellaneous' => [
                'Service AC kantor',
                'Perbaikan kursi',
                'Lampu ruang meeting',
                'Alat kebersihan',
                'Obat P3K',
                'Baterai remote AC',
            ],
            'Other' => [
                'Biaya admin bank',
                'Fotocopy dokumen',
                'Jasa kurir ekspres',
                'Biaya notaris',
                'Sumbangan kegiatan RT',
                'Kado ulang tahun karyawan',
            ],
        ];

        foreach ($users as $user) {
            // Create initial cash fund (Rp 5.000.000 - Rp 10.000.000)
            CashFund::create([
                'amount' => fake()->numberBetween(5000000, 10000000),
                'note' => 'Dana awal kas kecil',
                'fund_date' => now()->subDays(30),
                'user_id' => $user->id,
            ]);

            // Create 8-15 sample transactions for each user
            $numTransactions = fake()->numberBetween(8, 15);

            for ($i = 0; $i < $numTransactions; $i++) {
                $category = $categories->random();
                $categoryName = $category->name;

                // Get random description for this category
                $descriptions = $transactionTemplates[$categoryName] ?? ['Pengeluaran lainnya'];
                $description = fake()->randomElement($descriptions);

                Transaction::create([
                    'amount' => fake()->randomElement([
                        fake()->numberBetween(10000, 50000),
                        fake()->numberBetween(50000, 150000),
                        fake()->numberBetween(150000, 500000),
                    ]),
                    'description' => $description,
                    'transaction_date' => fake()->dateTimeBetween('-30 days', 'now'),
                    'category_id' => $category->id,
                    'user_id' => $user->id,
                ]);
            }
        }

        $totalTransactions = Transaction::count();
        $totalFunds = CashFund::count();

        $this->command->info('Transactions seeded successfully!');
        $this->command->info("- Cash funds created: {$totalFunds}");
        $this->command->info("- Transactions created: {$totalTransactions}");
    }
}
