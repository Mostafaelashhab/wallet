<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    protected $fillable = [
        'user_id', 'account_id', 'type', 'transfer_to_account_id', 'category_id',
        'amount', 'currency', 'description', 'occurred_at', 'attachment_path',
        'location_lat', 'location_lng', 'location_name', 'expense_id', 'payment_id',
    ];

    protected $casts = [
        'occurred_at' => 'datetime',
        'amount' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function transferToAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'transfer_to_account_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function isIncome(): bool { return $this->type === 'income'; }
    public function isExpense(): bool { return $this->type === 'expense'; }
    public function isTransfer(): bool { return $this->type === 'transfer'; }

    public function signedAmount(): float
    {
        return match ($this->type) {
            'income'   => (float) $this->amount,
            'expense'  => -(float) $this->amount,
            'transfer' => 0,
            default    => 0,
        };
    }
}
