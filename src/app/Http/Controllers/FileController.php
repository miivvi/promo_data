<?php

namespace App\Http\Controllers;

use App\Http\Requests\DownloadFileRequest;
use App\Jobs\FileDownloadJob;
use App\Services\FileDownloadService;
use Illuminate\Http\RedirectResponse;

class FileController extends Controller
{
    public function __invoke(
        DownloadFileRequest $request,
        FileDownloadService $downloadService,
    ): RedirectResponse {
        $id = $request->validated('id');

        $token = $downloadService->createToken();

        FileDownloadJob::dispatch($id, $token)->onQueue('downloads');

        return redirect()->route('download_stream', ['token' => $token]);
    }
}
