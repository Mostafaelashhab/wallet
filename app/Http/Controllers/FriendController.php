<?php

namespace App\Http\Controllers;

use App\Models\Friendship;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FriendController extends Controller
{
    public function index(Request $request)
    {
        $me = $request->user();
        $friends = $me->friends()->get()->map(function ($f) use ($me) {
            $f->balance_with_me = $me->balanceWith($f);
            return $f;
        });
        $pending = Friendship::where('addressee_id', $me->id)->where('status', 'pending')->with('requester')->get();
        return view('friends.index', compact('friends', 'pending'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email', Rule::exists('users', 'email')],
        ]);
        $other = User::where('email', $data['email'])->first();
        if ($other->id === $request->user()->id) return back()->withErrors(['email' => 'You cannot friend yourself']);

        Friendship::firstOrCreate(
            ['requester_id' => $request->user()->id, 'addressee_id' => $other->id],
            ['status' => 'accepted']
        );
        return back()->with('flash', 'Friend added');
    }

    public function show(User $user)
    {
        $me = request()->user();
        $balance = $me->balanceWith($user);
        $expenses = \App\Models\Expense::query()
            ->where(function ($q) use ($user, $me) {
                $q->where('payer_id', $me->id)->whereHas('splits', fn ($s) => $s->where('user_id', $user->id));
            })
            ->orWhere(function ($q) use ($user, $me) {
                $q->where('payer_id', $user->id)->whereHas('splits', fn ($s) => $s->where('user_id', $me->id));
            })
            ->latest('occurred_at')
            ->take(50)
            ->with('payer', 'category', 'splits')
            ->get();
        return view('friends.show', compact('user', 'balance', 'expenses'));
    }
}
