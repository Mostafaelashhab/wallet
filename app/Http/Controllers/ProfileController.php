<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        return view('profile', ['user' => $request->user()]);
    }

    public function update(Request $request)
    {
        $me = $request->user();
        $data = $request->validate([
            'name' => ['required', 'string', 'max:80'],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($me->id)],
            'phone' => ['nullable', 'string', 'max:32'],
            'locale' => ['nullable', 'in:ar,en'],
            'currency' => ['nullable', 'string', 'size:3'],
            'instapay_handle' => ['nullable', 'string', 'max:120'],
            'vodafone_cash' => ['nullable', 'string', 'max:32'],
            'color' => ['nullable', 'string', 'max:7'],
            'avatar' => ['nullable', 'image', 'max:4096'],
            'password' => ['nullable', 'min:6', 'confirmed'],
        ]);
        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }
        $me->update($data);
        if (!empty($data['locale'])) {
            cookie()->queue('app_locale', $data['locale'], 60 * 24 * 365);
        }
        return back()->with('flash', 'Profile updated');
    }
}
