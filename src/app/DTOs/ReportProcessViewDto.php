<?php

declare(strict_types=1);

namespace App\DTOs;

readonly class ReportProcessViewDto
{
    public function __construct(
        public int $id,
        public ?string $date,
        public ?string $startTime,
        public ?float $execTime,
        public ?string $pid,
        public ?string $name,
        public string $file,
        public bool $isFailProcess,
    ) {
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'date' => $this->date,
            'start_time' => $this->startTime,
            'exec_time' => $this->execTime,
            'pid' => $this->pid,
            'name' => $this->name,
            'file' => $this->file,
            'isFailProcess' => $this->isFailProcess,
        ];
    }
}
