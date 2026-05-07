<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Http\Request;

class OnboardingController extends Controller
{
    public function show(Request $request)
    {
        $me = $request->user();
        if ($me->onboarded_at) return redirect()->route('dashboard');
        $me->ensureDefaultAccounts();
        $accounts = $me->accounts()->get();
        return view('onboarding', compact('accounts'));
    }

    public function complete(Request $request)
    {
        $data = $request->validate([
            'balances' => ['array'],
            'balances.*' => ['nullable', 'numeric'],
            'salary' => ['nullable', 'numeric', 'min:0'],
            'salary_account_id' => ['nullable', 'exists:accounts,id'],
        ]);

        $me = $request->user();
        foreach ($data['balances'] ?? [] as $accountId => $balance) {
            if ($balance === null || $balance === '') continue;
            $acc = Account::where('user_id', $me->id)->find($accountId);
            if ($acc) $acc->update(['opening_balance' => (float) $balance]);
        }

        if (!empty($data['salary']) && !empty($data['salary_account_id'])) {
            $cat = Category::where('kind', 'income')->where('name_en', 'Salary')->first();
            Transaction::create([
                'user_id' => $me->id,
                'account_id' => $data['salary_account_id'],
                'type' => 'income',
                'category_id' => $cat?->id,
                'amount' => $data['salary'],
                'currency' => 'EGP',
                'description' => 'مرتب الشهر',
                'occurred_at' => now()->startOfMonth(),
            ]);
        }

        $me->update(['onboarded_at' => now()]);
        return redirect()->route('dashboard')->with('flash', __('app.welcome_done'));
    }

    public function skip(Request $request)
    {
        $request->user()->update(['onboarded_at' => now()]);
        return redirect()->route('dashboard');
    }
}
