<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $me = $request->user();
        $me->ensureDefaultAccounts();

        $accounts = $me->accounts()->get()->each(fn ($a) => $a->current_balance = $a->balance());
        $netWorth = $accounts->where('include_in_total', true)->sum->current_balance;
        $monthSummary = $me->monthSummary();

        $recentTx = Transaction::where('user_id', $me->id)
            ->with('account', 'category', 'transferToAccount')
            ->latest('occurred_at')->take(8)->get();

        // Group/friends section (kept as a secondary panel)
        $groups = $me->groups()->take(4)->get()->map(function ($g) use ($me) {
            $g->my_balance = $g->balanceFor($me);
            return $g;
        });

        return view('dashboard', [
            'accounts' => $accounts,
            'netWorth' => $netWorth,
            'monthSummary' => $monthSummary,
            'recentTx' => $recentTx,
            'groups' => $groups,
        ]);
    }
}
