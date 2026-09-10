<x-layout>
    <h1>Reģistrēties</h1>

    <form action="{{ route('register') }}" method="POST">
        @csrf

        @if ($errors->any())
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        @endif

        <p>
            <label>Vārds
                <input name="name" value="{{ old('name') }}" required>
            </label>
        </p>

        <p>
            <label>Uzvārds
                <input name="surname" value="{{ old('surname') }}" required>
            </label>
        </p>

        <p>
            <label>E-pasts
                <input type="email" name="email" value="{{ old('email') }}" required>
            </label>
        </p>

        <p>
            <label>Parole
                <input type="password" name="password" required>
            </label>
        </p>

        <p>
            <label>Parole atkārtoti
                <input type="password" name="password_confirmation" required>
            </label>
        </p>

        <button type="submit">Izveidot lietotāj profilu</button>
    </form>
</x-layout>