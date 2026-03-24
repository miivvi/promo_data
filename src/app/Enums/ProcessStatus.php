<?php

declare(strict_types=1);

namespace App\Enums;

final class ProcessStatus
{
    public const WAITING = 'waiting';
    public const COMPLETED = 'completed';
    public const FAILED = 'failed';

    public static function values(): array
    {
        return [
            1 => self::WAITING,
            2 => self::COMPLETED,
            3 => self::FAILED,
        ];
    }
}
