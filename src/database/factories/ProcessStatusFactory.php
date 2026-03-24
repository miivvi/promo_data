<?php

namespace Database\Factories;

use App\Models\ProcessStatus;
use App\Enums\ProcessStatus as ProcessStatusEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProcessStatus>
 */
class ProcessStatusFactory extends Factory
{
    protected $model = ProcessStatus::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ps_name' => fake()->randomElement(ProcessStatusEnum::values()),
        ];
    }
}
