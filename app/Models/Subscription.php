<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{
    protected $fillable = [
        'user_id', 'account_id', 'category_id', 'name', 'icon_name', 'color',
        'amount', 'currency', 'frequency', 'next_billing_at', 'started_at',
        'cancel_url', 'note', 'active', 'auto_log',
    ];

    protected $casts = [
        'next_billing_at' => 'date',
        'started_at' => 'date',
        'amount' => 'decimal:2',
        'active' => 'bool',
        'auto_log' => 'bool',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function monthlyEquivalent(): float
    {
        return match ($this->frequency) {
            'yearly' => (float) $this->amount / 12,
            'weekly' => (float) $this->amount * 4.345,
            default  => (float) $this->amount,
        };
    }

    public function advance(): \Carbon\CarbonInterface
    {
        return match ($this->frequency) {
            'weekly' => $this->next_billing_at->addWeek(),
            'yearly' => $this->next_billing_at->addYear(),
            default => $this->next_billing_at->addMonth(),
        };
    }

    public function daysUntilBilling(): int
    {
        return (int) now()->startOfDay()->diffInDays($this->next_billing_at, false);
    }
}
