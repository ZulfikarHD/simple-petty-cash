<?php

namespace App\Services;

use App\Models\CashFund;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class TransactionService
{
    /**
     * Get the current balance for a user (total funds - total expenses).
     */
    public function getCurrentBalance(User $user): float
    {
        $totalFunds = $user->cashFunds()->sum('amount');
        $totalExpenses = $user->transactions()->sum('amount');

        return (float) ($totalFunds - $totalExpenses);
    }

    /**
     * Get total funds for a user.
     */
    public function getTotalFunds(User $user): float
    {
        return (float) $user->cashFunds()->sum('amount');
    }

    /**
     * Get total expenses for a user.
     */
    public function getTotalExpenses(User $user): float
    {
        return (float) $user->transactions()->sum('amount');
    }

    /**
     * Check if user can afford a transaction amount.
     */
    public function canAfford(User $user, float $amount): bool
    {
        return $this->getCurrentBalance($user) >= $amount;
    }

    /**
     * Create a new transaction with validation.
     *
     * @param  array{amount: float, description: string, transaction_date: string, category_id: int}  $data
     */
    public function createTransaction(User $user, array $data): Transaction
    {
        return $user->transactions()->create($data);
    }

    /**
     * Update an existing transaction.
     *
     * @param  array{amount?: float, description?: string, transaction_date?: string, category_id?: int}  $data
     */
    public function updateTransaction(Transaction $transaction, array $data): Transaction
    {
        $transaction->update($data);

        return $transaction->fresh();
    }

    /**
     * Delete a transaction.
     */
    public function deleteTransaction(Transaction $transaction): bool
    {
        return $transaction->delete();
    }

    /**
     * Get transactions for a user with optional filtering.
     * Admin dapat melihat semua transaksi, user biasa hanya melihat miliknya.
     *
     * @param  array{start_date?: string, end_date?: string, category_id?: int, user_id?: int}  $filters
     */
    public function getTransactions(User $user, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Transaction::query()
            ->with(['category', 'user'])
            ->orderBy('transaction_date', 'desc')
            ->orderBy('created_at', 'desc');

        // Admin dapat melihat semua transaksi, user biasa hanya melihat miliknya
        if (! $user->isAdmin()) {
            $query->where('user_id', $user->id);
        } elseif (isset($filters['user_id']) && $filters['user_id']) {
            $query->where('user_id', $filters['user_id']);
        }

        if (isset($filters['start_date'])) {
            $query->whereDate('transaction_date', '>=', $filters['start_date']);
        }

        if (isset($filters['end_date'])) {
            $query->whereDate('transaction_date', '<=', $filters['end_date']);
        }

        if (isset($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        return $query->paginate($perPage);
    }

    /**
     * Get recent transactions for dashboard.
     *
     * @return Collection<int, Transaction>
     */
    public function getRecentTransactions(User $user, int $limit = 5): Collection
    {
        return $user->transactions()
            ->with('category')
            ->orderBy('transaction_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get spending by category for current month.
     *
     * @return Collection<int, object{category_id: int, category_name: string, category_color: string, total: float}>
     */
    public function getSpendingByCategory(User $user, ?string $startDate = null, ?string $endDate = null): Collection
    {
        $query = Transaction::query()
            ->selectRaw('category_id, categories.name as category_name, categories.color as category_color, SUM(transactions.amount) as total')
            ->join('categories', 'transactions.category_id', '=', 'categories.id')
            ->where('transactions.user_id', $user->id)
            ->groupBy('category_id', 'categories.name', 'categories.color');

        if ($startDate) {
            $query->whereDate('transaction_date', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('transaction_date', '<=', $endDate);
        }

        return $query->get();
    }

    /**
     * Get spending for current month.
     */
    public function getCurrentMonthSpending(User $user): float
    {
        return (float) $user->transactions()
            ->whereMonth('transaction_date', now()->month)
            ->whereYear('transaction_date', now()->year)
            ->sum('amount');
    }

    /**
     * Get available categories (default + user custom).
     *
     * @return Collection<int, Category>
     */
    public function getCategories(User $user): Collection
    {
        return Category::query()
            ->where('is_default', true)
            ->orWhere('user_id', $user->id)
            ->orderBy('name')
            ->get();
    }

    /**
     * Add funds (initial or replenishment).
     *
     * @param  array{amount: float, note?: string, fund_date: string}  $data
     */
    public function addFund(User $user, array $data): CashFund
    {
        return $user->cashFunds()->create($data);
    }

    /**
     * Get fund history for a user.
     *
     * @return Collection<int, CashFund>
     */
    public function getFundHistory(User $user): Collection
    {
        return $user->cashFunds()
            ->orderBy('fund_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Check if user has set up initial fund.
     */
    public function hasInitialFund(User $user): bool
    {
        return $user->cashFunds()->exists();
    }
}
