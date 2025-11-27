<?php

namespace Tests\Feature;

use App\Models\CashFund;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use App\Services\TransactionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->category = Category::factory()->default()->create();
    }

    public function test_user_can_view_transactions_list(): void
    {
        $this->actingAs($this->user);

        CashFund::factory()->create([
            'user_id' => $this->user->id,
            'amount' => 1000000,
        ]);

        Transaction::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
        ]);

        $response = $this->get('/transactions');

        $response->assertStatus(200);
        $response->assertInertia(
            fn ($page) => $page
                ->component('transactions/Index')
                ->has('transactions.data', 3)
                ->has('categories')
                ->has('currentBalance')
        );
    }

    public function test_user_can_view_create_transaction_form(): void
    {
        $this->actingAs($this->user);

        $response = $this->get('/transactions/create');

        $response->assertStatus(200);
        $response->assertInertia(
            fn ($page) => $page
                ->component('transactions/Create')
                ->has('categories')
                ->has('currentBalance')
        );
    }

    public function test_user_can_create_transaction(): void
    {
        $this->actingAs($this->user);

        CashFund::factory()->create([
            'user_id' => $this->user->id,
            'amount' => 1000000,
        ]);

        $response = $this->post('/transactions', [
            'amount' => 50000,
            'description' => 'Beli alat tulis',
            'transaction_date' => now()->toDateString(),
            'category_id' => $this->category->id,
        ]);

        $response->assertRedirect('/transactions');

        $this->assertDatabaseHas('transactions', [
            'user_id' => $this->user->id,
            'amount' => 50000,
            'description' => 'Beli alat tulis',
            'category_id' => $this->category->id,
        ]);
    }

    public function test_user_cannot_create_transaction_exceeding_balance(): void
    {
        $this->actingAs($this->user);

        CashFund::factory()->create([
            'user_id' => $this->user->id,
            'amount' => 100000,
        ]);

        $response = $this->post('/transactions', [
            'amount' => 200000,
            'description' => 'Pengeluaran besar',
            'transaction_date' => now()->toDateString(),
            'category_id' => $this->category->id,
        ]);

        $response->assertSessionHasErrors('amount');

        $this->assertDatabaseMissing('transactions', [
            'user_id' => $this->user->id,
            'description' => 'Pengeluaran besar',
        ]);
    }

    public function test_user_can_update_transaction(): void
    {
        $this->actingAs($this->user);

        CashFund::factory()->create([
            'user_id' => $this->user->id,
            'amount' => 1000000,
        ]);

        $transaction = Transaction::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'amount' => 50000,
            'description' => 'Original description',
        ]);

        $response = $this->put("/transactions/{$transaction->id}", [
            'amount' => 75000,
            'description' => 'Updated description',
            'transaction_date' => now()->toDateString(),
            'category_id' => $this->category->id,
        ]);

        $response->assertRedirect('/transactions');

        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'amount' => 75000,
            'description' => 'Updated description',
        ]);
    }

    public function test_user_can_delete_transaction(): void
    {
        $this->actingAs($this->user);

        $transaction = Transaction::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
        ]);

        $response = $this->delete("/transactions/{$transaction->id}");

        $response->assertRedirect('/transactions');

        $this->assertDatabaseMissing('transactions', [
            'id' => $transaction->id,
        ]);
    }

    public function test_user_cannot_access_other_users_transaction(): void
    {
        $this->actingAs($this->user);

        $otherUser = User::factory()->create();
        $transaction = Transaction::factory()->create([
            'user_id' => $otherUser->id,
            'category_id' => $this->category->id,
        ]);

        $response = $this->get("/transactions/{$transaction->id}/edit");

        $response->assertStatus(403);
    }

    public function test_transaction_validates_required_fields(): void
    {
        $this->actingAs($this->user);

        $response = $this->post('/transactions', []);

        $response->assertSessionHasErrors(['amount', 'description', 'transaction_date', 'category_id']);
    }

    public function test_transaction_validates_description_length(): void
    {
        $this->actingAs($this->user);

        CashFund::factory()->create([
            'user_id' => $this->user->id,
            'amount' => 1000000,
        ]);

        $response = $this->post('/transactions', [
            'amount' => 50000,
            'description' => str_repeat('a', 201),
            'transaction_date' => now()->toDateString(),
            'category_id' => $this->category->id,
        ]);

        $response->assertSessionHasErrors('description');
    }

    public function test_transaction_validates_future_date(): void
    {
        $this->actingAs($this->user);

        CashFund::factory()->create([
            'user_id' => $this->user->id,
            'amount' => 1000000,
        ]);

        $response = $this->post('/transactions', [
            'amount' => 50000,
            'description' => 'Test transaction',
            'transaction_date' => now()->addDay()->toDateString(),
            'category_id' => $this->category->id,
        ]);

        $response->assertSessionHasErrors('transaction_date');
    }

    public function test_balance_calculation_is_correct(): void
    {
        $service = new TransactionService;

        CashFund::factory()->create([
            'user_id' => $this->user->id,
            'amount' => 1000000,
        ]);

        CashFund::factory()->create([
            'user_id' => $this->user->id,
            'amount' => 500000,
        ]);

        Transaction::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'amount' => 200000,
        ]);

        Transaction::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'amount' => 100000,
        ]);

        $balance = $service->getCurrentBalance($this->user);

        $this->assertEquals(1200000, $balance);
    }

    public function test_spending_by_category_calculation(): void
    {
        $service = new TransactionService;

        $category1 = Category::factory()->default()->create(['name' => 'Food']);
        $category2 = Category::factory()->default()->create(['name' => 'Transport']);

        Transaction::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $category1->id,
            'amount' => 100000,
            'transaction_date' => now(),
        ]);

        Transaction::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $category1->id,
            'amount' => 50000,
            'transaction_date' => now(),
        ]);

        Transaction::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $category2->id,
            'amount' => 75000,
            'transaction_date' => now(),
        ]);

        $spending = $service->getSpendingByCategory(
            $this->user,
            now()->startOfMonth()->toDateString(),
            now()->endOfMonth()->toDateString()
        );

        $this->assertCount(2, $spending);

        $foodSpending = $spending->firstWhere('category_name', 'Food');
        $this->assertEquals(150000, $foodSpending->total);

        $transportSpending = $spending->firstWhere('category_name', 'Transport');
        $this->assertEquals(75000, $transportSpending->total);
    }

    public function test_guest_cannot_access_transactions(): void
    {
        $response = $this->get('/transactions');

        $response->assertRedirect('/login');
    }
}
