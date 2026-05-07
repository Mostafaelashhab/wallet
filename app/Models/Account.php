<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model
{
    protected $fillable = [
        'user_id', 'name', 'type', 'institution', 'currency',
        'opening_balance', 'color', 'icon', 'include_in_total', 'archived_at',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'include_in_total' => 'bool',
        'archived_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class)->orderByDesc('occurred_at');
    }

    public function balance(): float
    {
        $income = (float) Transaction::where('account_id', $this->id)->where('type', 'income')->sum('amount');
        $expense = (float) Transaction::where('account_id', $this->id)->where('type', 'expense')->sum('amount');
        $transferOut = (float) Transaction::where('account_id', $this->id)->where('type', 'transfer')->sum('amount');
        $transferIn = (float) Transaction::where('transfer_to_account_id', $this->id)->where('type', 'transfer')->sum('amount');

        return (float) $this->opening_balance + $income - $expense - $transferOut + $transferIn;
    }

    public static function typeOptions(): array
    {
        return [
            'cash'    => ['label_ar' => 'كاش',         'label_en' => 'Cash',     'icon' => '💵', 'color' => '#16A34A'],
            'bank'    => ['label_ar' => 'حساب بنكي',   'label_en' => 'Bank',     'icon' => '🏦', 'color' => '#3B82F6'],
            'wallet'  => ['label_ar' => 'محفظة موبايل','label_en' => 'Wallet',   'icon' => '📱', 'color' => '#EF4444'],
            'card'    => ['label_ar' => 'بطاقة ائتمان','label_en' => 'Card',     'icon' => '💳', 'color' => '#8B5CF6'],
            'savings' => ['label_ar' => 'توفير',       'label_en' => 'Savings',  'icon' => '🐷', 'color' => '#F97316'],
        ];
    }
}
