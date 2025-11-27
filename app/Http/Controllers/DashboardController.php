<?php

namespace App\Http\Controllers;

use App\Services\TransactionService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __construct(
        private TransactionService $transactionService
    ) {}

    /**
     * Display the petty cash dashboard.
     */
    public function __invoke(Request $request): Response
    {
        $user = $request->user();

        $currentBalance = $this->transactionService->getCurrentBalance($user);
        $totalFunds = $this->transactionService->getTotalFunds($user);
        $totalExpenses = $this->transactionService->getTotalExpenses($user);
        $currentMonthSpending = $this->transactionService->getCurrentMonthSpending($user);
        $recentTransactions = $this->transactionService->getRecentTransactions($user, 5);
        $spendingByCategory = $this->transactionService->getSpendingByCategory(
            $user,
            now()->startOfMonth()->toDateString(),
            now()->endOfMonth()->toDateString()
        );
        $hasInitialFund = $this->transactionService->hasInitialFund($user);

        return Inertia::render('Dashboard', [
            'currentBalance' => $currentBalance,
            'totalFunds' => $totalFunds,
            'totalExpenses' => $totalExpenses,
            'currentMonthSpending' => $currentMonthSpending,
            'recentTransactions' => $recentTransactions,
            'spendingByCategory' => $spendingByCategory,
            'hasInitialFund' => $hasInitialFund,
        ]);
    }
}
