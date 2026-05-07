<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class GroupController extends Controller
{
    public function index(Request $request)
    {
        $me = $request->user();
        $groups = $me->groups()->withCount('expenses')->get()->map(function ($g) use ($me) {
            $g->my_balance = $g->balanceFor($me);
            $g->total = $g->totalSpent();
            return $g;
        });
        return view('groups.index', compact('groups'));
    }

    public function create()
    {
        return view('groups.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:80'],
            'icon' => ['nullable', 'string', 'max:8'],
            'color' => ['nullable', 'string', 'max:7'],
            'currency' => ['required', 'string', 'size:3'],
            'simplify_debts' => ['nullable', 'boolean'],
            'members' => ['array'],
            'members.*' => ['integer', 'exists:users,id'],
        ]);

        $group = Group::create([
            'name' => $data['name'],
            'owner_id' => $request->user()->id,
            'icon' => $data['icon'] ?? '🎉',
            'color' => $data['color'] ?? '#FF6B35',
            'currency' => $data['currency'],
            'simplify_debts' => (bool) ($data['simplify_debts'] ?? true),
        ]);
        $group->members()->attach($request->user()->id, ['role' => 'owner', 'joined_at' => now()]);
        foreach ($data['members'] ?? [] as $uid) {
            if ($uid != $request->user()->id) {
                $group->members()->attach($uid, ['role' => 'member', 'joined_at' => now()]);
            }
        }
        Activity::log('group.created', ['name' => $group->name], $group->id, Group::class, $group->id);
        return redirect()->route('groups.show', $group)->with('flash', 'Group created');
    }

    public function show(Group $group)
    {
        $group->load(['members', 'expenses' => fn ($q) => $q->latest('occurred_at')->with('payer','category','splits'), 'goals.contributions']);
        $me = request()->user();
        $balance = $group->balanceFor($me);
        return view('groups.show', compact('group', 'balance'));
    }

    public function addMember(Request $request, Group $group)
    {
        $data = $request->validate([
            'email' => ['required', 'email', Rule::exists('users', 'email')],
        ]);
        $user = User::where('email', $data['email'])->first();
        if (!$group->members()->where('users.id', $user->id)->exists()) {
            $group->members()->attach($user->id, ['role' => 'member', 'joined_at' => now()]);
            Activity::log('group.member.added', ['user_id' => $user->id, 'name' => $user->name], $group->id);
        }
        return back()->with('flash', 'Member added');
    }

    public function removeMember(Group $group, User $user)
    {
        if ($group->owner_id === $user->id) abort(403, 'Cannot remove owner');
        $group->members()->detach($user->id);
        Activity::log('group.member.removed', ['user_id' => $user->id], $group->id);
        return back();
    }
}
