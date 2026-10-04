<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Club;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        $categories = [
            ['name' => 'MX 50', 'image' => 'categories/mx-50.png'],
            ['name' => 'MX 65', 'image' => 'categories/mx-65.png'],
            ['name' => 'MX 85', 'image' => 'categories/mx-85.png'],
            ['name' => 'MX 125', 'image' => 'categories/mx-125.png'],
            ['name' => 'MX 250', 'image' => 'categories/mx-250.png'],
            ['name' => 'MX 450', 'image' => 'categories/mx-450.png'],
            ['name' => 'Kvadri', 'image' => 'categories/kvadri.png'],
            ['name' => 'Blakusvāģi', 'image' => 'categories/blakusvagi.png'],
        ];

        foreach ($categories as &$category) {
            $category['image_url'] = Storage::disk('public')->exists($category['image'])
                ? Storage::disk('public')->url($category['image'])
                : null;
        }
        unset($category);

        return view('profile.edit', [
            'user' => $request->user(),
            'clubs' => Club::orderBy('name')->get(),
            'categories' => $categories,
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:30'],
            'surname' => ['nullable', 'string', 'max:20'],
            'email' => ['required', 'email', Rule::unique(User::class, 'email')->ignore($user->id)],
            'club' => ['nullable', 'string', 'exists:clubs,name'],
            'category' => ['required', Rule::in(['MX 50', 'MX 65', 'MX 85', 'MX 125', 'MX 250', 'MX 450', 'Kvadri', 'Blakusvāģi']),],
            'experience_level' => ['required', Rule::in(['Iesācējs', 'Amatieris', 'Veterāns', 'Profesionālis']),],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        if (! empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return back()->with('status', 'Profils atjaunots.');
    }
}