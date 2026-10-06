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
            <x-alerts />

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
                    <span>Trase</span><br>
                    <input id="announcement-track" type="hidden" name="track_id" value="{{ $announcementTrackId }}">
                    <button id="open-track-picker" class="button-secondary" type="button" aria-haspopup="dialog" aria-controls="track-picker-dialog" aria-label="Trase: {{ $tracks->firstWhere('id', $announcementTrackId)?->name ?? 'Visas trases' }}">
                        {{ $tracks->firstWhere('id', $announcementTrackId)?->name ?? 'Visas trases' }}
                    </button>
                </div>

                <div class="inline-checkbox">
                    <label>
                        <input type="checkbox" name="prioritize_pinned" value="1" @checked($prioritizePinned)>
                        Rādīt svarīgos pirmos
                    </label>
                </div>

                <div>
                    <button type="submit">Meklēt</button>
                </div>

                <div>
                    <a class="button button-secondary" href="{{ route('home') }}">Notīrīt filtrus</a>
                </div>
            </form>

            @if ($announcements->count() > 0)
                @php
                    $pageAnnouncements = $announcements->getCollection();
                    $announcementSections = count($preferredTrackIds)
                        ? [
                            [
                                'title' => 'Prioritāro trašu paziņojumi',
                                'empty' => 'Šobrīd nav prioritāro trašu paziņojumu.',
                                'items' => $pageAnnouncements->filter(fn ($announcement) => in_array((int) $announcement->track_id, $preferredTrackIds, true)),
                            ],
                            [
                                'title' => 'Pārējo trašu paziņojumi',
                                'empty' => 'Šobrīd nav paziņojumu no pārējām trasēm.',
                                'items' => $pageAnnouncements->reject(fn ($announcement) => in_array((int) $announcement->track_id, $preferredTrackIds, true)),
                            ],
                        ]
                        : [[
                            'title' => 'Trašu paziņojumi',
                            'empty' => 'Šobrīd nav trašu paziņojumu.',
                            'items' => $pageAnnouncements,
                        ]];
                @endphp

                @foreach ($announcementSections as $section)
                    <section class="announcement-section">
                        <h3>{{ $section['title'] }}</h3>
                        @if ($section['items']->isNotEmpty())
                            <div class="announcement-list">
                                @foreach ($section['items'] as $announcement)
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
                            <p>{{ $section['empty'] }}</p>
                        @endif
                    </section>
                @endforeach
            @else
                <p>Šobrīd nav trašu paziņojumu.</p>
            @endif

            {{ $announcements->links() }}
        </section>
    </div>

    <dialog id="track-picker-dialog" class="choice-dialog track-picker-dialog" aria-labelledby="track-picker-title">
        <div class="choice-dialog-header">
            <h2 id="track-picker-title">Izvēlies trasi</h2>
            <form method="dialog">
                <button class="button-secondary" type="submit">Aizvērt</button>
            </form>
        </div>

        <label for="track-picker-search">Meklēt trasi</label>
        <input id="track-picker-search" class="choice-search" type="search" placeholder="Ieraksti trases nosaukumu">

        @auth
            <p>Atzīmē prioritārās trases, kuru paziņojumi tiks rādīti pirmie.</p>
            <form id="track-preferences-form" method="POST" action="{{ route('track-preferences.update') }}">
                @csrf
                @method('PUT')
            </form>
        @endauth

        <div class="track-picker-grid">
            <article class="track-picker-card" data-track-search-option data-track-name="Visas trases">
                <button class="track-picker-filter" type="button" data-track-filter data-track-id="" data-track-label="Visas trases" aria-pressed="{{ $announcementTrackId ? 'false' : 'true' }}">
                    <span class="track-picker-placeholder">Rādīt paziņojumus no visām trasēm</span>
                    <span class="track-picker-name">Visas trases</span>
                </button>
            </article>

            @foreach ($tracks as $track)
                @php($coverImage = $track->images->first())
                <article class="track-picker-card" data-track-search-option data-track-name="{{ $track->name }}">
                    <button class="track-picker-filter" type="button" data-track-filter data-track-id="{{ $track->id }}" data-track-label="{{ $track->name }}" aria-pressed="{{ $announcementTrackId === $track->id ? 'true' : 'false' }}">
                        @if ($coverImage)
                            <img class="track-picker-cover" src="{{ asset('storage/' . $coverImage->path) }}" alt="{{ $track->name }}" loading="lazy">
                        @else
                            <span class="track-picker-placeholder">Nav pievienots cover attēls</span>
                        @endif
                        <span class="track-picker-name">{{ $track->name }}</span>
                    </button>
                    @auth
                        <label class="track-priority-toggle">
                            <input
                                type="checkbox"
                                name="track_ids[]"
                                value="{{ $track->id }}"
                                form="track-preferences-form"
                                @checked(in_array($track->id, $preferredTrackIds, true))
                            >
                            Prioritārā trase
                        </label>
                    @endauth
                </article>
            @endforeach
        </div>

        @auth
            <button class="track-priority-save" type="submit" form="track-preferences-form">Saglabāt prioritātes</button>
        @endauth
        <p id="track-picker-empty" hidden>Trases nav atrastas.</p>
    </dialog>

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
                const tooltip = colorVisionMode
                    ? 'Krāsu pieejamības režīms: ieslēgts'
                    : 'Krāsu pieejamības režīms: izslēgts';
                colorVisionToggle.setAttribute('aria-label', tooltip);
                colorVisionToggle.dataset.tooltip = tooltip;
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