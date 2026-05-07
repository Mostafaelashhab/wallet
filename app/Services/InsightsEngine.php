<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;

class InsightsEngine
{
    /**
     * Returns 0..N insight cards for the dashboard.
     * Each: ['icon' => 'name', 'tone' => 'emerald|rose|sky|amber', 'title' => '...', 'body' => '...']
     */
    public static function forUser(User $user): array
    {
        $insights = [];
        $now = now();

        // 1. Spike detection: this week vs last week, by category
        $weekStart  = $now->copy()->startOfWeek();
        $prevStart  = $now->copy()->subWeek()->startOfWeek();
        $prevEnd    = $now->copy()->subWeek()->endOfWeek();

        $thisWeek = Transaction::where('user_id', $user->id)
            ->where('type', 'expense')
            ->whereBetween('occurred_at', [$weekStart, $now])
            ->with('category')->get()
            ->groupBy('category_id')
            ->map(fn ($g) => (float) $g->sum('amount'));
        $lastWeek = Transaction::where('user_id', $user->id)
            ->where('type', 'expense')
            ->whereBetween('occurred_at', [$prevStart, $prevEnd])
            ->get()
            ->groupBy('category_id')
            ->map(fn ($g) => (float) $g->sum('amount'));

        foreach ($thisWeek as $catId => $amount) {
            $prev = (float) ($lastWeek[$catId] ?? 0);
            if ($prev > 0 && $amount > $prev * 1.4 && $amount > 100) {
                $cat = $thisWeek[$catId][0]->category ?? null;
                if ($cat) {
                    $pct = round((($amount - $prev) / $prev) * 100);
                    $insights[] = [
                        'icon' => 'flame', 'tone' => 'rose',
                        'title' => __('app.insight_spike_title', ['cat' => $cat->name()]),
                        'body' => __('app.insight_spike_body', ['pct' => $pct, 'amount' => number_format($amount, 0)]),
                    ];
                }
            }
        }

        // 2. Account about to run dry
        foreach ($user->accounts as $acc) {
            $bal = $acc->balance();
            if ($bal <= 0) continue;
            $monthlyOut = (float) Transaction::where('user_id', $user->id)
                ->where('account_id', $acc->id)
                ->where('type', 'expense')
                ->whereBetween('occurred_at', [$now->copy()->subDays(30), $now])
                ->sum('amount');
            if ($monthlyOut <= 0) continue;
            $dailyOut = $monthlyOut / 30;
            if ($dailyOut <= 0) continue;
            $daysLeft = (int) floor($bal / $dailyOut);
            if ($daysLeft <= 14) {
                $insights[] = [
                    'icon' => 'pin', 'tone' => 'amber',
                    'title' => __('app.insight_lowbalance_title', ['name' => $acc->name]),
                    'body' => __('app.insight_lowbalance_body', ['days' => $daysLeft]),
                ];
            }
        }

        // 3. Best week (positive)
        $monthIncome = (float) Transaction::where('user_id', $user->id)
            ->where('type', 'income')
            ->whereBetween('occurred_at', [$now->copy()->startOfMonth(), $now])->sum('amount');
        $monthExpense = (float) Transaction::where('user_id', $user->id)
            ->where('type', 'expense')
            ->whereBetween('occurred_at', [$now->copy()->startOfMonth(), $now])->sum('amount');
        if ($monthIncome > 0 && $monthExpense > 0 && $monthIncome > $monthExpense * 1.5) {
            $saved = $monthIncome - $monthExpense;
            $insights[] = [
                'icon' => 'sparkles', 'tone' => 'emerald',
                'title' => __('app.insight_savings_title'),
                'body' => __('app.insight_savings_body', ['amount' => number_format($saved, 0)]),
            ];
        }

        return array_slice($insights, 0, 3);
    }
}
