<?php

namespace Database\Seeders;

use App\Models\Price;
use App\Models\Product;
use Illuminate\Database\Seeder;

class PriceSeeder extends Seeder
{
    public function run(): void
    {
        if (Product::query()->doesntExist()) {
            $this->command?->warn('PriceSeeder: нет товаров — сначала запустите ProductSeeder.');

            return;
        }

        foreach (Product::query()->cursor() as $product) {
            $rows = fake()->numberBetween(1, 4);

            Price::factory()
                ->count($rows)
                ->forProduct($product)
                ->create();
        }
    }
}
