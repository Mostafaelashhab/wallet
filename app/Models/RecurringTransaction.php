<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecurringTransaction extends Model
{
    protected $fillable = [
        'user_id', 'account_id', 'category_id', 'type', 'transfer_to_account_id',
        'description', 'amount', 'currency', 'frequency',
        'next_run_at', 'end_date', 'active',
    ];

    protected $casts = [
        'next_run_at' => 'date',
        'end_date' => 'date',
        'amount' => 'decimal:2',
        'active' => 'bool',
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

    public function advance(): \Carbon\CarbonInterface
    {
        return match ($this->frequency) {
            'daily' => $this->next_run_at->addDay(),
            'weekly' => $this->next_run_at->addWeek(),
            'yearly' => $this->next_run_at->addYear(),
            default => $this->next_run_at->addMonth(),
        };
    }
}
