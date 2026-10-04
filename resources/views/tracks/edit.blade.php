<x-layout>
    <x-slot:title>Rediģēt trasi</x-slot:title>

    <div class="page-shell page-shell-narrow">
        <a href="{{ route('tracks.show', $track) }}">Atpakaļ uz trasi</a>

        <section class="panel">
            <h1>Rediģēt trasi</h1>

            <form class="stack-form" method="POST" action="{{ route('tracks.update', $track) }}">
                @csrf
                @method('PUT')

                <x-alerts />

                <label>Nosaukums
                    <input type="text" name="name" value="{{ old('name', $track->name) }}" required>
                </label>

                <label>Apraksts
                    <textarea name="description" rows="5">{{ old('description', $track->description) }}</textarea>
                </label>

                <button type="submit">Saglabāt izmaiņas</button>
            </form>
        </section>
    </div>
</x-layout>