<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecurringExpense extends Model
{
    protected $fillable = [
        'group_id', 'payer_id', 'category_id', 'amount', 'currency', 'description',
        'frequency', 'next_run_at', 'end_date', 'split_type', 'split_data', 'active',
    ];

    protected $casts = [
        'next_run_at' => 'date',
        'end_date' => 'date',
        'split_data' => 'array',
        'active' => 'bool',
        'amount' => 'decimal:2',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function payer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'payer_id');
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
