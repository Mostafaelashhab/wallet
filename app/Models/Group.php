<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Group extends Model
{
    protected $fillable = ['name', 'slug', 'owner_id', 'icon', 'color', 'currency', 'simplify_debts'];

    protected $casts = [
        'simplify_debts' => 'bool',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $g) {
            if (empty($g->slug)) {
                $g->slug = Str::slug($g->name) . '-' . Str::lower(Str::random(5));
            }
        });
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'group_members')->withPivot('role', 'joined_at');
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function goals(): HasMany
    {
        return $this->hasMany(Goal::class);
    }

    public function recurring(): HasMany
    {
        return $this->hasMany(RecurringExpense::class);
    }

    public function totalSpent(): float
    {
        return (float) $this->expenses()->sum('amount');
    }

    public function balanceFor(User $user): float
    {
        $owedToUser = (float) ExpenseSplit::query()
            ->whereHas('expense', fn ($q) => $q->where('group_id', $this->id)->where('payer_id', $user->id))
            ->where('user_id', '!=', $user->id)
            ->whereNull('settled_at')
            ->sum('amount');

        $userOwes = (float) ExpenseSplit::query()
            ->whereHas('expense', fn ($q) => $q->where('group_id', $this->id)->where('payer_id', '!=', $user->id))
            ->where('user_id', $user->id)
            ->whereNull('settled_at')
            ->sum('amount');

        $userPaid = (float) Payment::where('group_id', $this->id)->where('payer_id', $user->id)->sum('amount');
        $userReceived = (float) Payment::where('group_id', $this->id)->where('payee_id', $user->id)->sum('amount');

        return ($owedToUser - $userOwes) + ($userPaid - $userReceived);
    }
}
