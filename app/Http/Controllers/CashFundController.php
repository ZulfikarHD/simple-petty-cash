<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCashFundRequest;
use App\Services\TransactionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CashFundController extends Controller
{
    public function __construct(
        private TransactionService $transactionService
    ) {}

    /**
     * Show the form for creating/adding funds.
     */
    public function create(Request $request): Response
    {
        $user = $request->user();
        $hasInitialFund = $this->transactionService->hasInitialFund($user);
        $currentBalance = $this->transactionService->getCurrentBalance($user);
        $fundHistory = $this->transactionService->getFundHistory($user);

        return Inertia::render('cash-fund/Create', [
            'hasInitialFund' => $hasInitialFund,
            'currentBalance' => $currentBalance,
            'fundHistory' => $fundHistory,
        ]);
    }

    /**
     * Store a newly created fund entry.
     */
    public function store(StoreCashFundRequest $request): RedirectResponse
    {
        $user = $request->user();
        $data = $request->validated();

        $this->transactionService->addFund($user, $data);

        $message = $this->transactionService->hasInitialFund($user)
            ? 'Dana berhasil ditambahkan.'
            : 'Dana awal berhasil ditetapkan.';

        return redirect()->route('dashboard')
            ->with('success', $message);
    }
}
