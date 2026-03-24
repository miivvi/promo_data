<?php

namespace App\Repositories\ReportProcess;

use App\Models\ReportProcess;
use Illuminate\Database\Eloquent\Collection;

interface ReportProcessContract
{
    public function getAll();

    public function getById(int $id);
}
