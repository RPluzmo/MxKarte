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

        tracks.forEach(track => {
            L.marker([track.lat, track.lng])
            .addTo(map)
            .bindPopup(`
                <strong>${track.name}</strong><br>
                ${track.description ?? ''}
            `);
        });
    </script>
</x-layout>