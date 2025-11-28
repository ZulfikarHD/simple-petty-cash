<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function __construct(
        private UserService $userService
    ) {}

    /**
     * Display a listing of users.
     */
    public function index(Request $request): Response
    {
        $filters = $request->only(['search', 'role']);
        $users = $this->userService->getUsers($filters);
        $statistics = $this->userService->getStatistics();

        return Inertia::render('users/Index', [
            'users' => $users,
            'filters' => $filters,
            'statistics' => $statistics,
        ]);
    }

    /**
     * Show the form for creating a new user.
     */
    public function create(): Response
    {
        return Inertia::render('users/Create');
    }

    /**
     * Store a newly created user.
     */
    public function store(StoreUserRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $this->userService->createUser($data);

        return redirect()->route('users.index')
            ->with('success', 'User berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user): Response
    {
        return Inertia::render('users/Edit', [
            'user' => $user,
        ]);
    }

    /**
     * Update the specified user.
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();
        $this->userService->updateUser($user, $data);

        return redirect()->route('users.index')
            ->with('success', 'User berhasil diperbarui.');
    }

    /**
     * Remove the specified user.
     */
    public function destroy(User $user): RedirectResponse
    {
        // Mencegah admin menghapus dirinya sendiri
        if ($user->id === auth()->id()) {
            return back()->withErrors([
                'user' => 'Anda tidak dapat menghapus akun Anda sendiri.',
            ]);
        }

        $this->userService->deleteUser($user);

        return redirect()->route('users.index')
            ->with('success', 'User berhasil dihapus.');
    }

    /**
     * Reset password user.
     */
    public function resetPassword(User $user): RedirectResponse
    {
        // Mencegah admin mereset password sendiri
        if ($user->id === auth()->id()) {
            return back()->withErrors([
                'user' => 'Gunakan menu pengaturan untuk mengubah password Anda.',
            ]);
        }

        $newPassword = $this->userService->resetPassword($user);

        return back()->with('success', "Password berhasil direset. Password baru: {$newPassword}");
    }
}
