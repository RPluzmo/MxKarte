<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Rules\PersonName;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    public function create()
    {
        return view('auth.register');
    }

    public function store(Request $request)
    {
        $request->merge([
            'name' => PersonName::normalize($request->input('name')),
            'surname' => PersonName::normalize($request->input('surname')),
        ]);

        $validated = $request->validate([
            'name' => ['required', 'string', new PersonName(), 'max:255'],
            'surname' => ['required', 'string', new PersonName(), 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:1', 'confirmed'],
        ]);

        $user = User::create($validated);
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('home');
    }
}
