<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseSplit;
use App\Models\Group;
use App\Models\Payment;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $me = $request->user();

        $owedToMe = (float) ExpenseSplit::query()
            ->whereHas('expense', fn ($q) => $q->where('payer_id', $me->id))
            ->where('user_id', '!=', $me->id)
            ->whereNull('settled_at')
            ->sum('amount');

        $iOwe = (float) ExpenseSplit::query()
            ->whereHas('expense', fn ($q) => $q->where('payer_id', '!=', $me->id))
            ->where('user_id', $me->id)
            ->whereNull('settled_at')
            ->sum('amount');

        $paymentsToMe = (float) Payment::where('payee_id', $me->id)->sum('amount');
        $paymentsByMe = (float) Payment::where('payer_id', $me->id)->sum('amount');

        $owedToMe = max(0, $owedToMe - $paymentsToMe);
        $iOwe = max(0, $iOwe - $paymentsByMe);

        $groups = $me->groups()->with('owner')->take(8)->get()->map(function ($g) use ($me) {
            $g->my_balance = $g->balanceFor($me);
            return $g;
        });

        $friends = $me->friends()->take(8)->get()->map(function ($f) use ($me) {
            $f->balance_with_me = $me->balanceWith($f);
            return $f;
        });

        return view('dashboard', [
            'owedToMe' => $owedToMe,
            'iOwe' => $iOwe,
            'groups' => $groups,
            'friends' => $friends,
        ]);
    }
}
