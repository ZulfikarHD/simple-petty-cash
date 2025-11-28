<?php

namespace App\Services;

use App\Models\CashFund;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportService
{
    /**
     * Generate laporan pengeluaran berdasarkan filter yang diberikan.
     *
     * @param  array{start_date: string, end_date: string, category_id?: int|null, user_id?: int|null}  $filters
     * @return array{transactions: Collection, summary: array}
     */
    public function generateReport(User $user, array $filters): array
    {
        $query = $this->buildBaseQuery($user, $filters);

        $transactions = $query
            ->with(['category', 'user'])
            ->orderBy('transaction_date', 'desc')
            ->get();

        $summary = $this->calculateSummary($user, $filters);

        return [
            'transactions' => $transactions,
            'summary' => $summary,
        ];
    }

    /**
     * Membangun base query untuk laporan berdasarkan role user.
     *
     * @param  array{start_date: string, end_date: string, category_id?: int|null, user_id?: int|null}  $filters
     */
    private function buildBaseQuery(User $user, array $filters): \Illuminate\Database\Eloquent\Builder
    {
        $query = Transaction::query();

        // Admin dapat melihat semua transaksi, user biasa hanya melihat miliknya
        if (! $user->isAdmin()) {
            $query->where('user_id', $user->id);
        } elseif (isset($filters['user_id']) && $filters['user_id']) {
            $query->where('user_id', $filters['user_id']);
        }

        // Filter berdasarkan tanggal
        if (isset($filters['start_date'])) {
            $query->whereDate('transaction_date', '>=', $filters['start_date']);
        }

        if (isset($filters['end_date'])) {
            $query->whereDate('transaction_date', '<=', $filters['end_date']);
        }

        // Filter berdasarkan kategori
        if (isset($filters['category_id']) && $filters['category_id']) {
            $query->where('category_id', $filters['category_id']);
        }

        return $query;
    }

    /**
     * Menghitung summary laporan dengan data statistik.
     *
     * @param  array{start_date: string, end_date: string, category_id?: int|null, user_id?: int|null}  $filters
     * @return array{total_transactions: int, total_amount: float, beginning_balance: float, ending_balance: float, by_category: Collection, by_user: Collection}
     */
    private function calculateSummary(User $user, array $filters): array
    {
        $query = $this->buildBaseQuery($user, $filters);

        // Hitung total transaksi dan jumlah
        $totalTransactions = $query->count();
        $totalAmount = (float) $query->sum('amount');

        // Hitung saldo awal (sebelum periode laporan)
        $beginningBalance = $this->calculateBeginningBalance($user, $filters);

        // Hitung saldo akhir
        $endingBalance = $beginningBalance - $totalAmount;

        // Breakdown per kategori
        $byCategory = $this->getSpendingByCategory($user, $filters);

        // Breakdown per user (hanya untuk admin)
        $byUser = $user->isAdmin() ? $this->getSpendingByUser($filters) : collect([]);

        return [
            'total_transactions' => $totalTransactions,
            'total_amount' => $totalAmount,
            'beginning_balance' => $beginningBalance,
            'ending_balance' => $endingBalance,
            'by_category' => $byCategory,
            'by_user' => $byUser,
        ];
    }

    /**
     * Menghitung saldo awal sebelum periode laporan.
     *
     * @param  array{start_date: string, end_date: string, category_id?: int|null, user_id?: int|null}  $filters
     */
    private function calculateBeginningBalance(User $user, array $filters): float
    {
        // Total dana sebelum periode
        $fundsQuery = CashFund::query();
        if (! $user->isAdmin()) {
            $fundsQuery->where('user_id', $user->id);
        } elseif (isset($filters['user_id']) && $filters['user_id']) {
            $fundsQuery->where('user_id', $filters['user_id']);
        }

        if (isset($filters['start_date'])) {
            $fundsQuery->whereDate('fund_date', '<', $filters['start_date']);
        }
        $totalFundsBefore = (float) $fundsQuery->sum('amount');

        // Total pengeluaran sebelum periode
        $expensesQuery = Transaction::query();
        if (! $user->isAdmin()) {
            $expensesQuery->where('user_id', $user->id);
        } elseif (isset($filters['user_id']) && $filters['user_id']) {
            $expensesQuery->where('user_id', $filters['user_id']);
        }

        if (isset($filters['start_date'])) {
            $expensesQuery->whereDate('transaction_date', '<', $filters['start_date']);
        }
        $totalExpensesBefore = (float) $expensesQuery->sum('amount');

        // Dana dalam periode
        $fundsInPeriodQuery = CashFund::query();
        if (! $user->isAdmin()) {
            $fundsInPeriodQuery->where('user_id', $user->id);
        } elseif (isset($filters['user_id']) && $filters['user_id']) {
            $fundsInPeriodQuery->where('user_id', $filters['user_id']);
        }

        if (isset($filters['start_date'])) {
            $fundsInPeriodQuery->whereDate('fund_date', '>=', $filters['start_date']);
        }
        if (isset($filters['end_date'])) {
            $fundsInPeriodQuery->whereDate('fund_date', '<=', $filters['end_date']);
        }
        $fundsInPeriod = (float) $fundsInPeriodQuery->sum('amount');

        return ($totalFundsBefore - $totalExpensesBefore) + $fundsInPeriod;
    }

    /**
     * Mendapatkan breakdown pengeluaran per kategori.
     *
     * @param  array{start_date: string, end_date: string, category_id?: int|null, user_id?: int|null}  $filters
     */
    private function getSpendingByCategory(User $user, array $filters): Collection
    {
        $query = Transaction::query()
            ->selectRaw('category_id, categories.name as category_name, categories.color as category_color, SUM(transactions.amount) as total, COUNT(*) as count')
            ->join('categories', 'transactions.category_id', '=', 'categories.id')
            ->groupBy('category_id', 'categories.name', 'categories.color');

        if (! $user->isAdmin()) {
            $query->where('transactions.user_id', $user->id);
        } elseif (isset($filters['user_id']) && $filters['user_id']) {
            $query->where('transactions.user_id', $filters['user_id']);
        }

        if (isset($filters['start_date'])) {
            $query->whereDate('transaction_date', '>=', $filters['start_date']);
        }

        if (isset($filters['end_date'])) {
            $query->whereDate('transaction_date', '<=', $filters['end_date']);
        }

        return $query->orderByDesc('total')->get();
    }

    /**
     * Mendapatkan breakdown pengeluaran per user (untuk admin).
     *
     * @param  array{start_date: string, end_date: string, category_id?: int|null, user_id?: int|null}  $filters
     */
    private function getSpendingByUser(array $filters): Collection
    {
        $query = Transaction::query()
            ->selectRaw('transactions.user_id, users.name as user_name, SUM(transactions.amount) as total, COUNT(*) as count')
            ->join('users', 'transactions.user_id', '=', 'users.id')
            ->groupBy('transactions.user_id', 'users.name');

        if (isset($filters['start_date'])) {
            $query->whereDate('transaction_date', '>=', $filters['start_date']);
        }

        if (isset($filters['end_date'])) {
            $query->whereDate('transaction_date', '<=', $filters['end_date']);
        }

        if (isset($filters['category_id']) && $filters['category_id']) {
            $query->where('category_id', $filters['category_id']);
        }

        return $query->orderByDesc('total')->get();
    }

    /**
     * Export laporan ke format CSV.
     *
     * @param  array{start_date: string, end_date: string, category_id?: int|null, user_id?: int|null}  $filters
     */
    public function exportToCsv(User $user, array $filters): StreamedResponse
    {
        $report = $this->generateReport($user, $filters);

        $filename = 'laporan-petty-cash-'.date('Y-m-d').'.csv';

        return new StreamedResponse(function () use ($report, $filters, $user) {
            $handle = fopen('php://output', 'w');

            // BOM untuk UTF-8
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            // Header informasi
            fputcsv($handle, ['LAPORAN PETTY CASH']);
            fputcsv($handle, ['Periode', $filters['start_date'].' - '.$filters['end_date']]);
            fputcsv($handle, ['Digenerate pada', now()->format('d/m/Y H:i')]);
            fputcsv($handle, []);

            // Summary
            fputcsv($handle, ['RINGKASAN']);
            fputcsv($handle, ['Total Transaksi', $report['summary']['total_transactions']]);
            fputcsv($handle, ['Total Pengeluaran', 'Rp '.number_format($report['summary']['total_amount'], 0, ',', '.')]);
            fputcsv($handle, ['Saldo Awal', 'Rp '.number_format($report['summary']['beginning_balance'], 0, ',', '.')]);
            fputcsv($handle, ['Saldo Akhir', 'Rp '.number_format($report['summary']['ending_balance'], 0, ',', '.')]);
            fputcsv($handle, []);

            // Pengeluaran per Kategori
            fputcsv($handle, ['PENGELUARAN PER KATEGORI']);
            fputcsv($handle, ['Kategori', 'Jumlah', 'Jumlah Transaksi']);
            foreach ($report['summary']['by_category'] as $cat) {
                fputcsv($handle, [
                    $cat->category_name,
                    'Rp '.number_format($cat->total, 0, ',', '.'),
                    $cat->count,
                ]);
            }
            fputcsv($handle, []);

            // Pengeluaran per User (untuk admin)
            if ($user->isAdmin() && count($report['summary']['by_user']) > 0) {
                fputcsv($handle, ['PENGELUARAN PER USER']);
                fputcsv($handle, ['Nama User', 'Jumlah', 'Jumlah Transaksi']);
                foreach ($report['summary']['by_user'] as $usr) {
                    fputcsv($handle, [
                        $usr->user_name,
                        'Rp '.number_format($usr->total, 0, ',', '.'),
                        $usr->count,
                    ]);
                }
                fputcsv($handle, []);
            }

            // Detail Transaksi
            fputcsv($handle, ['DETAIL TRANSAKSI']);
            $headers = ['Tanggal', 'Deskripsi', 'Kategori', 'Jumlah'];
            if ($user->isAdmin()) {
                $headers[] = 'User';
            }
            fputcsv($handle, $headers);

            foreach ($report['transactions'] as $trx) {
                $row = [
                    date('d/m/Y', strtotime($trx->transaction_date)),
                    $trx->description,
                    $trx->category->name ?? '-',
                    'Rp '.number_format($trx->amount, 0, ',', '.'),
                ];
                if ($user->isAdmin()) {
                    $row[] = $trx->user->name ?? '-';
                }
                fputcsv($handle, $row);
            }

            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Mendapatkan daftar semua user (untuk filter admin).
     *
     * @return Collection<int, User>
     */
    public function getAllUsers(): Collection
    {
        return User::query()
            ->select('id', 'name', 'email')
            ->orderBy('name')
            ->get();
    }
}
