<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Category;
use Illuminate\Http\Request;

class BudgetController extends Controller
{
    public function index(Request $request)
    {
        $budgets = $request->user()->budgets()->with('category')->get();
        $month = now();
        foreach ($budgets as $b) {
            $b->p = $b->progress($month);
        }
        $totalLimit = (float) $budgets->sum('amount');
        $totalSpent = (float) $budgets->sum(fn ($b) => $b->p['spent']);
        return view('budgets.index', compact('budgets', 'month', 'totalLimit', 'totalSpent'));
    }

    public function create(Request $request)
    {
        $existing = $request->user()->budgets()->pluck('category_id')->all();
        $categories = Category::where('kind', 'expense')->whereNotIn('id', $existing)->get();
        return view('budgets.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'amount' => ['required', 'numeric', 'min:1'],
            'currency' => ['required', 'string', 'size:3'],
            'period' => ['required', 'in:month,week,year'],
            'rollover' => ['nullable', 'boolean'],
        ]);
        $request->user()->budgets()->updateOrCreate(
            ['category_id' => $data['category_id'], 'period' => $data['period']],
            [...$data, 'rollover' => (bool) ($data['rollover'] ?? false)]
        );
        return redirect()->route('budgets.index')->with('flash', __('app.flash_saved'));
    }

    public function destroy(Request $request, Budget $budget)
    {
        abort_unless($budget->user_id === $request->user()->id, 403);
        $budget->delete();
        return back();
    }
}
