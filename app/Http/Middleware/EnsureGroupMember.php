<?php

namespace App\Http\Middleware;

use App\Models\Group;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureGroupMember
{
    public function handle(Request $request, Closure $next): Response
    {
        $group = $request->route('group');
        if ($group instanceof Group && $request->user()) {
            $isMember = $group->members()->where('users.id', $request->user()->id)->exists();
            abort_unless($isMember, 403, 'Not a member of this group');
        }
        return $next($request);
    }
}
