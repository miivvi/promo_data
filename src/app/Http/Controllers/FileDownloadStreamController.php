<?php

namespace App\Http\Controllers;

use App\Services\FileDownloadService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FileDownloadStreamController extends Controller
{
    private const CHUNK_SIZE = 1024 * 1024;

    public function __invoke(Request $request, FileDownloadService $downloadService): Response
    {
        $token = $request->route('token');
        $data = $downloadService->getByToken($token);

        if ($data === null) {
            abort(404, 'Токен не найден или истёк');
        }

        if ($downloadService->isFailed($token)) {
            abort(404, 'Файл недоступен');
        }

        if (!$downloadService->isReady($token)) {
            $retryUrl = route('download_stream', ['token' => $token]);
            $html = sprintf(
                '<!DOCTYPE html><html><head><meta charset="utf-8"><meta http-equiv="refresh" content="2;url=%s"></head><body><p>Подготовка файла для скачивания...</p></body></html>',
                htmlspecialchars($retryUrl),
            );

            return response($html, 503)->header('Retry-After', '2');
        }

        $path = $data['path'] ?? '';
        if (!$path || !is_readable($path) || !is_file($path)) {
            abort(404, 'Файл не найден');
        }

        $filename = basename($path);

        return response()->streamDownload(
            function () use ($path): void {
                $handle = fopen($path, 'rb');
                if (!$handle) {
                    return;
                }
                try {
                    while (!feof($handle)) {
                        echo fread($handle, self::CHUNK_SIZE);
                        flush();
                    }
                } finally {
                    fclose($handle);
                }
            },
            $filename,
            [
                'Content-Type' => 'application/octet-stream',
                'Content-Disposition' => 'attachment; filename="' . addslashes($filename) . '"',
            ]
        );
    }
}
