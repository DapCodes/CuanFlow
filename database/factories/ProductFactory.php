<?php

namespace Database\Factories;

use App\Models\Outlet;
use App\Models\Product;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
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
            'outlet_id' => Outlet::factory(),
            'unit_id' => Unit::factory(),
            'selling_price' => $this->faker->numberBetween(10000, 100000),
            'hpp' => $this->faker->numberBetween(5000, 9000),
            'is_active' => true,
            'is_sellable' => true,
        ];
    }
}
