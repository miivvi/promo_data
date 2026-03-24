<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Table(
    name: 'report_process',
    key: 'rp_id',
    keyType: 'integer',
    incrementing: true,
    timestamps: false,
)]
class ReportProcess extends Model
{
    protected $primaryKey = 'rp_id';

    public $timestamps = false;

    protected $fillable = [
        'rp_pid',
        'rp_start_datetime',
        'rp_exec_time',
        'ps_id',
        'rp_file_save_path',
    ];

    protected $casts = [
        'rp_start_datetime' => 'datetime',
        'rp_exec_time' => 'float',
    ];

    public function processStatus(): BelongsTo
    {
        return $this->belongsTo(ProcessStatus::class, 'ps_id', 'ps_id');
    }
}
