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