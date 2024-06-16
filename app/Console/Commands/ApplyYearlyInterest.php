<?php

namespace App\Console\Commands;

use App\Models\BankCredit;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ApplyYearlyInterest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:apply-yearly-interest';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Apply yearly interest to credits';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Applying yearly interest to credits...');

        $credits = BankCredit::with('consumer:id,total_credit_amount')
            ->where('due_date', '>=', now())
            ->get();

        foreach ($credits as $credit) {
            $monthsSinceCreation = $credit->created_at->diffInMonths(Carbon::now());
            $today = Carbon::today();

            // Check if the current month is a multiple of 12 (yearly)
            if ($monthsSinceCreation % 12 == 0 && $monthsSinceCreation != 0) {
                // Check if interest was applied in the last 12 months
                if ($credit->last_interest_applied === null || $credit->last_interest_applied->diffInMonths($today) >= 12) {

                    // Calculate the interest amount for this specific credit
                    $interestAmount = $credit->remaining_amount * ($credit->interest_rate / 100);

                    $credit->remaining_amount += $interestAmount;

                    // Add only the calculated interest amount to the consumer's total credit amount
                    $credit->consumer->total_credit_amount += $interestAmount;

                    // Update the last interest applied date
                    $credit->last_interest_applied = $today;

                    $credit->save();
                    $credit->consumer->save();

                    $this->info("Applied interest to credit ID: {$credit->id}");
                }
            }
        }

        $this->info('Yearly interest application complete.');
    }
}
