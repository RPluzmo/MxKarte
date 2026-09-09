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
        </style>
    @endpush

    <h1>MxKarte</h1>
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

        tracks.forEach(track => { /* katrai trasei pievienot marķieri pēc lat-lng un attēleot*/
            const trackIcon = L.divIcon({
                className: '',
                html: `<span class="track-marker">${track.riders_count}</span>`,
                iconSize: [32, 32],
                iconAnchor: [16, 16],
            });

            L.marker([track.lat, track.lng], { icon: trackIcon })
            .addTo(map)
            .bindPopup(`<!-- popups ar trases info--> 
                <strong>${track.name}</strong><br>
                ${track.description ?? ''}
                <a class="popup-link" href="/tracks/${track.id}">Apskatīt</a>
            `);
        });


    </script>
</x-layout>