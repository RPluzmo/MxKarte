<x-layout>
    <x-slot:title>Profils</x-slot:title>

    <h1>Profils</h1>

    @if (session('status'))
        <p>{{ session('status') }}</p>
    @endif

    <form action="{{ route('profile.update') }}" method="POST">
        @csrf
        @method('PUT')

        @if ($errors->any())
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        @endif

        <p>
            <label>Vārds
                <input name="name" value="{{ old('name', $user->name) }}" required>
            </label>
        </p>

        <p>
            <label>Uzvārds
                <input name="surname" value="{{ old('surname', $user->surname ?? '') }}">
            </label>
        </p>

        <p>
            <label>E-pasts
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required>
            </label>
        </p>

        <p>
            <label>Klubs
                <input type="text" name="club" value="{{ old('club', $user->club ?? '') }}">
            </label>
        </p>

        <p>
            <label>Kategorija</label>
            <select name="category">
                <option value="">Nav izvēlēts</option>
                <option value="MX 50" {{ old('category', $user->category) === 'MX 50' ? 'selected' : '' }}>MX 50</option>
                <option value="MX 65" {{ old('category', $user->category) === 'MX 65' ? 'selected' : '' }}>MX 65</option>
                <option value="MX 85" {{ old('category', $user->category) === 'MX 85' ? 'selected' : '' }}>MX 85</option>
                <option value="MX 125" {{ old('category', $user->category) === 'MX 125' ? 'selected' : '' }}>MX 125</option>
                <option value="MX 250" {{ old('category', $user->category) === 'MX 250' ? 'selected' : '' }}>MX 250</option>
                <option value="MX 450" {{ old('category', $user->category) === 'MX 450' ? 'selected' : '' }}>MX 450</option>
                <option value="Kvadri" {{ old('category', $user->category) === 'Kvadri' ? 'selected' : '' }}>Kvadri</option>
                <option value="Blakusvāģi" {{ old('category', $user->category) === 'Blakusvāģi' ? 'selected' : '' }}>Blakusvāģi</option>
            </select>
        </p>

        <p>
            <label>Pieredze</label>
            <select name="experience_level">
                <option value="">Nav izvēlēts</option>
                <option value="Iesācējs" {{ old('experience_level', $user->experience_level) === 'Iesācējs' ? 'selected' : '' }}>Iesācējs</option>
                <option value="Amatieris" {{ old('experience_level', $user->experience_level) === 'Amatieris' ? 'selected' : '' }}>Amatieris</option>
                <option value="Veterāns" {{ old('experience_level', $user->experience_level) === 'Veterāns' ? 'selected' : '' }}>Veterāns</option>
                <option value="Profesionālis" {{ old('experience_level', $user->experience_level) === 'Profesionālis' ? 'selected' : '' }}>Profesionālis</option>
            </select>
        </p>

        <p>
            <label>Jauna parole
                <input type="password" name="password">
            </label>
        </p>

        <p>
            <label>Jauna parole atkārtoti
                <input type="password" name="password_confirmation">
            </label>
        </p>

        <button type="submit">Saglabāt</button>
    </form>
</x-layout>