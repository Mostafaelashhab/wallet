<?php

namespace App\Http\Controllers;

use App\Models\NetWorthSnapshot;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Services\InsightsEngine;
use App\Services\NetWorthSnapshotter;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $me = $request->user();
        $me->ensureDefaultAccounts();

        if (!$me->onboarded_at) {
            return redirect()->route('onboarding');
        }

        $accounts = $me->accounts()->get()->each(fn ($a) => $a->current_balance = $a->balance());
        $netWorth = $accounts->where('include_in_total', true)->sum->current_balance;
        $monthSummary = $me->monthSummary();

        $recentTx = Transaction::where('user_id', $me->id)
            ->with('account', 'category', 'transferToAccount')
            ->latest('occurred_at')->take(8)->get();

        // Net worth sparkline (30 days)
        $snapshots = NetWorthSnapshot::where('user_id', $me->id)
            ->where('date', '>=', now()->subDays(30)->toDateString())
            ->orderBy('date')->pluck('total', 'date');
        if ($snapshots->isEmpty()) {
            NetWorthSnapshotter::captureForUser($me);
            $snapshots = NetWorthSnapshot::where('user_id', $me->id)->pluck('total', 'date');
        }
        $sparkline = $snapshots->values()->all();

        // Active budgets
        $budgets = $me->budgets()->with('category')->get()->take(4);
        foreach ($budgets as $b) $b->p = $b->progress();

        // Upcoming subscriptions (7 days)
        $upcoming = Subscription::where('user_id', $me->id)->where('active', true)
            ->whereBetween('next_billing_at', [now()->toDateString(), now()->addDays(7)->toDateString()])
            ->orderBy('next_billing_at')->take(3)->get();
        $monthlySubs = (float) $me->subscriptions()->get()->sum(fn ($s) => $s->monthlyEquivalent());

        // Smart insights
        $insights = InsightsEngine::forUser($me);

        return view('dashboard', compact(
            'accounts', 'netWorth', 'monthSummary', 'recentTx',
            'sparkline', 'budgets', 'upcoming', 'monthlySubs', 'insights'
        ));
    }
}
