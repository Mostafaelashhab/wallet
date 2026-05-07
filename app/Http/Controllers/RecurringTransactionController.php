<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\RecurringTransaction;
use Illuminate\Http\Request;

class RecurringTransactionController extends Controller
{
    public function index(Request $request)
    {
        $items = RecurringTransaction::where('user_id', $request->user()->id)
            ->with('account', 'category')
            ->orderBy('next_run_at')
            ->get();
        return view('personal-recurring.index', compact('items'));
    }

    public function create(Request $request)
    {
        $type = in_array($request->query('type'), ['expense', 'income', 'transfer']) ? $request->query('type') : 'expense';
        $accounts = $request->user()->accounts()->get();
        $categories = Category::where('kind', $type === 'income' ? 'income' : 'expense')->get();
        return view('personal-recurring.create', compact('type', 'accounts', 'categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'type' => ['required', 'in:expense,income,transfer'],
            'account_id' => ['required', 'exists:accounts,id'],
            'transfer_to_account_id' => ['nullable', 'exists:accounts,id', 'different:account_id'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'description' => ['required', 'string', 'max:120'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'currency' => ['required', 'string', 'size:3'],
            'frequency' => ['required', 'in:daily,weekly,monthly,yearly'],
            'next_run_at' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after:next_run_at'],
        ]);
        RecurringTransaction::create([
            'user_id' => $request->user()->id,
            'active' => true,
            ...$data,
        ]);
        return redirect()->route('personal-recurring.index')->with('flash', __('app.flash_saved'));
    }

    public function destroy(Request $request, RecurringTransaction $recurringTransaction)
    {
        abort_unless($recurringTransaction->user_id === $request->user()->id, 403);
        $recurringTransaction->delete();
        return back();
    }
}
