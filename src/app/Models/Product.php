<?php

namespace App\Models;

use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Table(
    name: 'product',
    key: 'product_id',
    keyType: 'integer',
    incrementing: true,
    timestamps: false,
)]
class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory;

    protected $primaryKey = 'product_id';

    public $timestamps = false;

    protected $fillable = [
        'product_name',
        'category_id',
        'manufacturer_id',
    ];

    protected $casts = [
        'product_name' => 'string',
        'category_id' => 'integer',
        'manufacturer_id' => 'integer',
    ];

    public function manufacturer(): BelongsTo
    {
        return $this->belongsTo(Manufacturer::class, 'manufacturer_id', 'manufacturer_id');
    }

    public function prices(): HasMany
    {
        return $this->hasMany(Price::class, 'product_id', 'product_id');
    }
}
