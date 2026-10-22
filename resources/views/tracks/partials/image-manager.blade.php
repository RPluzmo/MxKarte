<fieldset class="track-image-manager">
    <legend>Trases attēli</legend>
    <div class="track-upload-grid">
        <div class="track-upload-field">
            <label class="track-upload-card" for="track-cover-image">
                <span class="track-upload-icon" aria-hidden="true">＋</span>
                <span class="track-upload-title">Izvēlēties jaunu titulattēlu</span>
                <input id="track-cover-image" class="track-upload-input" type="file" name="cover_image" accept="image/*" data-track-file-input="cover-file-name">
            </label>
            <p id="cover-file-name" class="track-upload-selection" aria-live="polite">Nav izvēlēts jauns attēls</p>
            <div id="cover-file-preview" class="track-upload-preview"></div>
            <x-field-error field="cover_image" />
        </div>

        <div class="track-upload-field">
            <label class="track-upload-card" for="track-gallery-images">
                <span class="track-upload-icon" aria-hidden="true">＋</span>
                <span class="track-upload-title">Pievienot galerijas attēlus</span>
                <input id="track-gallery-images" class="track-upload-input" type="file" name="gallery_images[]" accept="image/*" multiple data-track-file-input="gallery-file-name">
            </label>
            <p id="gallery-file-name" class="track-upload-selection" aria-live="polite">Nav izvēlēti jauni attēli</p>
            <div id="gallery-file-preview" class="track-upload-preview"></div>
            <x-field-error field="gallery_images" />
            <x-field-error field="gallery_images.*" />
        </div>
    </div>

    @php($coverImage = $track->exists ? $track->images->firstWhere('type', 'cover') : null)
    @if ($coverImage)
        <div class="track-current-cover">
            <img src="{{ asset('storage/' . $coverImage->path) }}" alt="Pašreizējais {{ $track->name }} titulattēls">
            <label class="track-remove-image">
                <input type="checkbox" name="remove_cover" value="1" @checked(old('remove_cover'))>
                Noņemt pašreizējo titulattēlu
            </label>
        </div>
    @endif

    @php($galleryImages = $track->exists ? $track->images->where('type', 'gallery') : collect())
    @if ($galleryImages->isNotEmpty())
        <div class="track-edit-gallery">
            @foreach ($galleryImages as $image)
                <label class="track-edit-gallery-item">
                    <img src="{{ asset('storage/' . $image->path) }}" alt="{{ $track->name }} galerijas attēls {{ $image->sort_order }}">
                    <span class="track-edit-gallery-label">
                        <input type="checkbox" name="remove_gallery_ids[]" value="{{ $image->id }}" @checked(in_array((string) $image->id, old('remove_gallery_ids', []), true))>
                        Noņemt {{ $image->sort_order }}. attēlu
                    </span>
                </label>
            @endforeach
        </div>
    @endif
    <x-field-error field="remove_gallery_ids" />
    <x-field-error field="remove_gallery_ids.*" />
</fieldset>
