<?php

namespace App\Http\Controllers;

use App\Models\PushSubscription;
use Illuminate\Http\Request;

class PushController extends Controller
{
    public function subscribe(Request $request)
    {
        $endpoint = $request->input('endpoint');
        $keys = $request->input('keys', []);
        if (!$endpoint || !($keys['p256dh'] ?? null) || !($keys['auth'] ?? null)) {
            return response()->json(['ok' => false], 422);
        }
        PushSubscription::firstOrCreate(
            ['user_id' => $request->user()->id, 'endpoint' => $endpoint],
            ['p256dh' => $keys['p256dh'], 'auth' => $keys['auth']]
        );
        return response()->json(['ok' => true]);
    }
}
