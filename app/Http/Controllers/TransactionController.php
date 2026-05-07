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
        $type = $request->query('type');
        $q = $request->query('q');
        $from = $request->query('from');
        $to = $request->query('to');
        $accountId = $request->query('account');
        $categoryId = $request->query('cat');

        $query = $request->user()->transactions()->with('account', 'transferToAccount', 'category');
        if (in_array($type, ['expense','income','transfer'])) $query->where('type', $type);
        if ($q) $query->where('description', 'like', '%' . $q . '%');
        if ($from) $query->whereDate('occurred_at', '>=', $from);
        if ($to) $query->whereDate('occurred_at', '<=', $to);
        if ($accountId) $query->where('account_id', $accountId);
        if ($categoryId) $query->where('category_id', $categoryId);

        $transactions = $query->take(150)->get();
        $accounts = $request->user()->accounts()->get();
        $categories = Category::all();

        return view('transactions.index', compact('transactions', 'type', 'q', 'from', 'to', 'accountId', 'categoryId', 'accounts', 'categories'));
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
            ->whereIn('kind', [$type === 'income' ? 'income' : 'expense', 'both'])->get();
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
        Account::where('user_id', $me->id)->findOrFail($data['account_id']);
        if ($data['type'] === 'transfer') {
            abort_unless($data['transfer_to_account_id'] ?? null, 422);
            Account::where('user_id', $me->id)->findOrFail($data['transfer_to_account_id']);
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
            'description' => $tx->description,
            'account' => Account::find($tx->account_id)?->name,
        ]);
        return redirect()->route('dashboard')->with('flash', match ($tx->type) {
            'income' => __('app.tx_saved_income'),
            'transfer' => __('app.tx_saved_transfer'),
            default => __('app.tx_saved_expense'),
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
        return redirect()->route('dashboard')->with('flash', __('app.deleted'));
    }

    public function exportCsv(Request $request)
    {
        $from = $request->query('from') ?: now()->startOfMonth()->toDateString();
        $to = $request->query('to') ?: now()->endOfMonth()->toDateString();
        $rows = $request->user()->transactions()->with('account', 'category', 'transferToAccount')
            ->whereDate('occurred_at', '>=', $from)
            ->whereDate('occurred_at', '<=', $to)
            ->orderBy('occurred_at')->get();

        $filename = "splitty-{$from}-to-{$to}.csv";
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];
        return response()->stream(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // BOM for Excel UTF-8
            fputcsv($out, ['Date', 'Type', 'Description', 'Category', 'Account', 'To Account', 'Amount', 'Currency']);
            foreach ($rows as $t) {
                fputcsv($out, [
                    $t->occurred_at->toDateTimeString(),
                    $t->type,
                    $t->description,
                    $t->category?->name() ?? '',
                    $t->account?->name ?? '',
                    $t->transferToAccount?->name ?? '',
                    number_format((float) $t->amount, 2, '.', ''),
                    $t->currency,
                ]);
            }
            fclose($out);
        }, 200, $headers);
    }
}
