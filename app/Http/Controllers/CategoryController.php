<?php

namespace App\Http\Controllers;

use App\Services\TransactionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function __construct(
        private TransactionService $transactionService
    ) {}

    /**
     * Get list of categories for the authenticated user.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $categories = $this->transactionService->getCategories($user);

        return response()->json($categories);
    }
}
