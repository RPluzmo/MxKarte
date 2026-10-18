const latviaCenter = [56.8796, 24.6032];

function createLatviaMap(element, center = latviaCenter, zoom = 7) {
    const latviaBounds = L.latLngBounds(
        [55.6, 20.8],
        [59.0, 28.3]
    );
    const map = L.map(element, {
        maxBounds: latviaBounds,
        maxBoundsViscosity: 1,
        minZoom: 7,
        maxZoom: 18,
    })
        .setView(center, zoom);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap',
    }).addTo(map);

    return map;
}

const tracksMapElement = document.querySelector('[data-latvia-map="tracks"]');

if (tracksMapElement) {
    const map = createLatviaMap(tracksMapElement);
    const { tracks, storageUrl, clubTrackIds, clubName } = JSON.parse(
        document.getElementById('map-data').textContent
    );
    const clubMarkersToggle = document.getElementById('toggle-club-markers');
    const colorVisionToggle = document.getElementById('toggle-color-vision');
    const trackSearchInput = document.getElementById('map-track-search');
    const trackSearchResults = document.getElementById('map-track-search-results');
    const trackSearchEmpty = document.getElementById('map-track-search-empty');
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
        const popupContent = document.createElement('div');
        popupContent.className = coverImage
            ? 'track-map-popup'
            : 'track-map-popup track-map-popup--without-cover';

        if (coverImage) {
            const image = document.createElement('img');
            image.className = 'popup-cover';
            image.src = `${storageUrl}/${coverImage.path}`;
            image.alt = track.name;
            popupContent.append(image);
        }

        const details = document.createElement('div');
        details.className = 'track-map-popup-details';

        const name = document.createElement('strong');
        name.textContent = track.name;
        details.append(name);

        const description = document.createElement('p');
        description.className = 'track-popup-description';
        description.textContent = track.description ?? '';
        details.append(description);

        const link = document.createElement('a');
        link.className = 'popup-link';
        link.href = `/tracks/${Number(track.id)}`;
        link.textContent = 'Apskatīt';
        details.append(link);
        popupContent.append(details);

        const marker = L.marker([track.lat, track.lng], {
            icon: createTrackIcon(track),
        })
            .addTo(map)
            .bindPopup(popupContent, {
                maxWidth: 460,
                minWidth: 280,
                autoPanPadding: [16, 16],
            });

        return { track, marker };
    });

    trackSearchInput?.addEventListener('input', () => {
        const search = trackSearchInput.value.toLocaleLowerCase('lv').trim();
        trackSearchResults.replaceChildren();

        if (!search) {
            trackSearchResults.hidden = true;
            trackSearchEmpty.hidden = true;
            trackSearchInput.setAttribute('aria-expanded', 'false');

            return;
        }

        const matches = trackMarkers
            .filter(({ track }) => track.name.toLocaleLowerCase('lv').includes(search))
            .slice(0, 8);

        matches.forEach(({ track, marker }) => {
            const item = document.createElement('li');
            const button = document.createElement('button');
            button.className = 'map-track-search-result';
            button.type = 'button';
            button.textContent = track.name;
            button.addEventListener('click', () => {
                trackSearchInput.value = track.name;
                trackSearchResults.hidden = true;
                trackSearchEmpty.hidden = true;
                trackSearchInput.setAttribute('aria-expanded', 'false');
                map.flyTo([track.lat, track.lng], Math.max(map.getZoom(), 13));
                marker.openPopup();
            });
            item.append(button);
            trackSearchResults.append(item);
        });

        trackSearchResults.hidden = matches.length === 0;
        trackSearchEmpty.hidden = matches.length > 0;
        trackSearchInput.setAttribute('aria-expanded', String(matches.length > 0));
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
}

const trackLocationMapElement = document.querySelector('[data-latvia-map="track-location"]');

if (trackLocationMapElement) {
    const latitudeInput = document.getElementById('track-lat');
    const longitudeInput = document.getElementById('track-lng');
    const coordinatesOutput = document.getElementById('track-location-coordinates');
    const initialLatitude = Number(trackLocationMapElement.dataset.lat);
    const initialLongitude = Number(trackLocationMapElement.dataset.lng);
    const hasInitialLocation = Number.isFinite(initialLatitude)
        && Number.isFinite(initialLongitude)
        && trackLocationMapElement.dataset.lat !== ''
        && trackLocationMapElement.dataset.lng !== ''
        && initialLatitude >= 55.6
        && initialLatitude <= 58.1
        && initialLongitude >= 20.8
        && initialLongitude <= 28.3;
    const initialCenter = hasInitialLocation
        ? [initialLatitude, initialLongitude]
        : latviaCenter;
    const map = createLatviaMap(trackLocationMapElement, initialCenter, hasInitialLocation ? 12 : 7);
    let marker = hasInitialLocation ? L.marker(initialCenter, { draggable: true }).addTo(map) : null;

    if (!hasInitialLocation && (latitudeInput.value || longitudeInput.value)) {
        latitudeInput.value = '';
        longitudeInput.value = '';
        coordinatesOutput.textContent = 'Iepriekšējās koordinātas nav atļautajā Latvijas zonā. Izvēlies vietu kartē.';
    }

    function setTrackLocation(latitude, longitude) {
        if (latitude < 55.6 || latitude > 58.1 || longitude < 20.8 || longitude > 28.3) {
            latitudeInput.value = '';
            longitudeInput.value = '';
            coordinatesOutput.textContent = 'Izvēlētā vieta atrodas ārpus atļautās Latvijas koordinātu zonas.';
            marker?.remove();
            marker = null;

            return;
        }

        const lat = latitude.toFixed(5);
        const lng = longitude.toFixed(5);

        latitudeInput.value = lat;
        longitudeInput.value = lng;
        coordinatesOutput.textContent = `Izvēlētās koordinātas: ${lat}, ${lng}`;

        if (marker) {
            marker.setLatLng([latitude, longitude]);
        } else {
            marker = L.marker([latitude, longitude], { draggable: true }).addTo(map);
            marker.on('dragend', event => {
                const position = event.target.getLatLng();
                setTrackLocation(position.lat, position.lng);
            });
        }
    }

    marker?.on('dragend', event => {
        const position = event.target.getLatLng();
        setTrackLocation(position.lat, position.lng);
    });

    map.on('click', event => setTrackLocation(event.latlng.lat, event.latlng.lng));

    latitudeInput.form.addEventListener('submit', event => {
        if (latitudeInput.value && longitudeInput.value) {
            return;
        }

        event.preventDefault();
        coordinatesOutput.textContent = 'Pirms saglabāšanas izvēlies trases atrašanās vietu kartē.';
        trackLocationMapElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
    });
}
