<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function index(Request $request)
    {
        $accounts = $request->user()->accounts()->get()->each(function ($a) {
            $a->current_balance = $a->balance();
        });
        $netWorth = $accounts->where('include_in_total', true)->sum->current_balance;
        return view('accounts.index', compact('accounts', 'netWorth'));
    }

    public function create()
    {
        return view('accounts.create', ['types' => Account::typeOptions()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:80'],
            'type' => ['required', 'in:cash,bank,wallet,card,savings'],
            'institution' => ['nullable', 'string', 'max:80'],
            'currency' => ['required', 'string', 'size:3'],
            'opening_balance' => ['required', 'numeric'],
            'color' => ['nullable', 'string', 'max:7'],
            'icon' => ['nullable', 'string', 'max:32'],
            'include_in_total' => ['nullable', 'boolean'],
        ]);
        Account::create([
            'user_id' => $request->user()->id,
            'icon' => $data['icon'] ?? Account::typeOptions()[$data['type']]['icon'],
            'color' => $data['color'] ?? Account::typeOptions()[$data['type']]['color'],
            'include_in_total' => (bool) ($data['include_in_total'] ?? true),
            ...$data,
        ]);
        return redirect()->route('accounts.index')->with('flash', 'Wallet added');
    }

    public function show(Account $account, Request $request)
    {
        abort_unless($account->user_id === $request->user()->id, 403);
        $balance = $account->balance();
        $transactions = $account->transactions()->with('category', 'transferToAccount')->take(80)->get();
        return view('accounts.show', compact('account', 'balance', 'transactions'));
    }

    public function update(Account $account, Request $request)
    {
        abort_unless($account->user_id === $request->user()->id, 403);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:80'],
            'institution' => ['nullable', 'string', 'max:80'],
            'opening_balance' => ['required', 'numeric'],
            'color' => ['nullable', 'string', 'max:7'],
            'icon' => ['nullable', 'string', 'max:32'],
            'include_in_total' => ['nullable', 'boolean'],
        ]);
        $account->update($data + ['include_in_total' => (bool) ($data['include_in_total'] ?? true)]);
        return back()->with('flash', 'Wallet updated');
    }

    public function destroy(Account $account, Request $request)
    {
        abort_unless($account->user_id === $request->user()->id, 403);
        $account->update(['archived_at' => now()]);
        return redirect()->route('accounts.index')->with('flash', 'Wallet archived');
    }
}
