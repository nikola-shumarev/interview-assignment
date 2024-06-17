<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\BankCredit;
use App\Models\Consumer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Inertia\Testing\AssertableInertia as AssertInertia;

class BankCreditTest extends TestCase
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

    /** @test */
    public function authenticated_user_can_create_bank_credit()
    {
        $this->assertDatabaseCount('bank_credits', 0);

        // Create a consumer with a specific total_credit_amount
        $consumer = Consumer::factory()->create(['total_credit_amount' => 0]);

        // Use the created consumer's ID in the POST request
        $response = $this->actingAs(User::factory()->create())->post('/bank-credits', [
            'consumer_id' => $consumer->id,
            'amount' => 25000,
            'months' => 12,
            'name' => 'John Doe',
            'email' => 'john.doe@example.com'
        ]);

        $response->assertSessionDoesntHaveErrors(); // No validation errors should occur

        // Ensure only one bank credit exists and it's for the correct consumer
        $this->assertDatabaseCount('bank_credits', 1);
    }

    /** @test */
    public function consumer_cannot_have_bank_credits_exceeding_grand_total_of_80000()
    {
        $consumer = Consumer::factory()->create(['total_credit_amount' => 0]);
        $initialCredit = BankCredit::factory()->create([
            'consumer_id' => $consumer->id,
            'amount' => 50000,
            'remaining_amount' => 50000
        ]);

        $consumer->total_credit_amount += $initialCredit->amount;
        $consumer->save();

        $response = $this->actingAs(User::factory()->create())->post('/bank-credits', [
            'consumer_id' => $consumer->id,
            'amount' => 31000, // This should push the total over 80000
            'months' => 12
        ]);

        $response->assertSessionHasErrors(); // Expecting validation errors due to credit limit

        $this->assertDatabaseCount('bank_credits', 1); // Only the initial credit should exist
        $this->assertDatabaseMissing('bank_credits', [
            'consumer_id' => $consumer->id,
            'amount' => 31000
        ]); // This credit should not be created
    }
}
