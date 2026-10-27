<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(rand(2, 4), true),
            'code' => strtoupper(fake()->unique()->bothify('PRD-####')),
            'price_per_unit' => fake()->randomFloat(2, 10, 500),
            'tax_percentage' => fake()->randomElement([5.00, 12.00, 18.00, 28.00]),
            'stock_on_hand' => fake()->numberBetween(0, 100),
        ];
    }

    public function lowStock(): static
    {
        return $this->state(fn () => [
            'stock_on_hand' => fake()->numberBetween(1, 4),
        ]);
    }

    public function outOfStock(): static
    {
        return $this->state(fn () => [
            'stock_on_hand' => 0,
        ]);
    }
}
