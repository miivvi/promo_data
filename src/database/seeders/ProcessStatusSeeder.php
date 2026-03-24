<?php

namespace Database\Seeders;

use App\Enums\ProcessStatus as ProcessStatusEnum;
use App\Models\ProcessStatus;
use Illuminate\Database\Seeder;

class ProcessStatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = ProcessStatusEnum::values();

        foreach ($statuses as $name) {
            ProcessStatus::query()->firstOrCreate(
                ['ps_name' => $name],
                ['ps_name' => $name],
            );
        }
    }
}
