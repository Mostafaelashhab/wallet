<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Group;
use App\Models\RecurringExpense;
use Illuminate\Http\Request;

class RecurringController extends Controller
{
    public function create(Group $group)
    {
        $categories = Category::all();
        return view('recurring.create', compact('group', 'categories'));
    }

    public function store(Request $request, Group $group)
    {
        $data = $request->validate([
            'description' => ['required', 'string', 'max:120'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'currency' => ['required', 'string', 'size:3'],
            'frequency' => ['required', 'in:daily,weekly,monthly,yearly'],
            'next_run_at' => ['required', 'date', 'after_or_equal:today'],
            'end_date' => ['nullable', 'date', 'after:next_run_at'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'split_type' => ['required', 'in:equal,exact,percent,shares'],
            'split_data' => ['nullable', 'array'],
        ]);
        RecurringExpense::create([
            'group_id' => $group->id,
            'payer_id' => $request->user()->id,
            ...$data,
            'active' => true,
        ]);
        return redirect()->route('groups.show', $group)->with('flash', 'Recurring bill scheduled');
    }

    public function destroy(RecurringExpense $recurring)
    {
        abort_unless($recurring->payer_id === request()->user()->id, 403);
        $recurring->delete();
        return back();
    }
}
