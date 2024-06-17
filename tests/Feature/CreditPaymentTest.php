<?php

namespace Tests\Feature;

use App\Mail\OverpaymentNotificationMailer;
use App\Models\Consumer;
use App\Models\BankCredit;
use App\Services\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class CreditPaymentTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function payment_can_be_made_successfully()
    {

        // Create a consumer and a bank credit
        $consumer = Consumer::factory()->create();
        $bankCredit = BankCredit::factory()->create([
            'consumer_id' => $consumer->id,
            'amount' => 10000,
            'remaining_amount' => 8000
        ]);

        // Attempt to make a payment within the remaining amount
        $paymentAmount = 3000; // Less than the remaining amount

        // Use the PaymentService to process the payment
        $paymentService = new PaymentService();
        $isOverpayment = $paymentService->processPayment([
            'bank_credit' => ['id' => $bankCredit->id],
            'amount' => $paymentAmount
        ]);

        // Assert that no overpayment was detected
        $this->assertFalse($isOverpayment);

        // Refresh the bank credit to check the updated remaining amount
        $bankCredit->refresh();

        // Assert that the remaining amount is correctly updated
        $this->assertEquals(5000, $bankCredit->remaining_amount);

        // Check that the payment is saved with the correct amount
        $this->assertDatabaseHas('payments', [
            'bank_credit_id' => $bankCredit->id,
            'amount' => $paymentAmount,
            'payment_date' => now()->toDateTimeString()
        ]);
    }

    /** @test */
    public function consumer_cannot_be_overbilled()
    {
        Mail::fake();

        // Create a consumer and a bank credit
        $consumer = Consumer::factory()->create();
        $bankCredit = BankCredit::factory()->create([
            'consumer_id' => $consumer->id,
            'amount' => 10000,
            'remaining_amount' => 5000
        ]);

        // Attempt to make a payment that exceeds the remaining amount
        $paymentAmount = 6000; // $1000 more than the remaining amount

        // Use the PaymentService to process the payment
        $paymentService = new PaymentService();
        $isOverpayment = $paymentService->processPayment([
            'bank_credit' => ['id' => $bankCredit->id],
            'amount' => $paymentAmount
        ]);

        // Assert that an overpayment was detected
        $this->assertTrue($isOverpayment);

        // Refresh the bank credit to check the updated remaining amount
        $bankCredit->refresh();

        // Assert that the remaining amount is zero
        $this->assertEquals(0, $bankCredit->remaining_amount);

        // Check that the payment is saved with the correct amount
        $this->assertDatabaseHas('payments', [
            'bank_credit_id' => $bankCredit->id,
            'amount' => 5000, // The owed amount, not the overpaid amount
            'payment_date' => now()->toDateTimeString()
        ]);

        // Assert the overpayment notification was sent
        Mail::assertSent(OverpaymentNotificationMailer::class, function ($mail) use ($bankCredit) {
            return $mail->uniqueId === $bankCredit->unique_id &&
                $mail->owedAmount === 5000 &&
                $mail->overpaidAmount === 1000;
        });
    }
}
