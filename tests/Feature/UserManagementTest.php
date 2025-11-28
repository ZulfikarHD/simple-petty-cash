<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\UserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $regularUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
        $this->regularUser = User::factory()->create();
    }

    public function test_admin_can_view_users_list(): void
    {
        $this->actingAs($this->admin);

        User::factory()->count(5)->create();

        $response = $this->get('/users');

        $response->assertStatus(200);
        $response->assertInertia(
            fn ($page) => $page
                ->component('users/Index')
                ->has('users.data')
                ->has('statistics')
        );
    }

    public function test_regular_user_cannot_access_users_list(): void
    {
        $this->actingAs($this->regularUser);

        $response = $this->get('/users');

        $response->assertStatus(403);
    }

    public function test_admin_can_view_create_user_form(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get('/users/create');

        $response->assertStatus(200);
        $response->assertInertia(
            fn ($page) => $page->component('users/Create')
        );
    }

    public function test_admin_can_create_user(): void
    {
        $this->actingAs($this->admin);

        $response = $this->post('/users', [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'is_admin' => false,
        ]);

        $response->assertRedirect('/users');

        $this->assertDatabaseHas('users', [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'is_admin' => false,
        ]);
    }

    public function test_admin_can_create_admin_user(): void
    {
        $this->actingAs($this->admin);

        $response = $this->post('/users', [
            'name' => 'New Admin',
            'email' => 'newadmin@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'is_admin' => true,
        ]);

        $response->assertRedirect('/users');

        $this->assertDatabaseHas('users', [
            'name' => 'New Admin',
            'email' => 'newadmin@example.com',
            'is_admin' => true,
        ]);
    }

    public function test_admin_can_view_edit_user_form(): void
    {
        $this->actingAs($this->admin);

        $user = User::factory()->create();

        $response = $this->get("/users/{$user->id}/edit");

        $response->assertStatus(200);
        $response->assertInertia(
            fn ($page) => $page
                ->component('users/Edit')
                ->has('user')
        );
    }

    public function test_admin_can_update_user(): void
    {
        $this->actingAs($this->admin);

        $user = User::factory()->create([
            'name' => 'Old Name',
            'email' => 'old@example.com',
        ]);

        $response = $this->put("/users/{$user->id}", [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'is_admin' => true,
        ]);

        $response->assertRedirect('/users');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'is_admin' => true,
        ]);
    }

    public function test_admin_can_update_user_with_new_password(): void
    {
        $this->actingAs($this->admin);

        $user = User::factory()->create();
        $oldPasswordHash = $user->password;

        $response = $this->put("/users/{$user->id}", [
            'name' => $user->name,
            'email' => $user->email,
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertRedirect('/users');

        $user->refresh();
        $this->assertNotEquals($oldPasswordHash, $user->password);
    }

    public function test_admin_can_delete_user(): void
    {
        $this->actingAs($this->admin);

        $user = User::factory()->create();

        $response = $this->delete("/users/{$user->id}");

        $response->assertRedirect('/users');

        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
    }

    public function test_admin_cannot_delete_self(): void
    {
        $this->actingAs($this->admin);

        $response = $this->delete("/users/{$this->admin->id}");

        $response->assertSessionHasErrors('user');

        $this->assertDatabaseHas('users', [
            'id' => $this->admin->id,
        ]);
    }

    public function test_admin_can_reset_user_password(): void
    {
        $this->actingAs($this->admin);

        $user = User::factory()->create();
        $oldPasswordHash = $user->password;

        $response = $this->post("/users/{$user->id}/reset-password");

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $user->refresh();
        $this->assertNotEquals($oldPasswordHash, $user->password);
    }

    public function test_admin_cannot_reset_own_password(): void
    {
        $this->actingAs($this->admin);

        $response = $this->post("/users/{$this->admin->id}/reset-password");

        $response->assertSessionHasErrors('user');
    }

    public function test_create_user_validates_required_fields(): void
    {
        $this->actingAs($this->admin);

        $response = $this->post('/users', []);

        $response->assertSessionHasErrors(['name', 'email', 'password']);
    }

    public function test_create_user_validates_unique_email(): void
    {
        $this->actingAs($this->admin);

        $existingUser = User::factory()->create([
            'email' => 'existing@example.com',
        ]);

        $response = $this->post('/users', [
            'name' => 'New User',
            'email' => 'existing@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_create_user_validates_password_confirmation(): void
    {
        $this->actingAs($this->admin);

        $response = $this->post('/users', [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'different_password',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_create_user_validates_minimum_password_length(): void
    {
        $this->actingAs($this->admin);

        $response = $this->post('/users', [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'password' => 'short',
            'password_confirmation' => 'short',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_update_user_allows_same_email(): void
    {
        $this->actingAs($this->admin);

        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);

        $response = $this->put("/users/{$user->id}", [
            'name' => 'Updated Name',
            'email' => 'test@example.com',
        ]);

        $response->assertRedirect('/users');
        $response->assertSessionHasNoErrors();
    }

    public function test_user_service_get_statistics(): void
    {
        User::factory()->count(3)->create(['is_admin' => false]);
        User::factory()->count(2)->create(['is_admin' => true]);

        $service = new UserService;
        $stats = $service->getStatistics();

        $this->assertEquals(7, $stats['total']);
        $this->assertEquals(3, $stats['admins']);
        $this->assertEquals(4, $stats['users']);
    }

    public function test_user_service_filter_by_search(): void
    {
        User::factory()->create(['name' => 'John Doe', 'email' => 'john@test.com']);
        User::factory()->create(['name' => 'Jane Smith', 'email' => 'jane@test.com']);
        User::factory()->create(['name' => 'Bob Wilson', 'email' => 'bob@test.com']);

        $service = new UserService;

        $results = $service->getUsers(['search' => 'john']);
        $this->assertEquals(1, $results->total());

        $results = $service->getUsers(['search' => '@test.com']);
        $this->assertEquals(3, $results->total());
    }

    public function test_user_service_filter_by_role(): void
    {
        User::factory()->count(3)->create(['is_admin' => false]);
        User::factory()->count(2)->create(['is_admin' => true]);

        $service = new UserService;

        $admins = $service->getUsers(['role' => 'admin']);
        $this->assertEquals(3, $admins->total());

        $users = $service->getUsers(['role' => 'user']);
        $this->assertEquals(4, $users->total());
    }

    public function test_guest_cannot_access_user_management(): void
    {
        $response = $this->get('/users');

        $response->assertRedirect('/login');
    }
}
