<x-layout>
    <div class="page-shell page-shell-narrow"><section class="panel">
        <h1>{{ $user->exists ? 'Rediģēt lietotāju' : 'Izveidot lietotāju' }}</h1>
        @include('admin.partials.nav')

        <x-alerts />

        <form method="POST" action="{{ $user->exists ? route('admin.users.update', $user) : route('admin.users.store') }}">
            @csrf
            @if ($user->exists)
                @method('PUT')
            @endif

            <p><label>Vārds<br><input name="name" value="{{ old('name', $user->name) }}" required></label></p>
            <p><label>Uzvārds<br><input name="surname" value="{{ old('surname', $user->surname) }}" required></label></p>
            <p><label>E-pasts<br><input type="email" name="email" value="{{ old('email', $user->email) }}" required></label></p>
            @if ($user->exists && auth()->id() === $user->id)
                <p><label>Pašreizējā parole (nepieciešama, mainot e-pastu vai paroli)<br><input type="password" name="current_password" autocomplete="current-password"></label></p>
            @endif

            <p>
                <label>Loma<br>
                    <select name="role" required>
                        @foreach (['user' => 'Lietotājs', 'owner' => 'Trases saimnieks', 'admin' => 'Administrators'] as $role => $label)
                            <option value="{{ $role }}" @selected(old('role', $user->role ?? 'user') === $role)>{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
            </p>

            <p>
                <label>Klubs<br>
                    <select name="club_id">
                        <option value="">Nav izvēlēts</option>
                        @foreach ($clubs as $club)
                            <option value="{{ $club->id }}" @selected((string) old('club_id', $user->club_id) === (string) $club->id)>{{ $club->name }}</option>
                        @endforeach
                    </select>
                </label>
            </p>

            <p>
                <label>Kategorija<br>
                    <select name="category">
                        <option value="">Nav izvēlēta</option>
                        @foreach (['MX 50', 'MX 65', 'MX 85', 'MX 125', 'MX 250', 'MX 450', 'Kvadri', 'Blakusvāģi'] as $category)
                            <option value="{{ $category }}" @selected(old('category', $user->category) === $category)>{{ $category }}</option>
                        @endforeach
                    </select>
                </label>
            </p>

            <p>
                <label>Pieredze<br>
                    <select name="experience_level">
                        <option value="">Nav norādīta</option>
                        @foreach (['Iesācējs', 'Amatieris', 'Veterāns', 'Profesionālis'] as $level)
                            <option value="{{ $level }}" @selected(old('experience_level', $user->experience_level) === $level)>{{ $level }}</option>
                        @endforeach
                    </select>
                </label>
            </p>

            <p><label>Parole {{ $user->exists ? '(atstāj tukšu, lai nemainītu)' : '' }}<br><input type="password" name="password" {{ $user->exists ? '' : 'required' }}></label></p>
            <p><label>Parole atkārtoti<br><input type="password" name="password_confirmation" {{ $user->exists ? '' : 'required' }}></label></p>

            <button type="submit">Saglabāt</button>
            <a class="button button-secondary" href="{{ route('admin.users.index') }}">Atcelt</a>
        </form>
    </section></div>
</x-layout>
