<?php

namespace App\Services;

use App\Models\ExpenseSplit;
use App\Models\Group;
use App\Models\Payment;
use Illuminate\Support\Collection;

class DebtSimplifier
{
    /**
     * Returns a list of suggested transfers for a group:
     * [['from' => userId, 'to' => userId, 'amount' => float], ...]
     *
     * The algorithm minimises the number of transactions by repeatedly
     * matching the largest debtor against the largest creditor.
     */
    public static function forGroup(Group $group): Collection
    {
        $balances = self::computeBalances($group);

        return collect(self::greedyMatch($balances));
    }

    /** @return array<int,float> userId => net balance (positive = owed money, negative = owes money) */
    public static function computeBalances(Group $group): array
    {
        $balances = [];
        foreach ($group->members as $m) {
            $balances[$m->id] = 0.0;
        }

        $expenses = $group->expenses()->with('splits')->get();
        foreach ($expenses as $expense) {
            $balances[$expense->payer_id] = ($balances[$expense->payer_id] ?? 0) + (float) $expense->amount;
            foreach ($expense->splits as $split) {
                if ($split->settled_at !== null) continue;
                $balances[$split->user_id] = ($balances[$split->user_id] ?? 0) - (float) $split->amount;
            }
        }

        foreach ($group->payments as $payment) {
            $balances[$payment->payer_id] = ($balances[$payment->payer_id] ?? 0) + (float) $payment->amount;
            $balances[$payment->payee_id] = ($balances[$payment->payee_id] ?? 0) - (float) $payment->amount;
        }

        return array_map(fn ($v) => round($v, 2), $balances);
    }

    /**
     * Greedy min-transactions matching.
     * @param  array<int,float>  $balances
     * @return array<int,array{from:int,to:int,amount:float}>
     */
    private static function greedyMatch(array $balances): array
    {
        $debtors = [];   // userId => |amount|, owes money
        $creditors = []; // userId => amount, is owed money

        foreach ($balances as $uid => $bal) {
            if ($bal < -0.01) $debtors[$uid] = -$bal;
            elseif ($bal > 0.01) $creditors[$uid] = $bal;
        }

        $transfers = [];
        while (!empty($debtors) && !empty($creditors)) {
            arsort($debtors);
            arsort($creditors);

            $debtor = array_key_first($debtors);
            $creditor = array_key_first($creditors);
            $amount = round(min($debtors[$debtor], $creditors[$creditor]), 2);

            $transfers[] = ['from' => $debtor, 'to' => $creditor, 'amount' => $amount];

            $debtors[$debtor] = round($debtors[$debtor] - $amount, 2);
            $creditors[$creditor] = round($creditors[$creditor] - $amount, 2);

            if ($debtors[$debtor] < 0.01) unset($debtors[$debtor]);
            if ($creditors[$creditor] < 0.01) unset($creditors[$creditor]);
        }

        return $transfers;
    }
}
