<?php

namespace App\Http\Controllers;

use App\Models\Goal;
use App\Models\GoalContribution;
use App\Models\Group;
use Illuminate\Http\Request;

class GoalController extends Controller
{
    public function create(Group $group)
    {
        return view('goals.create', compact('group'));
    }

    public function store(Request $request, Group $group)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:80'],
            'target_amount' => ['required', 'numeric', 'min:1'],
            'currency' => ['required', 'string', 'size:3'],
            'deadline' => ['nullable', 'date', 'after:today'],
            'icon' => ['nullable', 'string', 'max:8'],
            'color' => ['nullable', 'string', 'max:7'],
        ]);
        $goal = Goal::create([
            'group_id' => $group->id,
            'owner_id' => $request->user()->id,
            ...$data,
            'icon' => $data['icon'] ?? '🎯',
            'color' => $data['color'] ?? '#10B981',
        ]);
        return redirect()->route('groups.show', $group)->with('flash', 'Goal created');
    }

    public function contribute(Request $request, Goal $goal)
    {
        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:1'],
            'note' => ['nullable', 'string', 'max:200'],
        ]);
        GoalContribution::create([
            'goal_id' => $goal->id,
            'user_id' => $request->user()->id,
            'amount' => $data['amount'],
            'contributed_at' => now(),
            'note' => $data['note'] ?? null,
        ]);
        return back()->with('flash', 'Contribution added');
    }
}
