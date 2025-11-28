<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserService
{
    /**
     * Mendapatkan daftar user dengan pagination dan filter.
     *
     * @param  array{search?: string, role?: string}  $filters
     */
    public function getUsers(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = User::query()
            ->orderBy('name');

        if (isset($filters['search']) && $filters['search']) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if (isset($filters['role']) && $filters['role'] !== '') {
            $isAdmin = $filters['role'] === 'admin';
            $query->where('is_admin', $isAdmin);
        }

        return $query->paginate($perPage);
    }

    /**
     * Membuat user baru.
     *
     * @param  array{name: string, email: string, password: string, is_admin?: bool}  $data
     */
    public function createUser(array $data): User
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'is_admin' => $data['is_admin'] ?? false,
        ]);
    }

    /**
     * Update data user.
     *
     * @param  array{name?: string, email?: string, password?: string, is_admin?: bool}  $data
     */
    public function updateUser(User $user, array $data): User
    {
        $updateData = [];

        if (isset($data['name'])) {
            $updateData['name'] = $data['name'];
        }

        if (isset($data['email'])) {
            $updateData['email'] = $data['email'];
        }

        if (isset($data['password']) && $data['password']) {
            $updateData['password'] = Hash::make($data['password']);
        }

        if (isset($data['is_admin'])) {
            $updateData['is_admin'] = $data['is_admin'];
        }

        $user->update($updateData);

        return $user->fresh();
    }

    /**
     * Menghapus user.
     */
    public function deleteUser(User $user): bool
    {
        return $user->delete();
    }

    /**
     * Reset password user dengan password random.
     * Mengembalikan password baru yang di-generate.
     */
    public function resetPassword(User $user): string
    {
        $newPassword = Str::random(12);

        $user->update([
            'password' => Hash::make($newPassword),
        ]);

        return $newPassword;
    }

    /**
     * Mendapatkan statistik user untuk dashboard admin.
     *
     * @return array{total: int, admins: int, users: int}
     */
    public function getStatistics(): array
    {
        return [
            'total' => User::count(),
            'admins' => User::where('is_admin', true)->count(),
            'users' => User::where('is_admin', false)->count(),
        ];
    }
}
