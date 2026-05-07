<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Activity;
use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->query('type'); // expense, income, transfer
        $q = $request->user()->transactions()->with('account', 'transferToAccount', 'category');
        if (in_array($type, ['expense','income','transfer'])) $q->where('type', $type);
        $transactions = $q->take(120)->get();
        return view('transactions.index', compact('transactions', 'type'));
    }

    public function create(Request $request)
    {
        $type = in_array($request->query('type'), ['expense','income','transfer']) ? $request->query('type') : 'expense';
        $accounts = $request->user()->accounts()->get();
        if ($accounts->isEmpty()) {
            $request->user()->ensureDefaultAccounts();
            $accounts = $request->user()->accounts()->get();
        }
        $categories = Category::query()
            ->where(function ($q) use ($type) {
                $q->whereIn('kind', [$type === 'income' ? 'income' : 'expense', 'both']);
            })
            ->get();
        return view('transactions.create', compact('type', 'accounts', 'categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'type' => ['required', 'in:expense,income,transfer'],
            'account_id' => ['required', 'exists:accounts,id'],
            'transfer_to_account_id' => ['nullable', 'exists:accounts,id', 'different:account_id'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'currency' => ['required', 'string', 'size:3'],
            'description' => ['required', 'string', 'max:200'],
            'occurred_at' => ['required', 'date'],
            'attachment' => ['nullable', 'image', 'max:8192'],
            'location_lat' => ['nullable', 'numeric'],
            'location_lng' => ['nullable', 'numeric'],
            'location_name' => ['nullable', 'string', 'max:200'],
        ]);
        $me = $request->user();
        $account = Account::where('user_id', $me->id)->findOrFail($data['account_id']);
        if ($data['type'] === 'transfer') {
            abort_unless($data['transfer_to_account_id'] ?? null, 422, 'Transfer destination required');
            $to = Account::where('user_id', $me->id)->findOrFail($data['transfer_to_account_id']);
        }
        if ($data['type'] === 'expense' && !($data['category_id'] ?? null)) {
            $cat = Category::detect($data['description']);
            if ($cat) $data['category_id'] = $cat->id;
        }
        if ($request->hasFile('attachment')) {
            $data['attachment_path'] = $request->file('attachment')->store('attachments', 'public');
        }

        $tx = Transaction::create(['user_id' => $me->id, ...$data]);

        Activity::log('tx.' . $tx->type, [
            'amount' => (float) $tx->amount, 'currency' => $tx->currency,
            'description' => $tx->description, 'account' => $account->name,
        ]);

        return redirect()->route('dashboard')->with('flash', match ($tx->type) {
            'income' => 'تم تسجيل الدخل ✓',
            'transfer' => 'تم تسجيل التحويل ✓',
            default => 'تم تسجيل المصروف ✓',
        });
    }

    public function show(Transaction $transaction, Request $request)
    {
        abort_unless($transaction->user_id === $request->user()->id, 403);
        $transaction->load('account', 'transferToAccount', 'category');
        return view('transactions.show', compact('transaction'));
    }

    public function destroy(Transaction $transaction, Request $request)
    {
        abort_unless($transaction->user_id === $request->user()->id, 403);
        $transaction->delete();
        return redirect()->route('dashboard')->with('flash', 'Deleted');
    }
}
