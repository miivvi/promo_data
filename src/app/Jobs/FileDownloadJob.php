<?php

namespace App\Jobs;

use App\Models\ReportProcess;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use PDOException;
use Throwable;

class FileDownloadJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private const CACHE_PREFIX = 'download:';

    private const CACHE_TTL = 300;

    public int $tries = 5;

    private const PERMANENT_EXCEPTIONS = [
        ModelNotFoundException::class,
    ];

    public function __construct(
        public int $reportProcessId,
        public string $downloadToken,
    ) {
    }

    public function backoff(): array
    {
        return [60, 120, 300, 600];
    }

    public function handle(): void
    {
        try {
            $this->prepareDownload();
        } catch (Throwable $e) {
            if ($this->isTemporaryError($e) && $this->attempts() < $this->tries) {
                $delay = $this->backoff()[$this->attempts() - 1] ?? 600;
                $this->release($delay);

                Log::warning('FileDownloadJob: temporary error, releasing for retry', [
                    'rp_id' => $this->reportProcessId,
                    'attempt' => $this->attempts(),
                    'retry_in' => $delay,
                    'error' => $e->getMessage(),
                ]);

                return;
            }

            throw $e;
        }
    }

    private function prepareDownload(): void
    {
        $reportProcess = ReportProcess::query()->findOrFail($this->reportProcessId);

        if (!$reportProcess->rp_file_save_path) {
            $this->storeFailure('file_missing');
            Log::error('FileDownloadJob: rp_file_save_path отсутствует', ['rp_id' => $this->reportProcessId]);
            return;
        }

        $path = $reportProcess->rp_file_save_path;

        if (!is_readable($path) || !is_file($path)) {
            $this->storeFailure('file_unreadable');
            Log::error('FileDownloadJob: файл недоступен', ['path' => $path]);
            return;
        }

        $this->storeSuccess($path);

        Log::info('File download prepared', [
            'rp_id' => $this->reportProcessId,
            'token' => $this->downloadToken,
        ]);
    }

    private function storeSuccess(string $path): void
    {
        Cache::put(self::CACHE_PREFIX . $this->downloadToken, [
            'status' => 'ready',
            'path' => $path,
        ], self::CACHE_TTL);
    }

    private function storeFailure(string $reason): void
    {
        Cache::put(self::CACHE_PREFIX . $this->downloadToken, [
            'status' => 'failed',
            'reason' => $reason,
        ], 60);
    }

    public function failed(?Throwable $exception): void
    {
        $this->storeFailure('job_failed');

        Log::error('FileDownloadJob: job failed', [
            'rp_id' => $this->reportProcessId,
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

        return $e instanceof PDOException
            || $e instanceof QueryException
            || $e instanceof \RuntimeException;
    }
}
