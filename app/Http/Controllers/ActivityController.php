<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        $me = $request->user();
        $groupIds = $me->groups()->pluck('groups.id');
        $items = Activity::query()
            ->where(function ($q) use ($me, $groupIds) {
                $q->where('user_id', $me->id)
                  ->orWhereIn('group_id', $groupIds);
            })
            ->latest()
            ->take(80)
            ->with('user', 'group')
            ->get();
        return view('activity', compact('items'));
    }
}
