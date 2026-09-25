<x-layout>
    @push('styles')
        <style>
            body {
                margin: 0;
            }

            .page-shell {
                margin: 0 auto;
                max-width: 1200px;
                padding: 20px 16px 40px;
            }

            .track-panel {
                border: 1px solid #d1d5db;
                border-radius: 8px;
                margin-top: 12px;
                padding: 12px;
            }

            .track-grid {
                display: flex;
                flex-wrap: wrap;
                gap: 12px;
                margin-top: 12px;
            }

            .track-grid > .track-panel {
                flex: 1 1 320px;
                margin-top: 0;
            }

            .track-panel article {
                border-top: 1px solid #e5e7eb;
                margin-top: 12px;
                padding-top: 12px;
            }

            .track-cover {
                display: block;
                height: 280px;
                object-fit: cover;
                width: 100%;
            }

            .track-gallery {
                display: flex;
                flex-wrap: wrap;
                gap: 12px;
            }

            .track-gallery img {
                flex: 1 1 180px;
                height: 180px;
                object-fit: cover;
                width: 180px;
            }

            .track-panel h2,
            .track-panel h3,
            .track-panel p {
                margin-top: 0;
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
        <a href="/">Atpakaļ</a>
        <h1>{{ $track->name }}</h1>

        @if (session('status'))
            <p>{{ session('status') }}</p>
        @endif

        @auth
            @if ($track->user_id === auth()->id())
                <p><a href="{{ route('tracks.edit', $track) }}">Rediģēt trasi</a></p>
            @endif
        @endauth

        @php($coverImage = $track->images->firstWhere('type', 'cover'))
        @if ($coverImage)
            <section class="track-panel">
                <img class="track-cover" src="{{ asset('storage/' . $coverImage->path) }}" alt="{{ $track->name }}">
            </section>
        @endif

        <div class="track-grid">
            <section class="track-panel">
                <h2>Pieteikties treniņam</h2>
                <form method="POST" action="{{ route('riders.store', $track) }}">
                    @csrf
                    @php($authUser = auth()->user())

                    <p>
                        <label>Vārds<br>
                            <input type="text" name="name" value="{{ old('name', $authUser?->name ?? '') }}" required>
                        </label>
                    </p>
                    <p>
                        <label>Uzvārds<br>
                            <input type="text" name="surname" value="{{ old('surname', $authUser?->surname ?? '') }}" required>
                        </label>
                    </p>
                    <p>
                        <label>Klubs (neobligāti)<br>
                            <select name="club">
                                <option value="">Nav izvēlēts</option>
                                @foreach ($clubs as $club)
                                    <option value="{{ $club->name }}" {{ old('club', $authUser?->club) === $club->name ? 'selected' : '' }}>
                                        {{ $club->name }}
                                    </option>
                                @endforeach
                            </select>
                        </label>
                    </p>
                    <p>
                        <label>Klase<br>
                            <select name="category" required>
                                <option value="">Izvēlieties</option>
                                @foreach (['MX 50', 'MX 65', 'MX 85', 'MX 125', 'MX 250', 'MX 450', 'Kvadri', 'Blakusvāģi'] as $category)
                                    <option value="{{ $category }}" {{ old('category', $authUser?->category) === $category ? 'selected' : '' }}>{{ $category }}</option>
                                @endforeach
                            </select>
                        </label>
                    </p>
                    <p>
                        <label>Pieredze<br>
                            <select name="experience_level" required>
                                <option value="">Izvēlieties</option>
                                @foreach (['Iesācējs', 'Amatieris', 'Veterāns', 'Profesionālis'] as $experienceLevel)
                                    <option value="{{ $experienceLevel }}" {{ old('experience_level', $authUser?->experience_level) === $experienceLevel ? 'selected' : '' }}>{{ $experienceLevel }}</option>
                                @endforeach
                            </select>
                        </label>
                    </p>
                    <p>
                        <label>Ierašanās laiks<br>
                            <input type="time" name="ride_time" value="{{ old('ride_time') }}" required>
                        </label>
                    </p>
                    <button type="submit">Pieteikties</button>
                </form>
            </section>

            <section class="track-panel">
                <h2>Trases informācija</h2>
                <p><strong>Apraksts:</strong> {{ $track->description }}</p>
                <p><strong>Segums:</strong> {{ $track->surface_type ?? 'Nav norādīts' }}</p>
            </section>
        </div>

        <div class="track-grid">
            <section class="track-panel">
                <h2>Trases paziņojumi</h2>

                @auth
                    @if ($track->user_id === auth()->id())
                        <h3>Publicēt paziņojumu</h3>
                        <form method="POST" action="{{ route('announcements.store', $track) }}">
                            @csrf
                            <p>
                                <label>Virsraksts<br>
                                    <input type="text" name="title" value="{{ old('title') }}" maxlength="255" required>
                                </label>
                            </p>
                            <p>
                                <label>Ziņojums<br>
                                    <textarea name="body" rows="5" maxlength="5000" required>{{ old('body') }}</textarea>
                                </label>
                            </p>
                            <p>
                                <label>Rādīt līdz<br>
                                    <input type="datetime-local" name="expires_at" value="{{ old('expires_at') }}">
                                </label><br>
                                <small>Atstāj tukšu, lai rādītu bez termiņa.</small>
                            </p>
                            <label>
                                <input type="checkbox" name="is_pinned" value="1">
                                Atzīmēt kā svarīgu
                            </label>
                            <button type="submit">Publicēt</button>
                        </form>
                    @endif
                @endauth

                @forelse ($track->announcements as $announcement)
                    <article>
                        <h3>
                            @if ($announcement->is_pinned)
                                [Svarīgi]
                            @endif
                            {{ $announcement->title }}
                        </h3>
                        <p>{{ $announcement->body }}</p>
                        <small>
                            Publicēts {{ $announcement->published_at->format('d.m.Y H:i') }}
                            @if ($announcement->expires_at)
                                · Aktīvs līdz {{ $announcement->expires_at->format('d.m.Y H:i') }}
                            @else
                                · Bez termiņa
                            @endif
                        </small>

                        @auth
                            @if ($track->user_id === auth()->id())
                                <details>
                                    <summary>Rediģēt</summary>
                                    <form method="POST" action="{{ route('announcements.update', $announcement) }}">
                                        @csrf
                                        @method('PUT')
                                        <input type="text" name="title" value="{{ $announcement->title }}" maxlength="255" required>
                                        <textarea name="body" rows="5" maxlength="5000" required>{{ $announcement->body }}</textarea>
                                        <input type="datetime-local" name="expires_at" value="{{ $announcement->expires_at?->format('Y-m-d\\TH:i') }}">
                                        <label>
                                            <input type="checkbox" name="is_pinned" value="1" @checked($announcement->is_pinned)>
                                            Svarīgs
                                        </label>
                                        <button type="submit">Saglabāt</button>
                                    </form>
                                    <form method="POST" action="{{ route('announcements.destroy', $announcement) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit">Dzēst</button>
                                    </form>
                                </details>
                            @endif
                        @endauth
                    </article>
                @empty
                    <p>Šai trasei nav aktuālu paziņojumu.</p>
                @endforelse
            </section>

            <section class="track-panel">
                <h2>Komentāri</h2>

                @php($galleryImages = $track->images->where('type', 'gallery'))
                @if ($galleryImages->isNotEmpty())
                    <div class="track-gallery">
                        @foreach ($galleryImages as $galleryImage)
                            <img src="{{ asset('storage/' . $galleryImage->path) }}" alt="{{ $track->name }} galerijas attēls">
                        @endforeach
                    </div>
                @endif

                @auth
                    <form method="POST" action="{{ route('comments.store', $track) }}">
                        @csrf
                        <label for="comment-body">Pievienot komentāru</label><br>
                        <textarea id="comment-body" name="body" rows="4" maxlength="2000" required>{{ old('body') }}</textarea><br>
                        <button type="submit">Publicēt</button>
                    </form>
                @else
                    <p><a href="{{ route('login') }}">Ielogojies</a>, lai publicētu komentāru.</p>
                @endauth

                @forelse($track->comments as $comment)
                    <article>
                        <strong>{{ $comment->user->name }} {{ $comment->user->surname }}</strong>
                        <small>{{ $comment->created_at->format('d.m.Y H:i') }}</small>
                        <p>{{ $comment->body }}</p>

                        @auth
                            @if ($comment->user_id === auth()->id() || $track->user_id === auth()->id() || auth()->user()->role === 'admin')
                                <form method="POST" action="{{ route('comments.destroy', $comment) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit">Dzēst</button>
                                </form>
                            @endif
                        @endauth
                    </article>
                @empty
                    <p>Trasē vēl nav publicētu komentāru.</p>
                @endforelse
            </section>
        </div>

        <section class="track-panel">
            <h2>Pieteikušies sportisti</h2>

            @forelse($track->riders as $rider)
                <article>
                    <strong>{{ $rider->name }} {{ $rider->surname }}</strong><br>
                    <span>{{ $rider->category }}, {{ $rider->experience_level }}</span><br>
                    <span>{{ $rider->ride_time }}</span>
                    @if($rider->club)
                        <span> · {{ $rider->club }}</span>
                    @endif
                </article>
            @empty
                <p>Neviens neplāno ierasties.</p>
            @endforelse
        </section>
    </div>
</x-layout>
