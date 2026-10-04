<x-layout>
    <x-slot:title>
        MxKarte
    </x-slot:title>

    @push('styles')
        <link
            rel="stylesheet"
            href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
            integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
            crossorigin=""
        />
    @endpush

    <div class="page-shell">
        <h1>MxKarte</h1>

        @auth
            @if ($clubName)
                <div class="map-controls">
                    <button id="toggle-club-markers" class="button-secondary" type="button" aria-pressed="false">
                        Izcelt {{ $clubName }} pieteikumus
                    </button>
                    <span class="map-legend">
                        <span class="map-legend-swatch"></span>
                        Mana kluba trases
                    </span>
                </div>
            @endif
        @endauth

        <div class="map-panel">
            <div id="map" class="map-canvas"></div>
        </div>

        @if ($siteAnnouncements->isNotEmpty())
            <section class="announcements-panel">
                <h2>Sistēmas paziņojumi</h2>
                <div class="announcement-list">
                    @foreach ($siteAnnouncements as $siteAnnouncement)
                        <article class="announcement-card">
                            <h3>{{ $siteAnnouncement->title }}</h3>
                            <p>{{ $siteAnnouncement->body }}</p>
                            <small class="meta">
                                Publicēts {{ $siteAnnouncement->published_at->format('d.m.Y H:i') }}
                                @if ($siteAnnouncement->expires_at)
                                    · Aktīvs līdz {{ $siteAnnouncement->expires_at->format('d.m.Y H:i') }}
                                @endif
                            </small>
                        </article>
                    @endforeach
                </div>
            </section>
        @endif

        <section class="announcements-panel">
            <h2>Trašu paziņojumi</h2>

            <form method="GET" action="{{ route('home') }}" class="announcement-toolbar">
                <div>
                    <label for="announcement-search">Meklēt paziņojumos</label><br>
                    <input
                        id="announcement-search"
                        type="search"
                        name="announcement_search"
                        value="{{ $announcementSearch }}"
                        placeholder="Piemēram, slēgta vai lietus"
                    >
                </div>

                <div>
                    <label for="announcement-track">Trase</label><br>
                    <select id="announcement-track" name="track_id">
                        <option value="">Visas trases</option>
                        @foreach ($tracks as $track)
                            <option value="{{ $track->id }}" @selected($announcementTrackId === $track->id)>
                                {{ $track->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="inline-checkbox">
                    <label>
                        <input type="checkbox" name="only_pinned" value="1" @checked($onlyPinned)>
                        Rādīt tikai svarīgos
                    </label>
                </div>

                <div>
                    <button type="submit">Meklēt</button>
                </div>

                <div>
                    <a class="button button-secondary" href="{{ route('home') }}">Notīrīt filtrus</a>
                </div>
            </form>

            @auth
                <details>
                    <summary>Prioritārās trases</summary>
                    <form method="POST" action="{{ route('track-preferences.update') }}">
                        @csrf
                        @method('PUT')

                        <p>Atzīmētās trases paziņojumi tiks rādīti pirmie.</p>
                        @foreach ($tracks as $track)
                            <label>
                                <input
                                    type="checkbox"
                                    name="track_ids[]"
                                    value="{{ $track->id }}"
                                    @checked(in_array($track->id, $preferredTrackIds, true))
                                >
                                {{ $track->name }}
                            </label><br>
                        @endforeach

                        <button type="submit">Saglabāt prioritātes</button>
                    </form>
                </details>
            @endauth

            @if ($announcements->count() > 0)
                <div class="announcement-list">
                    @foreach ($announcements as $announcement)
                        <article class="announcement-card">
                            <h3>
                                @if ($announcement->is_pinned)
                                    [Svarīgi]
                                @endif
                                {{ $announcement->title }}
                            </h3>
                            <p><strong>{{ $announcement->track->name }}</strong></p>
                            <p>{{ $announcement->body }}</p>
                            <small class="meta">
                                Publicēts {{ $announcement->published_at->format('d.m.Y H:i') }}
                                @if ($announcement->expires_at)
                                    · Aktīvs līdz {{ $announcement->expires_at->format('d.m.Y H:i') }}
                                @endif
                            </small>
                            <a href="{{ route('tracks.show', $announcement->track) }}">Atvērt trasi</a>
                        </article>
                    @endforeach
                </div>
            @else
                <p>Šobrīd nav trašu paziņojumu.</p>
            @endif

            {{ $announcements->links() }}
        </section>
    </div>

    <!-- Leaflet JS bibliotēka kartes attēlošanau -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        const latviaCenter = [56.8796, 24.6032];
        const latviaBounds = L.latLngBounds(
            [55.6, 20.8],
            [58.1, 28.3]
        );
        const map = L.map('map', {
            maxBounds: latviaBounds,
            maxBoundsViscosity: 1,
            minZoom: 7,
            maxZoom: 18,
        }).setView(latviaCenter, 7);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap'
        }).addTo(map);

        const tracks = @json($tracks);
        const storageUrl = @json(asset('storage'));
        const clubTrackIds = @json($clubTrackIds);
        const clubName = @json($clubName);
        const clubMarkersToggle = document.getElementById('toggle-club-markers');
        const colorVisionToggle = document.getElementById('toggle-color-vision')
        let clubMarkersEnabled = false;
        let colorVisionMode = localStorage.getItem('mxkarte-color-vision') === 'true';

        function setColorVisionMode(enabled) {
            colorVisionMode = enabled;
            document.body.classList.toggle('color-vision-mode', colorVisionMode);

            if (colorVisionToggle) {
                colorVisionToggle.setAttribute('aria-pressed', String(colorVisionMode));
                colorVisionToggle.textContent = colorVisionMode
                    ? 'Krāsu pieejamības režīms: ieslēgts'
                    : 'Krāsu pieejamības režīms: izslēgts';
            }
        }

        setColorVisionMode(colorVisionMode);

        colorVisionToggle?.addEventListener('click', () => {
            setColorVisionMode(!colorVisionMode);
            localStorage.setItem('mxkarte-color-vision', String(colorVisionMode));
        });

        const markerSize = 2 * parseFloat(getComputedStyle(document.documentElement).fontSize);

        function createTrackIcon(track, isClubMarker = false) {
            return L.divIcon({
                className: '',
                html: `<span class="track-marker${isClubMarker ? ' club-marker' : ''}">${track.riders_count}</span>`,
                iconSize: [markerSize, markerSize],
                iconAnchor: [markerSize / 2, markerSize / 2],
            });
        }

        const trackMarkers = tracks.map(track => {
            const coverImage = track.images?.[0];
            const coverMarkup = coverImage
                ? `<img class="popup-cover" src="${storageUrl}/${coverImage.path}" alt="${track.name}">`
                : '';
            const marker = L.marker([track.lat, track.lng], {
                icon: createTrackIcon(track),
            })
                .addTo(map)
                .bindPopup(`
                    ${coverMarkup}<br>
                    <strong>${track.name}</strong><br>
                    ${track.description ?? ''}
                    <a class="popup-link" href="/tracks/${track.id}">Apskatīt</a>
                `);

            return { track, marker };
        });

        function updateClubMarkers() {
            trackMarkers.forEach(({ track, marker }) => {
                const isClubMarker = clubMarkersEnabled && clubTrackIds.includes(Number(track.id));
                marker.setIcon(createTrackIcon(track, isClubMarker));
            });
        }

        clubMarkersToggle?.addEventListener('click', () => {
            clubMarkersEnabled = !clubMarkersEnabled;
            clubMarkersToggle.setAttribute('aria-pressed', String(clubMarkersEnabled));
            clubMarkersToggle.textContent = clubMarkersEnabled
                ? 'Noņemt motokluba biedru izcēlumu'
                : `Izcelt ${clubName} biedru pieteikumus`;
            updateClubMarkers();
        });
    </script>
</x-layout>