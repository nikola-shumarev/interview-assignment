<?php

namespace Database\Factories;

use App\Models\BankCredit;
use App\Models\Consumer;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class BankCreditFactory extends Factory
{
    /**
     * The name of the model that the factory corresponds to.
     *
     * @var string
     */
    protected $model = BankCredit::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'consumer_id' => Consumer::factory(),  // Assuming each bank credit belongs to a consumer
            'amount' => $this->faker->numberBetween(1000, 50000),
            'remaining_amount' => function (array $attributes) {
                // Optionally, you can set the remaining amount to be less than the initial amount
                return $this->faker->numberBetween(500, $attributes['amount']);
            },
            'due_date' => Carbon::now()->addMonths($this->faker->numberBetween(3, 120)),
            'interest_rate' => config('app.bank_credit_interest_rate')
        ];
    }
}
