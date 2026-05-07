<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Subscription;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function index(Request $request)
    {
        $subscriptions = Subscription::where('user_id', $request->user()->id)
            ->with('account', 'category')
            ->orderBy('next_billing_at')
            ->get();
        $monthlyTotal = (float) $subscriptions->where('active', true)->sum(fn ($s) => $s->monthlyEquivalent());
        $upcoming = $subscriptions->where('active', true)
            ->filter(fn ($s) => $s->daysUntilBilling() >= 0 && $s->daysUntilBilling() <= 7);
        return view('subscriptions.index', compact('subscriptions', 'monthlyTotal', 'upcoming'));
    }

    public function create(Request $request)
    {
        $accounts = $request->user()->accounts()->get();
        $categories = Category::where('kind', 'expense')->get();
        return view('subscriptions.create', compact('accounts', 'categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:80'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'currency' => ['required', 'string', 'size:3'],
            'frequency' => ['required', 'in:weekly,monthly,yearly'],
            'next_billing_at' => ['required', 'date'],
            'account_id' => ['nullable', 'exists:accounts,id'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'icon_name' => ['nullable', 'string', 'max:32'],
            'color' => ['nullable', 'string', 'max:7'],
            'cancel_url' => ['nullable', 'url'],
            'note' => ['nullable', 'string', 'max:200'],
            'auto_log' => ['nullable', 'boolean'],
        ]);
        Subscription::create([
            'user_id' => $request->user()->id,
            'icon_name' => $data['icon_name'] ?? 'sparkles',
            'color' => $data['color'] ?? '#6366F1',
            'auto_log' => (bool) ($data['auto_log'] ?? true),
            'active' => true,
            ...$data,
        ]);
        return redirect()->route('subscriptions.index')->with('flash', __('app.flash_saved'));
    }

    public function update(Request $request, Subscription $subscription)
    {
        abort_unless($subscription->user_id === $request->user()->id, 403);
        $data = $request->validate([
            'active' => ['nullable', 'boolean'],
            'amount' => ['nullable', 'numeric', 'min:0.01'],
            'next_billing_at' => ['nullable', 'date'],
        ]);
        $subscription->update($data);
        return back()->with('flash', __('app.flash_saved'));
    }

    public function destroy(Request $request, Subscription $subscription)
    {
        abort_unless($subscription->user_id === $request->user()->id, 403);
        $subscription->delete();
        return redirect()->route('subscriptions.index');
    }
}
