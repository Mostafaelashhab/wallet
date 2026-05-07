<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->query('month')
            ? Carbon::createFromFormat('Y-m', $request->query('month'))->startOfMonth()
            : now()->startOfMonth();
        $end = $month->copy()->endOfMonth();
        $me = $request->user();

        $income = (float) Transaction::where('user_id', $me->id)->where('type', 'income')
            ->whereBetween('occurred_at', [$month, $end])->sum('amount');
        $expense = (float) Transaction::where('user_id', $me->id)->where('type', 'expense')
            ->whereBetween('occurred_at', [$month, $end])->sum('amount');

        $byCategory = Transaction::where('user_id', $me->id)
            ->where('type', 'expense')
            ->whereBetween('occurred_at', [$month, $end])
            ->with('category')
            ->get()
            ->groupBy('category_id')
            ->map(fn ($group) => [
                'category' => $group->first()->category,
                'total' => (float) $group->sum('amount'),
                'count' => $group->count(),
            ])
            ->sortByDesc('total')
            ->values();

        // Daily series (for sparkline)
        $dailySpend = [];
        for ($d = $month->copy(); $d <= $end; $d->addDay()) {
            $dailySpend[$d->day] = 0;
        }
        Transaction::where('user_id', $me->id)->where('type', 'expense')
            ->whereBetween('occurred_at', [$month, $end])
            ->get()->each(function ($t) use (&$dailySpend) {
                $dailySpend[$t->occurred_at->day] = ($dailySpend[$t->occurred_at->day] ?? 0) + (float) $t->amount;
            });

        return view('reports.index', compact('month', 'income', 'expense', 'byCategory', 'dailySpend'));
    }
}
