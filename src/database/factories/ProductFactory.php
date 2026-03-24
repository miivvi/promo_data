<?php

namespace Database\Factories;

use App\Models\Manufacturer;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_name' => fake()->words(3, true),
            'category_id' => fake()->numberBetween(1, 100),
            'manufacturer_id' => Manufacturer::query()->inRandomOrder()->value('manufacturer_id')
                ?? Manufacturer::factory(),
        ];
    }

    public function forManufacturer(Manufacturer|int $manufacturer): static
    {
        $id = $manufacturer instanceof Manufacturer
            ? $manufacturer->manufacturer_id
            : $manufacturer;

        return $this->state(fn (array $attributes) => [
            'manufacturer_id' => $id,
        ]);
    }
}
