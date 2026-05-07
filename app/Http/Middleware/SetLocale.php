<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        $locale = $request->cookie('app_locale')
            ?? optional($request->user())->locale
            ?? config('app.locale', 'ar');
        if (!in_array($locale, ['ar', 'en'])) $locale = 'ar';
        App::setLocale($locale);
        return $next($request);
    }
}
