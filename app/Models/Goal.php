<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Goal extends Model
{
    protected $fillable = ['group_id', 'owner_id', 'name', 'target_amount', 'currency', 'deadline', 'icon', 'color'];

    protected $casts = [
        'deadline' => 'date',
        'target_amount' => 'decimal:2',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function contributions(): HasMany
    {
        return $this->hasMany(GoalContribution::class);
    }

    public function progress(): float
    {
        return (float) $this->contributions()->sum('amount');
    }

    public function percent(): int
    {
        return $this->target_amount > 0 ? (int) min(100, round($this->progress() / $this->target_amount * 100)) : 0;
    }
}
