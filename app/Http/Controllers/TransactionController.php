<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Models\Transaction;
use App\Services\TransactionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TransactionController extends Controller
{
    public function __construct(
        private TransactionService $transactionService
    ) {}

    /**
     * Display a listing of transactions.
     */
    public function index(Request $request): Response
    {
        $user = $request->user();
        $filters = $request->only(['start_date', 'end_date', 'category_id']);

        $transactions = $this->transactionService->getTransactions($user, $filters);
        $categories = $this->transactionService->getCategories($user);
        $currentBalance = $this->transactionService->getCurrentBalance($user);

        return Inertia::render('transactions/Index', [
            'transactions' => $transactions,
            'categories' => $categories,
            'currentBalance' => $currentBalance,
            'filters' => $filters,
        ]);
    }

    /**
     * Show the form for creating a new transaction.
     */
    public function create(Request $request): Response
    {
        $user = $request->user();
        $categories = $this->transactionService->getCategories($user);
        $currentBalance = $this->transactionService->getCurrentBalance($user);

        return Inertia::render('transactions/Create', [
            'categories' => $categories,
            'currentBalance' => $currentBalance,
        ]);
    }

    /**
     * Store a newly created transaction.
     */
    public function store(StoreTransactionRequest $request): RedirectResponse
    {
        $user = $request->user();
        $data = $request->validated();

        $currentBalance = $this->transactionService->getCurrentBalance($user);

        if ($data['amount'] > $currentBalance) {
            return back()->withErrors([
                'amount' => 'Saldo tidak mencukupi. Saldo saat ini: Rp '.number_format($currentBalance, 0, ',', '.'),
            ]);
        }

        $this->transactionService->createTransaction($user, $data);

        return redirect()->route('transactions.index')
            ->with('success', 'Pengeluaran berhasil dicatat.');
    }

    /**
     * Show the form for editing the specified transaction.
     */
    public function edit(Transaction $transaction): Response
    {
        $this->authorizeTransaction($transaction);

        $user = request()->user();
        $categories = $this->transactionService->getCategories($user);
        $currentBalance = $this->transactionService->getCurrentBalance($user);

        return Inertia::render('transactions/Edit', [
            'transaction' => $transaction->load('category'),
            'categories' => $categories,
            'currentBalance' => $currentBalance,
        ]);
    }

    /**
     * Update the specified transaction.
     */
    public function update(UpdateTransactionRequest $request, Transaction $transaction): RedirectResponse
    {
        $user = $request->user();
        $data = $request->validated();

        if (isset($data['amount'])) {
            $currentBalance = $this->transactionService->getCurrentBalance($user);
            $balanceWithoutTransaction = $currentBalance + $transaction->amount;

            if ($data['amount'] > $balanceWithoutTransaction) {
                return back()->withErrors([
                    'amount' => 'Saldo tidak mencukupi untuk perubahan ini.',
                ]);
            }
        }

        $this->transactionService->updateTransaction($transaction, $data);

        return redirect()->route('transactions.index')
            ->with('success', 'Transaksi berhasil diperbarui.');
    }

    /**
     * Remove the specified transaction.
     */
    public function destroy(Transaction $transaction): RedirectResponse
    {
        $this->authorizeTransaction($transaction);

        $this->transactionService->deleteTransaction($transaction);

        return redirect()->route('transactions.index')
            ->with('success', 'Transaksi berhasil dihapus.');
    }

    /**
     * Authorize that the user owns this transaction.
     */
    private function authorizeTransaction(Transaction $transaction): void
    {
        if ($transaction->user_id !== request()->user()->id) {
            abort(403);
        }
    }
}
