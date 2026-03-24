<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Table(
    name: 'process_status',
    key: 'ps_id',
    keyType: 'integer',
    incrementing: true,
    timestamps: false,
)]
class ProcessStatus extends Model
{
    use HasFactory;

    protected $primaryKey = 'ps_id';

    public $timestamps = false;

    protected $fillable = ['ps_name'];
}
