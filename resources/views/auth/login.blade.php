<x-layout>
    <h1>Ienākt</h1>

    <form action="{{ route('login') }}" method="POST">
        @csrf

        @if ($errors->any())
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        @endif

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

        <button type="submit">Ienākt</button>
    </form>
</x-layout>