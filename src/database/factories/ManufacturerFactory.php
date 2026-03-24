<?php

namespace Database\Factories;

use App\Models\Manufacturer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Manufacturer>
 */
class ManufacturerFactory extends Factory
{
    protected $model = Manufacturer::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'manufacturer_name' => fake()->company(),
        ];
    }

    public function named(string $name): static
    {
        return $this->state(fn (array $attributes) => [
            'manufacturer_name' => $name,
        ]);
    }
}
