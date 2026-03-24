<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

abstract class AbstractRepository
{
    protected Model $model;

    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->model = app($this->modelClass());
    }

    public function query(): Builder
    {
        return $this->model->query();
    }

    abstract protected function modelClass(): string;
}
