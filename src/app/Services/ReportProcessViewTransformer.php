<?php

namespace App\Services;

use App\DTOs\ReportProcessViewDto;
use App\Enums\ProcessStatus;
use App\Models\ReportProcess;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as BaseCollection;

class ReportProcessViewTransformer
{
    public function transform(ReportProcess $process): ReportProcessViewDto
    {
        $isFail = $this->isFailProcess($process);
        $filePath = $process->rp_file_save_path;

        return new ReportProcessViewDto(
            id: $process->rp_id,
            date: $process->rp_start_datetime?->format('Y-m-d'),
            startTime: $process->rp_start_datetime?->format('H:i:s'),
            execTime: $process->rp_exec_time,
            pid: $process->rp_pid,
            name: $process->processStatus?->ps_name,
            file: $isFail ? '' : (is_string($filePath) ? basename($filePath) : ''),
            isFailProcess: $isFail,
        );
    }

    /**
     * @param  Collection<int, ReportProcess>  $processes
     * @return BaseCollection<int, ReportProcessViewDto>
     */
    public function transformCollection(Collection $processes): BaseCollection
    {
        return $processes->map(fn (ReportProcess $p) => $this->transform($p));
    }

    protected function isFailProcess(ReportProcess $process): bool
    {
        return ProcessStatus::values()[$process->processStatus?->ps_id] === ProcessStatus::FAILED;
    }
}
