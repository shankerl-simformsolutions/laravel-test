<?php

namespace Database\Factories;

use App\Models\SubscriptionHistory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubscriptionHistoryFactory extends Factory
{
    protected $model = SubscriptionHistory::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'type' => $this->faker->randomElement(['free', 'premium', 'pro']),
            'status' => $this->faker->randomElement(['active', 'canceled']),
        ];
    }
}
