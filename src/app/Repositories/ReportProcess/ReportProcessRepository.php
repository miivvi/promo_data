<?php

namespace App\Repositories\ReportProcess;

use App\Models\ReportProcess;
use App\Repositories\AbstractRepository;
use Illuminate\Database\Eloquent\Collection;

class ReportProcessRepository extends AbstractRepository implements ReportProcessContract
{
    public function getAll(): Collection
    {
        return $this->query()
            ->with('processStatus')
            ->orderByDesc('rp_id')
            ->get();
    }

    public function getById(int $id)
    {
        return $this->query()->find($id);
    }

    protected function modelClass(): string
    {
        return ReportProcess::class;
    }
}
