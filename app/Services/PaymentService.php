<?php

namespace App\Services;
use App\Mail\OverpaymentNotificationMailer;
use App\Models\BankCredit;
use App\Models\Payment;
use Illuminate\Support\Facades\Mail;

class PaymentService
{
    public function processPayment($data)
    {
        $bankCredit = BankCredit::select('id', 'remaining_amount', 'consumer_id')
                                ->where('id', $data['bank_credit']['id'])
                                ->with('consumer:id,email,total_credit_amount')
                                ->firstOrFail();

        $amount = $data['amount'];
        $remainingAmount = $bankCredit->remaining_amount;
        $isOverpayment = $amount > $remainingAmount;

        if ($isOverpayment) {
            Mail::to($bankCredit->consumer->email)->send(new OverpaymentNotificationMailer($bankCredit->unique_id, $remainingAmount, $amount - $remainingAmount));
            // Adjust the amount to the maximum payable amount
            $amount = $remainingAmount;
        }

        $payment = new Payment([
            'bank_credit_id' => $bankCredit->id,
            'amount' => $amount,
            'payment_date' => now(),
        ]);
        $payment->save();

        $bankCredit->remaining_amount -= $amount;
        $bankCredit->save();

        $bankCredit->consumer->total_credit_amount -= $amount;
        $bankCredit->consumer->save();

        return $isOverpayment;
    }
}
