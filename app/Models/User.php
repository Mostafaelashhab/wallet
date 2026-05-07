<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'avatar', 'phone', 'locale', 'currency', 'instapay_handle', 'vodafone_cash', 'color'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'group_members')
            ->withPivot('role', 'joined_at')
            ->orderByDesc('groups.updated_at');
    }

    public function ownedGroups(): HasMany
    {
        return $this->hasMany(Group::class, 'owner_id');
    }

    public function expensesPaid(): HasMany
    {
        return $this->hasMany(Expense::class, 'payer_id');
    }

    public function splits(): HasMany
    {
        return $this->hasMany(ExpenseSplit::class);
    }

    public function paymentsMade(): HasMany
    {
        return $this->hasMany(Payment::class, 'payer_id');
    }

    public function paymentsReceived(): HasMany
    {
        return $this->hasMany(Payment::class, 'payee_id');
    }

    public function friendships(): HasMany
    {
        return $this->hasMany(Friendship::class, 'requester_id');
    }

    public function pushSubscriptions(): HasMany
    {
        return $this->hasMany(PushSubscription::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }

    public function avatarUrl(): string
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }
        $initial = mb_strtoupper(mb_substr($this->name ?? '?', 0, 1));
        $color = ltrim($this->color ?? '#FF6B35', '#');
        return "https://ui-avatars.com/api/?name={$initial}&background={$color}&color=fff&bold=true&size=128";
    }

    public function friends()
    {
        return User::query()
            ->whereIn('id', function ($q) {
                $q->select('addressee_id')->from('friendships')
                    ->where('requester_id', $this->id)->where('status', 'accepted');
            })
            ->orWhereIn('id', function ($q) {
                $q->select('requester_id')->from('friendships')
                    ->where('addressee_id', $this->id)->where('status', 'accepted');
            });
    }

    public function balanceWith(User $other): float
    {
        // positive => other owes me; negative => I owe other
        $owedToMe = (float) ExpenseSplit::query()
            ->whereHas('expense', fn ($q) => $q->where('payer_id', $this->id))
            ->where('user_id', $other->id)
            ->whereNull('settled_at')
            ->sum('amount');

        $iOwe = (float) ExpenseSplit::query()
            ->whereHas('expense', fn ($q) => $q->where('payer_id', $other->id))
            ->where('user_id', $this->id)
            ->whereNull('settled_at')
            ->sum('amount');

        $paymentsMine = (float) Payment::where('payer_id', $this->id)->where('payee_id', $other->id)->sum('amount');
        $paymentsTheirs = (float) Payment::where('payer_id', $other->id)->where('payee_id', $this->id)->sum('amount');

        return ($owedToMe - $iOwe) + ($paymentsTheirs - $paymentsMine);
    }
}
