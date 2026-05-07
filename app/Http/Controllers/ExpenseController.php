<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Expense;
use App\Models\ExpenseSplit;
use App\Models\Group;
use App\Models\NotificationOutbox;
use App\Models\User;
use App\Services\SplitCalculator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExpenseController extends Controller
{
    public function create(Request $request)
    {
        $me = $request->user();
        $groups = $me->groups()->get();
        $friends = $me->friends()->get();
        $categories = Category::all();
        $selectedGroup = $request->query('group') ? Group::find($request->query('group')) : null;
        return view('expenses.create', compact('groups', 'friends', 'categories', 'selectedGroup'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'group_id' => ['nullable', 'exists:groups,id'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'currency' => ['required', 'string', 'size:3'],
            'description' => ['required', 'string', 'max:200'],
            'occurred_at' => ['required', 'date'],
            'split_type' => ['required', 'in:equal,exact,percent,shares'],
            'shares' => ['required', 'array', 'min:1'],
            'shares.*' => ['nullable', 'numeric', 'min:0'],
            'receipt' => ['nullable', 'image', 'max:8192'],
            'location_lat' => ['nullable', 'numeric'],
            'location_lng' => ['nullable', 'numeric'],
            'location_name' => ['nullable', 'string', 'max:200'],
        ]);

        $me = $request->user();
        $payerId = (int) ($request->input('payer_id') ?: $me->id);

        $shares = collect($data['shares'])->filter(fn ($v) => $v !== null && $v !== '')->all();
        if (empty($shares)) {
            return back()->withErrors(['shares' => 'Choose at least one person'])->withInput();
        }

        $cat = !empty($data['category_id']) ? Category::find($data['category_id']) : Category::detect($data['description']);

        $receiptPath = null;
        if ($request->hasFile('receipt')) {
            $receiptPath = $request->file('receipt')->store('receipts', 'public');
        }

        DB::transaction(function () use ($data, $payerId, $shares, $cat, $receiptPath, &$expense) {
            $expense = Expense::create([
                'group_id' => $data['group_id'] ?: null,
                'payer_id' => $payerId,
                'category_id' => $cat?->id,
                'amount' => $data['amount'],
                'currency' => $data['currency'],
                'description' => $data['description'],
                'occurred_at' => $data['occurred_at'],
                'split_type' => $data['split_type'],
                'receipt_path' => $receiptPath,
                'location_lat' => $data['location_lat'] ?? null,
                'location_lng' => $data['location_lng'] ?? null,
                'location_name' => $data['location_name'] ?? null,
            ]);

            $sharesForCalc = $shares;
            if ($data['split_type'] === 'equal') $sharesForCalc = array_fill_keys(array_keys($shares), 1);

            $amounts = SplitCalculator::compute($data['split_type'], (float) $data['amount'], $sharesForCalc);
            foreach ($amounts as $uid => $amt) {
                ExpenseSplit::create([
                    'expense_id' => $expense->id,
                    'user_id' => (int) $uid,
                    'amount' => $amt,
                    'share_value' => $shares[$uid] ?? 1,
                ]);
            }
        });

        Activity::log('expense.created', [
            'amount' => (float) $data['amount'],
            'currency' => $data['currency'],
            'description' => $data['description'],
        ], $expense->group_id, Expense::class, $expense->id);

        // Outbox: notify involved members (debtors)
        foreach ($expense->splits()->where('user_id', '!=', $payerId)->get() as $split) {
            NotificationOutbox::create([
                'user_id' => $split->user_id,
                'title' => __('app.add_expense'),
                'body' => $me->name . ' • ' . $expense->description . ' (' . number_format($split->amount, 2) . ' ' . $expense->currency . ')',
                'url' => route('expenses.show', $expense),
            ]);
        }

        return redirect()->route('expenses.show', $expense)->with('flash', 'Expense added');
    }

    public function show(Expense $expense)
    {
        $expense->load('payer', 'category', 'splits.user', 'group', 'comments.user');
        return view('expenses.show', compact('expense'));
    }

    public function destroy(Expense $expense, Request $request)
    {
        abort_unless($expense->payer_id === $request->user()->id, 403);
        $gid = $expense->group_id;
        $expense->delete();
        Activity::log('expense.deleted', ['id' => $expense->id], $gid);
        return redirect()->route($gid ? 'groups.show' : 'dashboard', $gid ? ['group' => $gid] : []);
    }

    public function comment(Request $request, Expense $expense)
    {
        $data = $request->validate(['body' => ['required', 'string', 'max:500']]);
        Comment::create([
            'expense_id' => $expense->id,
            'user_id' => $request->user()->id,
            'body' => $data['body'],
        ]);
        return back();
    }
}
