<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::withCount('tracks')->latest()->paginate(25);

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.form', [
            'user' => new User(),
            'clubs' => Club::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateUser($request);
        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);

        return redirect()->route('admin.users.index')->with('status', 'Lietotājs izveidots.');
    }

    public function edit(User $user)
    {
        return view('admin.users.form', [
            'user' => $user,
            'clubs' => Club::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, User $user)
    {
        $validated = $this->validateUser($request, $user);
        $password = $validated['password'] ?? null;
        unset($validated['password']);

        if ($user->is($request->user()) && $validated['role'] !== 'admin') {
            abort(403, 'Admin nevar noņemt sev administratora lomu.');
        }

        if ($user->role === 'admin'
            && $validated['role'] !== 'admin'
            && User::where('role', 'admin')->count() <= 1) {
            abort(422, 'Sistēmā jābūt vismaz vienam administratoram.');
        }

        if ($password) {
            $validated['password'] = Hash::make($password);
        }

        $user->update($validated);

        return redirect()->route('admin.users.index')->with('status', 'Lietotājs atjaunots.');
    }

    public function destroy(Request $request, User $user)
    {
        abort_if($user->is($request->user()), 403, 'Admin nevar izdzēst pats savu kontu.');

        if ($user->role === 'admin' && User::where('role', 'admin')->count() <= 1) {
            abort(422, 'Sistēmā jābūt vismaz vienam administratoram.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('status', 'Lietotājs izdzēsts.');
    }

    private function validateUser(Request $request, ?User $user = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'surname' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user?->id)],
            'role' => ['required', Rule::in(['user', 'owner', 'admin'])],
            'club' => ['nullable', 'string', 'exists:clubs,name'],
            'category' => ['nullable', Rule::in(['MX 50', 'MX 65', 'MX 85', 'MX 125', 'MX 250', 'MX 450', 'Kvadri', 'Blakusvāģi'])],
            'experience_level' => ['nullable', Rule::in(['Iesācējs', 'Amatieris', 'Veterāns', 'Profesionālis'])],
            'password' => [$user ? 'nullable' : 'required', 'string', 'min:8', 'confirmed'],
        ]);
    }
}
