<?php

namespace App\Services;

use App\Models\NetWorthSnapshot;
use App\Models\User;
use Carbon\Carbon;

class NetWorthSnapshotter
{
    public static function captureForUser(User $user, ?Carbon $date = null): NetWorthSnapshot
    {
        $date = $date ?? today();
        $accounts = $user->accounts()->where('include_in_total', true)->get();
        $byAccount = [];
        $total = 0.0;
        foreach ($accounts as $acc) {
            $bal = $acc->balance();
            $total += $bal;
            $byAccount[$acc->id] = ['name' => $acc->name, 'balance' => $bal];
        }
        return NetWorthSnapshot::updateOrCreate(
            ['user_id' => $user->id, 'date' => $date->toDateString()],
            ['total' => $total, 'by_account' => $byAccount]
        );
    }

    public static function captureAll(): int
    {
        $count = 0;
        User::query()->chunk(50, function ($users) use (&$count) {
            foreach ($users as $u) {
                self::captureForUser($u);
                $count++;
            }
        });
        return $count;
    }
}
