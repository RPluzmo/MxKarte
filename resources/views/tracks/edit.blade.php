<x-layout>
    <x-slot:title>Rediģēt trasi</x-slot:title>

    <a href="{{ route('tracks.show', $track) }}">Atpakaļ uz trasi</a>
    <h1>Rediģēt trasi</h1>

    @if ($errors->any())
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form method="POST" action="{{ route('tracks.update', $track) }}">
        @csrf
        @method('PUT')

        <p>
            <label>Nosaukums
                <input type="text" name="name" value="{{ old('name', $track->name) }}" required>
            </label>
        </p>

        <p>
            <label>Apraksts
                <textarea name="description" rows="5">{{ old('description', $track->description) }}</textarea>
            </label>
        </p>

        <button type="submit">Saglabāt izmaiņas</button>
    </form>
</x-layout>