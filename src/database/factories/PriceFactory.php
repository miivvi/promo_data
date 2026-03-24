<?php

namespace Database\Factories;

use App\Models\Price;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Price>
 */
class PriceFactory extends Factory
{
    protected $model = Price::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'price' => fake()->randomFloat(2, 10, 99_999.99),
            'price_date' => fake()->dateTimeBetween('-7 days', 'now'),
        ];
    }

    public function forProduct(Product|int $product): static
    {
        $id = $product instanceof Product ? $product->product_id : $product;

        return $this->state(fn (array $attributes) => [
            'product_id' => $id,
        ]);
    }
}
