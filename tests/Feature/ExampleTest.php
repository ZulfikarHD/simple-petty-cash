<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_redirects_to_login_for_guest()
    {
        $response = $this->get(route('home'));

        $response->assertRedirect(route('login'));
    }

    public function test_home_redirects_to_dashboard_for_authenticated_user()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('home'));

        $response->assertRedirect(route('dashboard'));
    }
}
