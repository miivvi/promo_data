<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class FileDownloadService
{
    private const CACHE_PREFIX = 'download:';

    private const CACHE_TTL = 300;

    public function createToken(): string
    {
        $token = Str::uuid()->toString();
        Cache::put(self::CACHE_PREFIX . $token, ['status' => 'pending'], self::CACHE_TTL);

        return $token;
    }

    /**
     * @return array{status: string, path?: string, reason?: string}|null
     */
    public function getByToken(string $token): ?array
    {
        $data = Cache::get(self::CACHE_PREFIX . $token);

        return is_array($data) ? $data : null;
    }

    public function isReady(string $token): bool
    {
        $data = $this->getByToken($token);

        return ($data['status'] ?? '') === 'ready' && !empty($data['path']);
    }

    public function isFailed(string $token): bool
    {
        return ($this->getByToken($token)['status'] ?? '') === 'failed';
    }
}
