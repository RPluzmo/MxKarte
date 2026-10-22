<x-layout>
    @push('styles')
        <link
            rel="stylesheet"
            href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
            integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
            crossorigin=""
        />
    @endpush
    @push('scripts')
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    @endpush

    <div class="page-shell page-shell-narrow"><section class="panel">
        <h1>{{ $track->exists ? 'Rediģēt trasi' : 'Izveidot trasi' }}</h1>
        @include('admin.partials.nav')

        <x-alerts />

        <form method="POST" enctype="multipart/form-data" action="{{ $track->exists ? route('admin.tracks.update', $track) : route('admin.tracks.store') }}">
            @csrf
            @if ($track->exists)
                @method('PUT')
            @endif

            <p><label>Nosaukums<br><input name="name" value="{{ old('name', $track->name) }}" required><x-field-error field="name" /></label></p>
            <p><label>Slug<br><input name="slug" value="{{ old('slug', $track->slug) }}" required><x-field-error field="slug" /></label></p>
            <fieldset class="track-location-field">
                <legend>Trases atrašanās vieta</legend>
                <div
                    id="track-location-map"
                    class="map-canvas track-location-map"
                    data-latvia-map="track-location"
                    data-lat="{{ old('lat', $track->lat) }}"
                    data-lng="{{ old('lng', $track->lng) }}"
                ></div>
                <input id="track-lat" type="hidden" name="lat" value="{{ old('lat', $track->lat) }}">
                <input id="track-lng" type="hidden" name="lng" value="{{ old('lng', $track->lng) }}">
                <p id="track-location-coordinates" class="track-location-coordinates" aria-live="polite">
                    @if (filled(old('lat', $track->lat)) && filled(old('lng', $track->lng)))
                        Izvēlētās koordinātas: {{ old('lat', $track->lat) }}, {{ old('lng', $track->lng) }}
                    @else
                        Atrašanās vieta vēl nav izvēlēta.
                    @endif
                </p>
                <x-field-error field="lat" />
                <x-field-error field="lng" />
            </fieldset>
            <p><label>Apraksts<br><textarea name="description" rows="4">{{ old('description', $track->description) }}</textarea><x-field-error field="description" /></label></p>
            <p><label>Segums<br><input name="surface_type" value="{{ old('surface_type', $track->surface_type) }}" maxlength="255"><x-field-error field="surface_type" /></label></p>

            @include('tracks.partials.image-manager', ['track' => $track])

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
                    <x-field-error field="user_id" />
                </label>
            </p>

            <button type="submit">Saglabāt</button>
            <a class="button button-secondary" href="{{ route('admin.tracks.index') }}">Atcelt</a>
        </form>
    </section></div>
</x-layout>
