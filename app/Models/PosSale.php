<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PosSale extends Model
{
    protected $fillable = [
        'sale_number',
        'user_id',
        'total',
        'payment_method',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'total' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PosSaleItem::class);
    }

    public static function generateSaleNumber(): string
    {
        $today = now()->format('Ymd');
        $lastSale = static::where('sale_number', 'like', "POS-{$today}-%")
            ->orderByDesc('sale_number')
            ->first();

        if ($lastSale) {
            $lastNumber = (int) substr($lastSale->sale_number, -4);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return "POS-{$today}-" . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }
}
