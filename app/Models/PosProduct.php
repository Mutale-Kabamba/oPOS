<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PosProduct extends Model
{
    protected $fillable = [
        'name',
        'sku',
        'price',
        'category',
        'stock',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function saleItems(): HasMany
    {
        return $this->hasMany(PosSaleItem::class);
    }
}
