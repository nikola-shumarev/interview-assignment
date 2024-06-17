<?php

namespace App\Services;

use App\Models\BankCredit;
use App\Models\Consumer;
use Carbon\Carbon;

class BankCreditService
{
    public function createCredit(array $data, Consumer $consumer): BankCredit
    {
        if (!$consumer) {
            $consumer = Consumer::firstOrCreate([
                'email' => $data['email']
            ], [
                'name' => $data['name']
            ]);
        }

        // Calculate due date based on the current date plus the number of months
        $dueDate = Carbon::now()->addMonths($data['months']);

        $bankCredit = new BankCredit([
            'consumer_id' => $consumer->id,
            'amount' => $data['amount'],
            // Initially, the remaining amount is the full amount
            'remaining_amount' => $data['amount'],
            'due_date' => $dueDate,
            'interest_rate' => config('app.bank_credit_interest_rate'),
        ]);

        $bankCredit->save();

        $consumer->total_credit_amount += $data['amount'];
        $consumer->save();

        return $bankCredit;
    }
}
