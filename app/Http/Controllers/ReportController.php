<?php

namespace App\Http\Controllers;

use App\Services\ReportService;
use App\Services\TransactionService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function __construct(
        private ReportService $reportService,
        private TransactionService $transactionService
    ) {}

    /**
     * Menampilkan halaman laporan dengan filter dan summary.
     */
    public function index(Request $request): Response
    {
        $user = $request->user();

        // Default filter: bulan ini
        $filters = [
            'start_date' => $request->input('start_date', now()->startOfMonth()->toDateString()),
            'end_date' => $request->input('end_date', now()->endOfMonth()->toDateString()),
            'category_id' => $request->input('category_id'),
            'user_id' => $request->input('user_id'),
        ];

        $report = $this->reportService->generateReport($user, $filters);
        $categories = $this->transactionService->getCategories($user);

        // Daftar user untuk filter (hanya untuk admin)
        $users = $user->isAdmin() ? $this->reportService->getAllUsers() : collect([]);

        return Inertia::render('reports/Index', [
            'transactions' => $report['transactions'],
            'summary' => $report['summary'],
            'filters' => $filters,
            'categories' => $categories,
            'users' => $users,
            'isAdmin' => $user->isAdmin(),
        ]);
    }

    /**
     * Export laporan ke format CSV.
     */
    public function export(Request $request): StreamedResponse
    {
        $user = $request->user();

        $filters = [
            'start_date' => $request->input('start_date', now()->startOfMonth()->toDateString()),
            'end_date' => $request->input('end_date', now()->endOfMonth()->toDateString()),
            'category_id' => $request->input('category_id'),
            'user_id' => $request->input('user_id'),
        ];

        return $this->reportService->exportToCsv($user, $filters);
    }
}
