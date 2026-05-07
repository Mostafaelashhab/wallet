<?php

namespace App\Console\Commands;

use App\Models\NotificationOutbox;
use App\Models\RecurringTransaction;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Services\NetWorthSnapshotter;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RunPersonalRecurring extends Command
{
    protected $signature = 'splitty:daily-tasks';
    protected $description = 'Run daily tasks: recurring transactions, subscriptions billing, net worth snapshots';

    public function handle(): int
    {
        $today = today();

        // 1. Recurring transactions (personal)
        $recurring = RecurringTransaction::with('account', 'category')
            ->where('active', true)->where('next_run_at', '<=', $today)
            ->where(fn ($q) => $q->whereNull('end_date')->orWhere('end_date', '>=', $today))
            ->get();
        foreach ($recurring as $r) {
            DB::transaction(function () use ($r) {
                Transaction::create([
                    'user_id' => $r->user_id,
                    'account_id' => $r->account_id,
                    'transfer_to_account_id' => $r->transfer_to_account_id,
                    'category_id' => $r->category_id,
                    'type' => $r->type,
                    'amount' => $r->amount,
                    'currency' => $r->currency,
                    'description' => $r->description,
                    'occurred_at' => $r->next_run_at,
                ]);
                $r->update(['next_run_at' => $r->advance()->toDateString()]);
            });
        }
        $this->info("Processed {$recurring->count()} personal recurring entries.");

        // 2. Subscriptions billing
        $subs = Subscription::where('active', true)->where('next_billing_at', '<=', $today)->get();
        foreach ($subs as $s) {
            DB::transaction(function () use ($s) {
                if ($s->auto_log && $s->account_id) {
                    Transaction::create([
                        'user_id' => $s->user_id,
                        'account_id' => $s->account_id,
                        'category_id' => $s->category_id,
                        'type' => 'expense',
                        'amount' => $s->amount,
                        'currency' => $s->currency,
                        'description' => $s->name . ' · ' . $s->next_billing_at->translatedFormat('M Y'),
                        'occurred_at' => $s->next_billing_at,
                    ]);
                }
                NotificationOutbox::create([
                    'user_id' => $s->user_id,
                    'title' => __('app.sub_renewed_title', ['name' => $s->name]),
                    'body' => number_format($s->amount, 2) . ' ' . $s->currency,
                    'url' => route('subscriptions.index'),
                ]);
                $s->update(['next_billing_at' => $s->advance()->toDateString()]);
            });
        }
        $this->info("Billed {$subs->count()} subscriptions.");

        // 3. Subscription renewal reminders (3 days ahead)
        $upcoming = Subscription::where('active', true)
            ->whereDate('next_billing_at', $today->copy()->addDays(3))->get();
        foreach ($upcoming as $s) {
            NotificationOutbox::firstOrCreate(
                ['user_id' => $s->user_id, 'title' => __('app.sub_upcoming_title', ['name' => $s->name])],
                ['body' => __('app.sub_upcoming_body', ['name' => $s->name, 'days' => 3]), 'url' => route('subscriptions.index')]
            );
        }

        // 4. Net worth snapshots
        $count = NetWorthSnapshotter::captureAll();
        $this->info("Captured net worth for {$count} users.");

        return self::SUCCESS;
    }
}
