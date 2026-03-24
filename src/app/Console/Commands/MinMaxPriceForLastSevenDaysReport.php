<?php

namespace App\Console\Commands;

use App\Jobs\MinMaxPriceForLastSevenDaysReportJob;
use App\Models\ProcessStatus;
use App\Models\ReportProcess;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Enums\ProcessStatus as ProcessStatusEnum;

#[Signature('app:report {category_id}')]
#[Description('Command to get a report on minimum and maximum prices for the last 7 days.')]
class MinMaxPriceForLastSevenDaysReport extends Command
{
    private const LOG_MESSAGES = [
        'Missing argument category_id',
        'Invalid argument category_id',
        'Query returned 0 results by category_id',
        'Command finished',
    ];

    /**
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     * @throws \Throwable
     */
    public function handle(): void
    {
        $categoryId = $this->argument('category_id');

        if ($categoryId === null) {
            Log::warning(self::LOG_MESSAGES[0]);
            $this->fail(self::LOG_MESSAGES[0]);
        }

        if (!is_numeric($categoryId)) {
            Log::warning(self::LOG_MESSAGES[1]);
            $this->fail(self::LOG_MESSAGES[1]);
        }

        $categoryId = (int) $categoryId;
        $status = ProcessStatus::query()
            ->where('ps_name', ProcessStatusEnum::WAITING)
            ->firstOrFail();

        $reportProcess = ReportProcess::query()->create([
            'rp_pid' => null,
            'rp_start_datetime' => now(),
            'rp_exec_time' => null,
            'ps_id' => $status->ps_id,
            'rp_file_save_path' => null,
        ]);


        MinMaxPriceForLastSevenDaysReportJob::dispatch($categoryId, $reportProcess->rp_id)
            ->onQueue('reports');

        $this->info("Report export queued for category_id={$categoryId}, rp_id={$reportProcess->rp_id} (queue: reports)");
    }
}
