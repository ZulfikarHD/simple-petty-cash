<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Models\Transaction;
use App\Services\ReceiptService;
use App\Services\TransactionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TransactionController extends Controller
{
    public function __construct(
        private TransactionService $transactionService,
        private ReceiptService $receiptService
    ) {}

    /**
     * Display a listing of transactions.
     */
    public function index(Request $request): Response
    {
        $user = $request->user();
        $filters = $request->only(['start_date', 'end_date', 'category_id', 'user_id']);

        $transactions = $this->transactionService->getTransactions($user, $filters);
        $categories = $this->transactionService->getCategories($user);
        $currentBalance = $this->transactionService->getCurrentBalance($user);

        // Daftar user untuk filter (hanya untuk admin)
        $users = $user->isAdmin() ? \App\Models\User::select('id', 'name')->orderBy('name')->get() : collect([]);

        return Inertia::render('transactions/Index', [
            'transactions' => $transactions,
            'categories' => $categories,
            'currentBalance' => $currentBalance,
            'filters' => $filters,
            'users' => $users,
            'isAdmin' => $user->isAdmin(),
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

        // Handle receipt upload
        if ($request->hasFile('receipt')) {
            $data['receipt_path'] = $this->receiptService->storeReceipt(
                $request->file('receipt'),
                $user->id
            );
        }

        // Remove receipt key as it's not a model attribute
        unset($data['receipt']);

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

        // Handle receipt removal
        if (! empty($data['remove_receipt']) && $transaction->receipt_path) {
            $this->receiptService->deleteReceipt($transaction->receipt_path);
            $data['receipt_path'] = null;
        }

        // Handle receipt upload/replace
        if ($request->hasFile('receipt')) {
            $data['receipt_path'] = $this->receiptService->replaceReceipt(
                $transaction->receipt_path,
                $request->file('receipt'),
                $user->id
            );
        }

        // Remove non-model attributes
        unset($data['receipt'], $data['remove_receipt']);

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

        // Delete receipt file if exists
        if ($transaction->receipt_path) {
            $this->receiptService->deleteReceipt($transaction->receipt_path);
        }

        $this->transactionService->deleteTransaction($transaction);

        return redirect()->route('transactions.index')
            ->with('success', 'Transaksi berhasil dihapus.');
    }

    /**
     * Remove the receipt from a transaction.
     */
    public function destroyReceipt(Transaction $transaction): RedirectResponse
    {
        $this->authorizeTransaction($transaction);

        if ($transaction->receipt_path) {
            $this->receiptService->deleteReceipt($transaction->receipt_path);
            $transaction->update(['receipt_path' => null]);
        }

        return back()->with('success', 'Bukti transaksi berhasil dihapus.');
    }

    /**
     * Authorize that the user owns this transaction or is admin.
     */
    private function authorizeTransaction(Transaction $transaction): void
    {
        $user = request()->user();
        if (! $user->isAdmin() && $transaction->user_id !== $user->id) {
            abort(403);
        }
    }
}
