<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\User;
use App\Services\DebtSimplifier;
use Illuminate\Http\Request;

class SettleUpController extends Controller
{
    public function show(Group $group, Request $request)
    {
        $group->load('members');
        $balances = DebtSimplifier::computeBalances($group);
        $transfers = DebtSimplifier::forGroup($group);
        $usersById = User::whereIn('id', array_keys($balances))->get()->keyBy('id');

        return view('settle.show', compact('group', 'balances', 'transfers', 'usersById'));
    }
}
