<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\BankCredit;
use App\Models\Consumer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Inertia\Testing\AssertableInertia as AssertInertia;
use Inertia\Testing\AssertableInertia;

class BankCreditsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function authenticated_users_can_view_bank_credits()
    {
        $this->assertDatabaseCount('bank_credits', 0); // Ensure no bank credits exist before test starts

        $user = User::factory()->create();
        $consumer = Consumer::factory()->create();
        BankCredit::factory()->count(3)->create(['consumer_id' => $consumer->id]);

        $response = $this->actingAs($user)->get('/bank-credits');

        $response->assertStatus(200);

        $response->assertInertia(function (AssertInertia $page) {
            $page->component('BankCredits/Index')
                ->has('bankCredits');
        });

        $this->assertDatabaseCount('bank_credits', 3); // Confirm only 3 bank credits exist after setup
    }

    /** @test */
    public function unauthenticated_users_cannot_view_bank_credits()
    {
        $this->assertDatabaseCount('bank_credits', 0); // Ensure no bank credits exist before test starts

        $consumer = Consumer::factory()->create();
        BankCredit::factory()->count(3)->create(['consumer_id' => $consumer->id]);

        $response = $this->get('/bank-credits');

        $response->assertStatus(302); // Typically unauthenticated access redirects to login
        $response->assertRedirect('/login'); // Ensure the redirection goes to the login page
    }
}
