<?php

namespace App\Models;

use App\Services\JournalPostingService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Transaction extends Model
{
    public const PAYMENT_STATUS_PENDING = 'pending';

    public const PAYMENT_STATUS_PARTIALLY_PAID = 'partially_paid';

    public const PAYMENT_STATUS_PAID = 'paid';

    protected static function booted(): void
    {
        static::saving(function (Transaction $transaction): void {
            if (! $transaction->parent_transaction_id) {
                return;
            }

            $parentUserId = $transaction->parentTransaction()->value('user_id');

            if ($parentUserId) {
                $transaction->user_id = $parentUserId;
            }
        });

        static::saved(function (Transaction $transaction): void {
            app(JournalPostingService::class)->sync($transaction);
        });
    }

    protected $fillable = [
        'amount',
        'date',
        'account_id',
        'supplier_id',
        'parent_transaction_id',
        'category_id',
        'description',
        'payment_status',
        'is_reconciled',
        'reconciled_at',
        'user_id',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'amount' => 'decimal:2',
            'is_reconciled' => 'boolean',
            'reconciled_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function parentTransaction(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_transaction_id');
    }

    public function paymentTransactions(): HasMany
    {
        return $this->hasMany(self::class, 'parent_transaction_id');
    }

    public function journalEntry(): HasOne
    {
        return $this->hasOne(JournalEntry::class);
    }

    public function paidAmount(): float
    {
        $paidAmount = $this->paid_amount ?? null;

        if ($paidAmount !== null) {
            return round((float) $paidAmount, 2);
        }

        return round((float) $this->paymentTransactions()->sum('amount'), 2);
    }

    public function remainingAmount(): float
    {
        return round(max((float) $this->amount - $this->paidAmount(), 0), 2);
    }

    public function displayAmount(): float
    {
        if ($this->parent_transaction_id === null && $this->account?->type === 'liability') {
            return $this->remainingAmount();
        }

        return round((float) $this->amount, 2);
    }

    public function syncPaymentStatus(): void
    {
        if ($this->parent_transaction_id) {
            return;
        }

        $paidAmount = $this->paidAmount();
        $transactionAmount = round((float) $this->amount, 2);

        $paymentStatus = match (true) {
            $paidAmount <= 0 => self::PAYMENT_STATUS_PENDING,
            $paidAmount >= $transactionAmount => self::PAYMENT_STATUS_PAID,
            default => self::PAYMENT_STATUS_PARTIALLY_PAID,
        };

        if ($this->payment_status !== $paymentStatus) {
            $this->forceFill(['payment_status' => $paymentStatus])->saveQuietly();
        }
    }
}
