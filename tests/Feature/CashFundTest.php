<?php

namespace Tests\Feature;

use App\Models\CashFund;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CashFundTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    public function test_user_can_view_cash_fund_form(): void
    {
        $this->actingAs($this->user);

        $response = $this->get('/cash-fund');

        $response->assertStatus(200);
        $response->assertInertia(
            fn ($page) => $page
                ->component('cash-fund/Create')
                ->has('hasInitialFund')
                ->has('currentBalance')
                ->has('fundHistory')
        );
    }

    public function test_user_can_add_initial_fund(): void
    {
        $this->actingAs($this->user);

        $response = $this->post('/cash-fund', [
            'amount' => 1000000,
            'note' => 'Dana awal kas kecil',
            'fund_date' => now()->toDateString(),
        ]);

        $response->assertRedirect('/dashboard');

        $this->assertDatabaseHas('cash_funds', [
            'user_id' => $this->user->id,
            'amount' => 1000000,
            'note' => 'Dana awal kas kecil',
        ]);
    }

    public function test_user_can_add_replenishment(): void
    {
        $this->actingAs($this->user);

        CashFund::factory()->create([
            'user_id' => $this->user->id,
            'amount' => 500000,
        ]);

        $response = $this->post('/cash-fund', [
            'amount' => 300000,
            'note' => 'Isi ulang kas',
            'fund_date' => now()->toDateString(),
        ]);

        $response->assertRedirect('/dashboard');

        $this->assertEquals(2, CashFund::where('user_id', $this->user->id)->count());
        $this->assertEquals(800000, CashFund::where('user_id', $this->user->id)->sum('amount'));
    }

    public function test_cash_fund_validates_required_fields(): void
    {
        $this->actingAs($this->user);

        $response = $this->post('/cash-fund', []);

        $response->assertSessionHasErrors(['amount', 'fund_date']);
    }

    public function test_cash_fund_validates_positive_amount(): void
    {
        $this->actingAs($this->user);

        $response = $this->post('/cash-fund', [
            'amount' => 0,
            'fund_date' => now()->toDateString(),
        ]);

        $response->assertSessionHasErrors('amount');
    }

    public function test_cash_fund_validates_future_date(): void
    {
        $this->actingAs($this->user);

        $response = $this->post('/cash-fund', [
            'amount' => 1000000,
            'fund_date' => now()->addDay()->toDateString(),
        ]);

        $response->assertSessionHasErrors('fund_date');
    }

    public function test_fund_history_shows_all_entries(): void
    {
        $this->actingAs($this->user);

        CashFund::factory()->count(3)->create([
            'user_id' => $this->user->id,
        ]);

        $response = $this->get('/cash-fund');

        $response->assertInertia(
            fn ($page) => $page
                ->component('cash-fund/Create')
                ->has('fundHistory', 3)
        );
    }

    public function test_guest_cannot_access_cash_fund(): void
    {
        $response = $this->get('/cash-fund');

        $response->assertRedirect('/login');
    }
}
