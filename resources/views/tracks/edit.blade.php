<x-layout>
    <x-slot:title>Rediģēt trasi</x-slot:title>

    <div class="page-shell page-shell-narrow">
        <div class="page-actions">
            <a class="button button-ghost" href="{{ route('tracks.show', $track) }}">&larr; Atpakaļ uz trasi</a>
        </div>

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

                <div class="form-actions">
                    <button type="submit">Saglabāt izmaiņas</button>
                    <a class="button button-secondary" href="{{ route('tracks.show', $track) }}">Atcelt</a>
                </div>
            </form>
        </section>
    </div>
</x-layout>