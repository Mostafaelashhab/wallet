<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Expense extends Model
{
    protected $fillable = [
        'group_id', 'payer_id', 'category_id', 'amount', 'currency',
        'description', 'occurred_at', 'split_type', 'receipt_path',
        'location_lat', 'location_lng', 'location_name', 'recurring_id',
    ];

    protected $casts = [
        'occurred_at' => 'datetime',
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

    public function splits(): HasMany
    {
        return $this->hasMany(ExpenseSplit::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class)->latest();
    }

    public function involves(User $user): bool
    {
        return $this->payer_id === $user->id || $this->splits->contains('user_id', $user->id);
    }

    public function shareFor(User $user): float
    {
        return (float) ($this->splits->firstWhere('user_id', $user->id)?->amount ?? 0);
    }
}
