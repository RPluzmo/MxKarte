<x-layout>
    <div class="page-shell">
        <a href="/">Atpakaļ</a>
        <h1>{{ $track->name }}</h1>

        <x-alerts />

        @auth
            @if ($track->user_id === auth()->id())
                <p><a href="{{ route('tracks.edit', $track) }}">Rediģēt trasi</a></p>
            @endif
        @endauth

        @php($coverImage = $track->images->firstWhere('type', 'cover'))
        @if ($coverImage)
            <section class="track-panel">
                <img class="track-cover js-track-image" src="{{ asset('storage/' . $coverImage->path) }}" alt="{{ $track->name }}" role="button" tabindex="0" aria-label="Atvērt attēlu pilnekrānā">
            </section>
        @endif

        <div class="track-grid">
            <section class="track-panel">
                <h2>Pieteikties treniņam</h2>
                <form method="POST" action="{{ route('riders.store', $track) }}">
                    @csrf
                    @php($authUser = auth()->user())

                    @auth
                        <p><strong>Vārds:</strong> {{ $authUser->name }}</p>
                        <p><strong>Uzvārds:</strong> {{ $authUser->surname ?: '-' }}</p>
                        <p><strong>Klubs:</strong> {{ $authUser->club ?: 'Privāti' }}</p>
                        <p><strong>Klase:</strong> {{ $authUser->category ?: '-' }}</p>
                        <p><strong>Pieredze:</strong> {{ $authUser->experience_level ?: '-' }}</p>
                        <p>Datus var mainīt <a href="{{ route('profile.edit') }}">profilā</a>.</p>
                    @else
                    <p>
                        <label>Vārds<br>
                            <input type="text" name="name" value="{{ old('name') }}" required>
                        </label>
                    </p>
                    <p>
                        <label>Uzvārds<br>
                            <input type="text" name="surname" value="{{ old('surname') }}" required>
                        </label>
                    </p>
                    <x-choice-picker
                        key="club"
                        name="club"
                        label="Klubs (neobligāti)"
                        title="Izvēlies motoklubu"
                        search="Meklēt klubu"
                        button="Atzīmēt motokluba piederību"
                        empty="Klubi nav atrasti."
                        none="Privāti"
                        none-hint="Nav kluba logo"
                        :options="$clubs"
                        :selected="old('club')"
                    />
                    <x-choice-picker
                        key="category"
                        name="category"
                        label="Klase"
                        title="Izvēlies motocikla kategoriju"
                        search="Meklēt kategoriju"
                        button="Izvēlēties kategoriju"
                        empty="Kategorijas nav atrastas."
                        :show-image="false"
                        :options="$categories"
                        :selected="old('category')"
                    />
                    <x-choice-picker
                        key="experience"
                        name="experience_level"
                        label="Pieredze"
                        title="Izvēlies pieredzes līmeni"
                        search="Meklēt pieredzi"
                        button="Izvēlēties pieredzi"
                        empty="Pieredzes līmeņi nav atrasti."
                        :show-image="false"
                        :options="$experienceLevels"
                        :selected="old('experience_level')"
                    />
                    @endauth
                    <p>
                        <label>Ierašanās laiks<br>
                            <input type="time" name="ride_time" value="{{ old('ride_time') }}" min="06:00" max="23:59" required>
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

        @php($galleryImages = $track->images->where('type', 'gallery'))
        @if ($galleryImages->isNotEmpty())
            <section class="track-panel">
                <h2>Trases attēli</h2>
                <div class="track-gallery">
                    @foreach ($galleryImages as $galleryImage)
                        <img class="js-track-image" src="{{ asset('storage/' . $galleryImage->path) }}" alt="{{ $track->name }} trases foto" role="button" tabindex="0" aria-label="Atvērt attēlu pilnekrānā" loading="lazy">
                    @endforeach
                </div>
            </section>
        @endif

        <section class="track-panel">
            <h2>Pieteikušies sportisti</h2>
<div class="table-scroll">
<table class="rider-table">
    <thead>
        <tr>
            <th>Vārds</th>
            <th>Uzvārds</th>
            <th>Klase</th>
            <th>Pieredzes līmenis</th>
            <th>Ierašanās laiks</th>
            <th>Moto klubs</th>
        </tr>
    </thead>
    <tbody>
            @php($ridersByPeriod = $track->riders->sortBy('ride_time')->groupBy('arrival_period'))
            @foreach (['Rīts', 'Pusdienlaiks', 'Pēcpusdiena', 'Vakars'] as $period)
                <tr class="rider-period"><th colspan="6">{{ $period }}</th></tr>
                @forelse ($ridersByPeriod->get($period, collect()) as $rider)
                    <tr>
                        <td>{{ $rider->name }}</td>
                        <td>{{ $rider->surname }}</td>
                        <td>{{ $rider->category }}</td>
                        <td>{{ $rider->experience_level }}</td>
                        <td>{{ substr($rider->ride_time, 0, 5) }}</td>
                        <td>
                            @if ($rider->club)
                                <div class="rider-club">
                                    @if ($rider->clubModel?->logo_path)
                                        <img
                                            class="rider-club-logo"
                                            src="{{ asset('storage/' . $rider->clubModel->logo_path) }}"
                                            alt="{{ $rider->club }} logo"
                                            loading="lazy"
                                        >
                                    @endif
                                    <span>{{ $rider->club }}</span>
                                </div>
                            @else
                                <span>Privāti</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6">Šajā laikā nav pieteikumu.</td></tr>
                @endforelse
            @endforeach
        </tbody>
    </table>
    </div>
        </section>
    </div>

    <dialog id="track-image-viewer" class="track-image-viewer" aria-label="Trases attēls pilnekrānā">
        <img id="track-image-viewer-image" alt="">
    </dialog>
</x-layout>
