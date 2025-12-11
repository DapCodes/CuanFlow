<?php

namespace Database\Factories;

use App\Models\Discount;
use Illuminate\Database\Eloquent\Factories\Factory;

class DiscountFactory extends Factory
{
    protected $model = Discount::class;

    public function definition()
    {
        return [
            'code' => $this->faker->unique()->bothify('DISC-####'),
            'name' => $this->faker->words(3, true),
            'type' => $this->faker->randomElement(['percentage', 'fixed', 'buy_x_get_y']),
            'value' => $this->faker->numberBetween(5, 50),
            'min_purchase' => 0,
            'max_discount' => null,
            'is_active' => true,
            'start_date' => now()->subDay(),
            'end_date' => now()->addMonth(),
        ];
    }

    public function percentage()
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => 'percentage',
                'value' => 10, // 10%
            ];
        });
    }

    public function fixed()
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => 'fixed',
                'value' => 5000, // Rp 5.000
            ];
        });
    }

    public function buyXGetY()
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => 'buy_x_get_y',
                'buy_quantity' => 2,
                'get_quantity' => 1,
                'value' => 0,
            ];
        });
    }
}
