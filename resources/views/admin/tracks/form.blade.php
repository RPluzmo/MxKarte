<x-layout>
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
            <p><label>Slug (URL nosaukums)<br><input name="slug" value="{{ old('slug', $track->slug) }}" required><x-field-error field="slug" /></label></p>
            <p><label>Platums (latitude)<br><input type="number" name="lat" step="0.00001" min="55.6" max="58.1" value="{{ old('lat', $track->lat) }}" required><x-field-error field="lat" /></label></p>
            <p><label>Garums (longitude)<br><input type="number" name="lng" step="0.00001" min="20.8" max="28.3" value="{{ old('lng', $track->lng) }}" required><x-field-error field="lng" /></label></p>
            <p><label>Apraksts<br><textarea name="description" rows="4">{{ old('description', $track->description) }}</textarea><x-field-error field="description" /></label></p>
            <p><label>Segums<br><input name="surface_type" value="{{ old('surface_type', $track->surface_type) }}" maxlength="255"><x-field-error field="surface_type" /></label></p>

            <p><label>Cover attēls<br><input type="file" name="cover_image" accept="image/*"><x-field-error field="cover_image" /></label></p>
            @if ($track->exists && $track->images->firstWhere('type', 'cover'))
                <img class="track-admin-cover-preview" src="{{ asset('storage/' . $track->images->firstWhere('type', 'cover')->path) }}" alt="{{ $track->name }} cover">
            @endif

            <fieldset class="track-admin-gallery">
                <legend>Galerija (maksimums 4 attēli)</legend>
                @if ($track->exists)
                    @foreach ($track->images->where('type', 'gallery') as $image)
                        <label class="track-admin-gallery-item">
                            <img src="{{ asset('storage/' . $image->path) }}" alt="{{ $track->name }} galerijas attēls">
                            <span>{{ $image->sort_order }}. attēls</span>
                            <input type="checkbox" name="remove_gallery_ids[]" value="{{ $image->id }}">
                            Noņemt
                        </label>
                        <x-field-error :field="'remove_gallery_ids.' . $loop->index" />
                    @endforeach
                @endif
                <label>Pievienot attēlus<br><input type="file" name="gallery_images[]" accept="image/*" multiple></label>
                <x-field-error field="gallery_images" />
                <x-field-error field="gallery_images.*" />
                <x-field-error field="remove_gallery_ids" />
                <x-field-error field="remove_gallery_ids.*" />
            </fieldset>

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
