<?php

namespace App\Models;

use Database\Factories\PriceFactory;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Table(
    name: 'price',
    key: 'price_id',
    keyType: 'integer',
    incrementing: true,
    timestamps: false
)]
class Price extends Model
{
    /** @use HasFactory<PriceFactory> */
    use HasFactory;

    protected $primaryKey = 'price_id';

    public $timestamps = false;

    protected $fillable = [
        'product_id',
        'price',
        'price_date',
    ];

    protected $casts = [
        'price' => 'float',
        'price_date' => 'datetime',
        'product_id' => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }
}
