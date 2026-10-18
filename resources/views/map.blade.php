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
    @push('scripts')
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
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
            <div class="map-track-search">
                <input
                    id="map-track-search"
                    type="search"
                    autocomplete="off"
                    aria-controls="map-track-search-results"
                    aria-expanded="false"
                    placeholder="Meklēt trasi"
                >
                <ul id="map-track-search-results" class="map-track-search-results" aria-label="Trašu meklēšanas rezultāti" hidden></ul>
                <p id="map-track-search-empty" class="map-track-search-empty" role="status" hidden>Trase ar šādu nosaukumu nav atrasta.</p>
            </div>
            <div id="map" class="map-canvas" data-latvia-map="tracks"></div>
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
                        maxlength="255"
                        value="{{ $announcementSearch }}"
                        placeholder="Piemēram, slēgta vai lietus"
                    >
                    <x-field-error field="announcement_search" />
                </div>

                <div>
                    <span>Trase</span><br>
                    <input id="announcement-track" type="hidden" name="track_id" value="{{ $announcementTrackId }}">
                    <button id="open-track-picker" class="button-secondary" type="button" aria-haspopup="dialog" aria-controls="track-picker-dialog" aria-label="Trase: {{ $tracks->firstWhere('id', $announcementTrackId)?->name ?? 'Visas trases' }}">
                        {{ $tracks->firstWhere('id', $announcementTrackId)?->name ?? 'Visas trases' }}
                    </button>
                    <x-field-error field="track_id" />
                </div>

                <div class="inline-checkbox">
                    <label>
                        <input type="checkbox" name="prioritize_pinned" value="1" @checked($prioritizePinned)>
                        Rādīt svarīgos pirmos
                    </label>
                    <x-field-error field="prioritize_pinned" />
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
                @php
                    $coverImage = $track->images->first();
                @endphp
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
            <x-field-error field="track_ids" />
            <x-field-error field="track_ids.*" />
        @endauth
        <p id="track-picker-empty" hidden>Trases nav atrastas.</p>
    </dialog>

    <script id="map-data" type="application/json">{!! \Illuminate\Support\Js::encode($mapData) !!}</script>
</x-layout>
