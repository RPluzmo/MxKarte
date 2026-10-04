<x-layout>
    <div class="page-shell page-shell-narrow"><section class="panel">
        <h1>{{ $track->exists ? 'Rediģēt trasi' : 'Izveidot trasi' }}</h1>
        @include('admin.partials.nav')

        <x-alerts />

        <form method="POST" action="{{ $track->exists ? route('admin.tracks.update', $track) : route('admin.tracks.store') }}">
            @csrf
            @if ($track->exists)
                @method('PUT')
            @endif

            <p><label>Nosaukums<br><input name="name" value="{{ old('name', $track->name) }}" required></label></p>
            <p><label>Slug (URL nosaukums)<br><input name="slug" value="{{ old('slug', $track->slug) }}" required></label></p>
            <p><label>Platums (latitude)<br><input type="number" name="lat" step="0.00001" min="-90" max="90" value="{{ old('lat', $track->lat) }}" required></label></p>
            <p><label>Garums (longitude)<br><input type="number" name="lng" step="0.00001" min="-180" max="180" value="{{ old('lng', $track->lng) }}" required></label></p>
            <p><label>Apraksts<br><textarea name="description" rows="4">{{ old('description', $track->description) }}</textarea></label></p>

            <p>
                <label>Saimnieks<br>
                    <select name="user_id">
                        <option value="">Nav piešķirts</option>
                        @foreach ($owners as $owner)
                            <option value="{{ $owner->id }}" @selected((string) old('user_id', $track->user_id) === (string) $owner->id)>
                                {{ $owner->name }} {{ $owner->surname }} ({{ $owner->role }})
                            </option>
                        @endforeach
                    </select>
                </label>
            </p>

            <button type="submit">Saglabāt</button>
            <a href="{{ route('admin.tracks.index') }}">Atcelt</a>
        </form>
    </section></div>
</x-layout>
