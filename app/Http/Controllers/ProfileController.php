<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\RiderChoices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        return view('profile.edit', [
            'user' => $request->user(),
            'clubs' => RiderChoices::clubs(),
            'categories' => RiderChoices::categories(),
            'experienceLevels' => RiderChoices::experienceLevels(),
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'surname' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique(User::class, 'email')->ignore($user->id)],
            'current_password' => [
                Rule::requiredIf(fn () => $request->input('email') !== $user->email || $request->filled('password')),
                'nullable',
                'current_password',
            ],
            'club_id' => ['nullable', 'integer', 'exists:clubs,id'],
            'category' => ['required', Rule::in(['MX 50', 'MX 65', 'MX 85', 'MX 125', 'MX 250', 'MX 450', 'Kvadri', 'Blakusvāģi'])],
            'experience_level' => ['required', Rule::in(['Iesācējs', 'Amatieris', 'Veterāns', 'Profesionālis'])],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        if (! empty($validated['password'])) {
            Auth::logoutOtherDevices($validated['current_password']);
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        unset($validated['current_password']);
        $user->update($validated);

        if (! empty($validated['password'])) {
            DB::table('sessions')->where('user_id', $user->id)
                ->where('id', '<>', $request->session()->getId())
                ->delete();
        }

        return back()->with('status', 'Profils atjaunots.');
    }
}
