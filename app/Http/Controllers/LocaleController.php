<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LocaleController extends Controller
{
    public function switch(Request $request, string $locale)
    {
        if (!in_array($locale, ['ar', 'en'], true)) abort(404);

        if ($user = $request->user()) {
            $user->update(['locale' => $locale]);
        }

        return redirect($request->input('redirect') ?: url()->previous() ?: '/')
            ->withCookie(cookie()->forever('app_locale', $locale, '/', null, false, false));
    }
}
