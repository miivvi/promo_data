<?php

namespace App\Jobs;

use App\Enums\ProcessStatus as ProcessStatusEnum;
use App\Models\ProcessStatus;
use App\Models\ReportProcess;
use App\Services\MinMaxPriceReportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class MinMaxPriceForLastSevenDaysReportJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 2;

    private const PERMANENT_EXCEPTIONS = [
        ModelNotFoundException::class,
    ];

    public function __construct(
        public int $categoryId,
        public int $rpId,
    ) {
    }

    public function backoff(): array
    {
        return [60, 120, 300, 600];
    }

    public function handle(MinMaxPriceReportService $reportService): void
    {
        try {
            $this->runReport($reportService);
        } catch (Throwable $e) {
            if ($this->isTemporaryError($e) && $this->attempts() < $this->tries) {
                $delay = $this->backoff()[$this->attempts() - 1] ?? 600;
                $this->release($delay);

                Log::warning('MinMaxPriceForLastSevenDaysReport: temporary error, releasing for retry', [
                    'category_id' => $this->categoryId,
                    'rp_id' => $this->rpId,
                    'attempt' => $this->attempts(),
                    'retry_in' => $delay,
                    'error' => $e->getMessage(),
                ]);

                return;
            }

            throw $e;
        }
    }

    private function runReport(MinMaxPriceReportService $reportService): void
    {
        $startTime = microtime(true);

        $reportProcess = ReportProcess::query()->findOrFail($this->rpId);
        $completedStatus = ProcessStatus::query()
            ->where('ps_name', ProcessStatusEnum::COMPLETED)
            ->firstOrFail();

        $filePath = $reportService->exportToCsv($this->categoryId);
        $execTime = round(microtime(true) - $startTime, 2);

        $reportProcess->update([
            'rp_pid' => (string) getmypid(),
            'rp_start_datetime' => $reportProcess->rp_start_datetime ?? now(),
            'rp_exec_time' => $execTime,
            'ps_id' => $completedStatus->ps_id,
            'rp_file_save_path' => $filePath,
        ]);

        Log::info('MinMaxPriceForLastSevenDaysReport: CSV generated', [
            'category_id' => $this->categoryId,
            'rp_id' => $this->rpId,
            'path' => $filePath,
            'exec_time' => $execTime,
        ]);
    }

    public function failed(?Throwable $exception): void
    {
        $failedStatus = ProcessStatus::query()
            ->where('ps_name', ProcessStatusEnum::FAILED)
            ->first();

        if ($failedStatus) {
            ReportProcess::query()
                ->where('rp_id', $this->rpId)
                ->update(['ps_id' => $failedStatus->ps_id]);
        }

        Log::error('MinMaxPriceForLastSevenDaysReport: job failed', [
            'category_id' => $this->categoryId,
            'rp_id' => $this->rpId,
            'error' => $exception?->getMessage(),
        ]);
    }

    private function isTemporaryError(Throwable $e): bool
    {
        foreach (self::PERMANENT_EXCEPTIONS as $class) {
            if ($e instanceof $class) {
                return false;
            }
        }

        return $e instanceof ConnectionException
            || $e instanceof \RuntimeException;
    }
}
