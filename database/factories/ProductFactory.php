<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word,
            'code' => $this->faker->unique()->bothify('PROD-####'),
            'outlet_id' => \App\Models\Outlet::factory(),
            'unit_id' => \App\Models\Unit::factory(),
            'selling_price' => $this->faker->numberBetween(10000, 100000),
            'hpp' => $this->faker->numberBetween(5000, 9000),
            'is_active' => true,
            'is_sellable' => true,
        ];
    }
}
