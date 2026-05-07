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

    public function accounts(): HasMany
    {
        return $this->hasMany(Account::class)->whereNull('archived_at')->orderBy('id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class)->orderByDesc('occurred_at');
    }

    public function netWorth(): float
    {
        return (float) $this->accounts()->where('include_in_total', true)->get()->sum(fn ($a) => $a->balance());
    }

    public function monthSummary(?\Carbon\CarbonInterface $month = null): array
    {
        $month = $month ?? now();
        $start = $month->copy()->startOfMonth();
        $end   = $month->copy()->endOfMonth();
        $income = (float) Transaction::where('user_id', $this->id)
            ->where('type', 'income')
            ->whereBetween('occurred_at', [$start, $end])->sum('amount');
        $expense = (float) Transaction::where('user_id', $this->id)
            ->where('type', 'expense')
            ->whereBetween('occurred_at', [$start, $end])->sum('amount');
        return ['income' => $income, 'expense' => $expense, 'net' => $income - $expense];
    }

    public function ensureDefaultAccounts(): void
    {
        if ($this->accounts()->count() > 0) return;
        $defs = [
            ['name' => 'كاش',           'type' => 'cash',   'icon' => '💵', 'color' => '#16A34A', 'institution' => null],
            ['name' => 'حساب بنكي',     'type' => 'bank',   'icon' => '🏦', 'color' => '#3B82F6', 'institution' => null],
            ['name' => 'فودافون كاش',   'type' => 'wallet', 'icon' => '📱', 'color' => '#EF4444', 'institution' => 'Vodafone Cash'],
            ['name' => 'إنستا باي',     'type' => 'wallet', 'icon' => '🟣', 'color' => '#8B5CF6', 'institution' => 'InstaPay'],
        ];
        foreach ($defs as $d) {
            Account::create([
                'user_id' => $this->id,
                'currency' => $this->currency ?? 'EGP',
                'opening_balance' => 0,
                ...$d,
            ]);
        }
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
