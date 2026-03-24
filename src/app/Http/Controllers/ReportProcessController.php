<?php

namespace App\Http\Controllers;

use App\Repositories\ReportProcess\ReportProcessContract;
use App\Services\ReportProcessViewTransformer;
use Illuminate\View\View;

class ReportProcessController extends Controller
{
    public function __invoke(
        ReportProcessContract $contract,
        ReportProcessViewTransformer $transformer,
    ): View {
        $processes = $transformer->transformCollection($contract->getAll());

        return view('reports.process_control', [
            'title' => 'Контроль выполнения процессов',
            'data' => $processes,
        ]);
    }
}
