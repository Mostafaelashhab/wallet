<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Budget extends Model
{
    protected $fillable = ['user_id', 'category_id', 'amount', 'currency', 'period', 'rollover'];

    protected $casts = [
        'amount' => 'decimal:2',
        'rollover' => 'bool',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function spent(?Carbon $month = null): float
    {
        $month = $month ?? now();
        $start = $month->copy()->startOfMonth();
        $end   = $month->copy()->endOfMonth();
        return (float) Transaction::where('user_id', $this->user_id)
            ->where('category_id', $this->category_id)
            ->where('type', 'expense')
            ->whereBetween('occurred_at', [$start, $end])
            ->sum('amount');
    }

    public function progress(?Carbon $month = null): array
    {
        $spent = $this->spent($month);
        $amount = (float) $this->amount;
        $pct = $amount > 0 ? min(100, round(($spent / $amount) * 100)) : 0;
        return [
            'spent' => $spent,
            'amount' => $amount,
            'remaining' => max(0, $amount - $spent),
            'percent' => $pct,
            'state' => $pct >= 100 ? 'over' : ($pct >= 80 ? 'warn' : 'ok'),
        ];
    }
}
