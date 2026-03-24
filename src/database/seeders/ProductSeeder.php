<?php

namespace Database\Seeders;

use App\Models\Manufacturer;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    private const SEED_COUNT = 200;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $manufacturerIds = Manufacturer::query()->pluck('manufacturer_id');

        if ($manufacturerIds->isEmpty()) {
            $this->command?->warn('ProductSeeder: нет производителей — сначала запустите ManufacturerSeeder.');

            return;
        }

        $ids = $manufacturerIds->all();
        $count = count($ids);

        $demo = [
            ['name' => 'товар 1', 'category_id' => 1],
            ['name' => 'товар 2', 'category_id' => 2],
            ['name' => 'товар 3', 'category_id' => 3],
        ];

        foreach ($demo as $i => $item) {
            $manufacturerId = $ids[$i % $count];

            Product::query()->firstOrCreate(
                [
                    'product_name' => $item['name'],
                    'manufacturer_id' => $manufacturerId,
                ],
                [
                    'category_id' => $item['category_id'],
                ],
            );
        }

        Product::factory()
            ->count(self::SEED_COUNT)
            ->state(fn () => [
                'manufacturer_id' => $manufacturerIds->random(),
            ])
            ->create();
    }
}
