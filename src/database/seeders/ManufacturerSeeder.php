<?php

namespace Database\Seeders;

use App\Models\Manufacturer;
use Illuminate\Database\Seeder;

class ManufacturerSeeder extends Seeder
{
    private const SEED_COUNT = 8;

    private const NAMES = [
        'ООО «Ромашка»',
        'ЗАО «ТехноПром»',
        'ИП Иванов',
    ];

    public function run(): void
    {
        foreach (self::NAMES as $name) {
            Manufacturer::query()->firstOrCreate(
                ['manufacturer_name' => $name],
            );
        }

        Manufacturer::factory()
            ->count(self::SEED_COUNT)
            ->create();
    }
}
