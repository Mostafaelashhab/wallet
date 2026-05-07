<?php

namespace App\Console\Commands;

use App\Models\Activity;
use App\Models\Expense;
use App\Models\ExpenseSplit;
use App\Models\NotificationOutbox;
use App\Models\RecurringExpense;
use App\Services\SplitCalculator;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ProcessRecurringExpenses extends Command
{
    protected $signature = 'recurring:run';
    protected $description = 'Materialize due recurring bills into expenses';

    public function handle(): int
    {
        $today = today();
        $due = RecurringExpense::query()
            ->where('active', true)
            ->where('next_run_at', '<=', $today)
            ->where(function ($q) use ($today) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', $today);
            })
            ->with('group.members')
            ->get();

        foreach ($due as $r) {
            DB::transaction(function () use ($r) {
                $expense = Expense::create([
                    'group_id' => $r->group_id,
                    'payer_id' => $r->payer_id,
                    'category_id' => $r->category_id,
                    'amount' => $r->amount,
                    'currency' => $r->currency,
                    'description' => $r->description . ' · ' . $r->next_run_at->translatedFormat('M Y'),
                    'occurred_at' => $r->next_run_at,
                    'split_type' => $r->split_type,
                    'recurring_id' => $r->id,
                ]);
                $shares = $r->split_data ?? [];
                if (empty($shares) && $r->group) {
                    $shares = array_fill_keys($r->group->members->pluck('id')->all(), 1);
                }
                $amounts = SplitCalculator::compute($r->split_type, (float) $r->amount, $shares);
                foreach ($amounts as $uid => $amt) {
                    ExpenseSplit::create(['expense_id' => $expense->id, 'user_id' => (int) $uid, 'amount' => $amt, 'share_value' => $shares[$uid] ?? 1]);
                }
                Activity::log('expense.created', [
                    'amount' => (float) $r->amount, 'currency' => $r->currency,
                    'description' => $expense->description, 'recurring' => true,
                ], $r->group_id, Expense::class, $expense->id);

                if ($r->group) {
                    foreach ($r->group->members as $m) {
                        if ($m->id !== $r->payer_id) {
                            NotificationOutbox::create([
                                'user_id' => $m->id,
                                'title' => 'فاتورة دورية',
                                'body' => $expense->description . ' (' . number_format($expense->amount, 2) . ' ' . $expense->currency . ')',
                                'url' => route('expenses.show', $expense),
                            ]);
                        }
                    }
                }

                $r->update(['next_run_at' => $r->advance()->toDateString()]);
            });
        }

        $this->info("Processed {$due->count()} recurring bills.");
        return self::SUCCESS;
    }
}
