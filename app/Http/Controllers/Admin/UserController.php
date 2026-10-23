<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Models\Track;
use App\Models\User;
use App\Rules\PersonName;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

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
            'user' => new User,
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
        $currentPassword = $validated['current_password'] ?? null;
        unset($validated['password'], $validated['current_password']);

        DB::transaction(function () use ($request, $user, $validated, $password, $currentPassword): void {
            $admins = User::where('role', 'admin')->orderBy('id')->lockForUpdate()->get(['id']);
            $target = User::findOrFail($user->id);

            if ($target->is($request->user()) && $validated['role'] !== 'admin') {
                abort(403, 'Admin nevar noņemt sev administratora lomu.');
            }

            if ($target->role === 'admin'
                && $validated['role'] !== 'admin'
                && $admins->count() <= 1) {
                abort(422, 'Sistēmā jābūt vismaz vienam administratoram.');
            }

            if ($validated['role'] !== 'owner') {
                Track::where('user_id', $target->id)->update(['user_id' => null]);
            }

            if ($validated['role'] !== 'owner' && $target->role === 'owner') {
                DB::table('track_owner_access_requests')
                    ->where('user_id', $target->id)
                    ->where('status', 'pending')
                    ->update([
                        'status' => 'rejected',
                        'reviewed_by' => $request->user()->id,
                        'reviewed_at' => now(),
                        'updated_at' => now(),
                    ]);
            }

            if ($password) {
                if ($target->is($request->user())) {
                    Auth::logoutOtherDevices($currentPassword);
                }
                $validated['password'] = Hash::make($password);
            }

            $target->update($validated);
        });

        $user->refresh();

        if ($password) {
            $sessions = DB::table('sessions')->where('user_id', $user->id);
            if ($user->is($request->user())) {
                $sessions->where('id', '<>', $request->session()->getId());
            }
            $sessions->delete();
        }

        return redirect()->route('admin.users.index')->with('status', 'Lietotājs atjaunots.');
    }

    public function destroy(Request $request, User $user)
    {
        DB::transaction(function () use ($request, $user): void {
            $admins = User::where('role', 'admin')->orderBy('id')->lockForUpdate()->get(['id']);
            $target = User::findOrFail($user->id);

            abort_if($target->is($request->user()), 403, 'Admin nevar izdzēst pats savu kontu.');

            if ($target->role === 'admin' && $admins->count() <= 1) {
                abort(422, 'Sistēmā jābūt vismaz vienam administratoram.');
            }

            $target->delete();
        });

        return redirect()->route('admin.users.index')->with('status', 'Lietotājs izdzēsts.');
    }

    private function validateUser(Request $request, ?User $user = null): array
    {
        $request->merge([
            'name' => PersonName::normalize($request->input('name')),
            'surname' => PersonName::normalize($request->input('surname')),
        ]);

        return $request->validate([
            'name' => ['required', 'string', new PersonName(), 'max:255'],
            'surname' => ['required', 'string', new PersonName(), 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user?->id)],
            'current_password' => [
                Rule::requiredIf(fn () => $user?->is($request->user())
                    && ($request->input('email') !== $user->email || $request->filled('password'))),
                'nullable',
                'current_password',
            ],
            'role' => ['required', Rule::in($user?->role === 'owner' ? ['user', 'owner', 'admin'] : ['user', 'admin'])],
            'club_id' => ['nullable', 'integer', 'exists:clubs,id'],
            'category' => ['nullable', Rule::in(['MX 50', 'MX 65', 'MX 85', 'MX 125', 'MX 250', 'MX 450', 'Kvadri', 'Blakusvāģi'])],
            'experience_level' => ['nullable', Rule::in(['Iesācējs', 'Amatieris', 'Veterāns', 'Profesionālis'])],
            'password' => [$user ? 'nullable' : 'required', 'string', Password::defaults(), 'confirmed'],
        ]);
    }
}
