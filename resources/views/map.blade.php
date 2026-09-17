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
            .track-marker {
                align-items: center;
                background: red;
                border: 3px solid white;
                border-radius: 50%;

                color: white;
                display: flex;
                font-size: 14px;
                font-weight: 700;
                height: 32px;
                justify-content: center;
                width: 32px;
            }

            .track-marker.club-marker {
                background: #1769aa;
            }

            .map-controls {
                align-items: center;
                display: flex;
                gap: 12px;
                margin: 12px 0;
            }

            .map-legend {
                align-items: center;
                display: flex;
                gap: 6px;
            }

            .map-legend-swatch {
                background: #1769aa;
                border-radius: 50%;
                display: inline-block;
                height: 12px;
                width: 12px;
            }

        </style>
    @endpush

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

    <div id="map" style="height: 600px;"></div>

    <!-- Leaflet JS bibliotēka kartes attēlošanau -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        const latviaCenter = [56.8796, 24.6032];
        const map = L.map('map').setView(latviaCenter, 7); // varbūt nomainīt uz .fitBounds

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap'
        }).addTo(map);

        const tracks = @json($tracks);
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
            const marker = L.marker([track.lat, track.lng], {
                icon: createTrackIcon(track),
            })
                .addTo(map)
                .bindPopup(`
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