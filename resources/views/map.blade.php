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
        <style>
            body {
                margin: 0;
            }

            .page-shell {
                margin: 0 auto;
                max-width: 1200px;
                padding: 20px 16px 40px;
            }

            .map-panel,
            .announcements-panel {
                border: 1px solid #d1d5db;
                border-radius: 8px;
                padding: 12px;
                margin-top: 12px;
            }

            #map {
                border: 1px solid #d1d5db;
                border-radius: 8px;
                overflow: hidden;
            }

            .track-marker {
                align-items: center;
                background: #ef4444;
                border: 2px solid #ffffff;
                border-radius: 50%;
                color: #ffffff;
                display: flex;
                font-size: 14px;
                font-weight: 700;
                height: 32px;
                justify-content: center;
                width: 32px;
            }

            .track-marker.club-marker {
                background: #2563eb;
            }

            .map-controls {
                align-items: center;
                display: flex;
                flex-wrap: wrap;
                gap: 12px;
                margin: 12px 0;
            }

            .map-legend {
                align-items: center;
                display: flex;
                gap: 6px;
            }

            .map-legend-swatch {
                background: #2563eb;
                border-radius: 50%;
                display: inline-block;
                height: 12px;
                width: 12px;
            }

            .announcement-toolbar {
                align-items: end;
                display: flex;
                flex-wrap: wrap;
                gap: 12px;
                margin-bottom: 18px;
            }

            .announcement-toolbar > div {
                flex: 1 1 180px;
            }

            .announcement-toolbar input,
            .announcement-toolbar select,
            .announcement-toolbar button,
            .announcement-toolbar a,
            .map-controls button {
                box-sizing: border-box;
                padding: 8px 10px;
                width: 100%;
            }

            .announcement-toolbar button,
            .announcement-toolbar a,
            .map-controls button {
                background: #ffffff;
                border: 1px solid #111827;
                border-radius: 6px;
                color: #111827;
                cursor: pointer;
                display: inline-block;
                text-align: center;
                text-decoration: none;
            }

            .inline-checkbox {
                align-items: center;
                display: inline-flex;
                gap: 8px;
                min-height: 40px;
            }

            .announcement-list {
                display: flex;
                flex-wrap: wrap;
                gap: 12px;
                margin-top: 12px;
            }

            .announcement-card {
                border: 1px solid #d1d5db;
                display: flex;
                flex: 1 1 260px;
                flex-direction: column;
                gap: 8px;
                padding: 12px;
            }

            .announcement-card h3,
            .announcement-card p,
            .announcement-card small {
                margin: 0;
            }

            .announcement-card a {
                margin-top: auto;
            }

            @media (max-width: 640px) {
                .page-shell {
                    padding-left: 12px;
                    padding-right: 12px;
                }
            }
        </style>
    @endpush

    <div class="page-shell">
        <h1>MxKarte</h1>

        @auth
            @if ($clubName)
                <div class="map-controls">
                    <button id="toggle-club-markers" type="button" aria-pressed="false">
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
            <div id="map" style="height: 600px;"></div>
        </div>

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
                    <a href="{{ route('home') }}">Notīrīt filtrus</a>
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
        const map = L.map('map').setView(latviaCenter, 7); // varbūt nomainīt uz .fitBounds

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap'
        }).addTo(map);

        const tracks = @json($tracks);
        const storageUrl = @json(asset('storage'));
        const clubTrackIds = @json($clubTrackIds);
        const clubName = @json($clubName);
        const clubMarkersToggle = document.getElementById('toggle-club-markers');
        let clubMarkersEnabled = false;

        function createTrackIcon(track, isClubMarker = false) {
            return L.divIcon({
                className: '',
                html: `<span class="track-marker${isClubMarker ? ' club-marker' : ''}">${track.riders_count}</span>`,
                iconSize: [32, 32],
                iconAnchor: [16, 16],
            });
        }

        const trackMarkers = tracks.map(track => {
            const coverImage = track.images?.[0];
            const coverMarkup = coverImage
                ? `<img src="${storageUrl}/${coverImage.path}" alt="${track.name}" style="height: 120px; object-fit: cover; width: 180px;">`
                : '';
            const marker = L.marker([track.lat, track.lng], {
                icon: createTrackIcon(track),
            })
                .addTo(map)
                .bindPopup(`
                    ${coverMarkup}
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