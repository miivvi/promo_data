<?php

namespace App\Models;

use Database\Factories\ManufacturerFactory;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Table(
    name: 'manufacturer',
    key: 'manufacturer_id',
    keyType: 'integer',
    incrementing: true,
    timestamps: false,
)]
class Manufacturer extends Model
{
    /** @use HasFactory<ManufacturerFactory> */
    use HasFactory;

    protected $primaryKey = 'manufacturer_id';

    protected $fillable = [
        'manufacturer_name',
    ];

    protected $casts = [
        'manufacturer_id' => 'integer',
        'manufacturer_name' => 'string',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'manufacturer_id', 'manufacturer_id');
    }
}
