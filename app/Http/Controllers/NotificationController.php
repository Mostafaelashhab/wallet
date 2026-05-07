<?php

namespace App\Http\Controllers;

use App\Models\NotificationOutbox;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $items = NotificationOutbox::where('user_id', $request->user()->id)
            ->latest()->take(50)->get();
        NotificationOutbox::where('user_id', $request->user()->id)
            ->whereNull('read_at')->update(['read_at' => now()]);
        return view('notifications', compact('items'));
    }
}
