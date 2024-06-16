<?php

namespace Database\Factories;

use App\Models\Consumer;
use Illuminate\Database\Eloquent\Factories\Factory;

class ConsumerFactory extends Factory
{
    /**
     * The name of the model that the factory corresponds to.
     *
     * @var string
     */
    protected $model = Consumer::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'total_credit_amount' => 0,
        ];
    }
}
