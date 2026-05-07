<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        $cookie = $request->cookie('app_locale');
        if (!in_array($cookie, ['ar', 'en'], true)) $cookie = null;

        $locale = $cookie
            ?? optional($request->user())->locale
            ?? config('app.locale', 'ar');

        if (!in_array($locale, ['ar', 'en'], true)) $locale = 'ar';
        App::setLocale($locale);
        return $next($request);
    }
}
