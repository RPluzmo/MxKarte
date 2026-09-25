<x-layout>
    <div style="max-width: 1200px; margin: 0 auto; padding: 20px 16px 40px;">
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

        <div style="display: flex; flex-wrap: wrap; gap: 16px; margin-top: 16px;">
            <section style="flex: 1 1 320px; border: 1px solid #d1d5db; padding: 12px;">
                <h2>Informācija</h2>
                <p><strong>Apraksts:</strong> {{ $track->description }}</p>
                <p><strong>Segums:</strong> {{ $track->surface_type ?? 'Nav norādīts' }}</p>
            </section>

            <section style="flex: 1 1 320px; border: 1px solid #d1d5db; padding: 12px;">
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
                                </label>
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
                    <article style="border-top: 1px solid #e5e7eb; margin-top: 12px; padding-top: 12px;">
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
        </div>

        <div style="display: flex; flex-wrap: wrap; gap: 16px; margin-top: 16px;">
            <section style="flex: 1 1 320px; border: 1px solid #d1d5db; padding: 12px;">
                <h3>Pieteikties treniņam</h3>
                <form method="POST" action="{{ route('riders.store', $track) }}">
                    @csrf
                    @php
                        $authUser = auth()->user();
                    @endphp
                    <div>
                        <label>Vārds</label><br>
                        <input type="text" name="name" value="{{ old('name', $authUser?->name ?? '') }}" required>
                    </div>

                    <div>
                        <label>Uzvārds</label><br>
                        <input type="text" name="surname" value="{{ old('surname', $authUser?->surname ?? '') }}" required>
                    </div>

                    <div>
                        <label>Klubs (neobligāti)</label><br>
                        <select name="club">
                            <option value="">Nav izvēlēts</option>
                            @foreach ($clubs as $club)
                                <option value="{{ $club->name }}" {{ old('club', $authUser?->club) === $club->name ? 'selected' : '' }}>
                                    {{ $club->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label>Klase</label><br>
                        <select name="category" required>
                            <option value="">Izvēlieties</option>
                            <option value="MX 50" {{ old('category', $authUser?->category) === 'MX 50' ? 'selected' : '' }}>MX 50</option>
                            <option value="MX 65" {{ old('category', $authUser?->category) === 'MX 65' ? 'selected' : '' }}>MX 65</option>
                            <option value="MX 85" {{ old('category', $authUser?->category) === 'MX 85' ? 'selected' : '' }}>MX 85</option>
                            <option value="MX 125" {{ old('category', $authUser?->category) === 'MX 125' ? 'selected' : '' }}>MX 125</option>
                            <option value="MX 250" {{ old('category', $authUser?->category) === 'MX 250' ? 'selected' : '' }}>MX 250</option>
                            <option value="MX 450" {{ old('category', $authUser?->category) === 'MX 450' ? 'selected' : '' }}>MX 450</option>
                            <option value="Kvadri" {{ old('category', $authUser?->category) === 'Kvadri' ? 'selected' : '' }}>Kvadri</option>
                            <option value="Blakusvāģi" {{ old('category', $authUser?->category) === 'Blakusvāģi' ? 'selected' : '' }}>Blakusvāģi</option>
                        </select>
                    </div>

                    <div>
                        <label>Pieredze</label><br>
                        <select name="experience_level" required>
                            <option value="">Izvēlieties</option>
                            <option value="Iesācējs" {{ old('experience_level', $authUser?->experience_level) === 'Iesācējs' ? 'selected' : '' }}>Iesācējs</option>
                            <option value="Amatieris" {{ old('experience_level', $authUser?->experience_level) === 'Amatieris' ? 'selected' : '' }}>Amatieris</option>
                            <option value="Veterāns" {{ old('experience_level', $authUser?->experience_level) === 'Veterāns' ? 'selected' : '' }}>Veterāns</option>
                            <option value="Profesionālis" {{ old('experience_level', $authUser?->experience_level) === 'Profesionālis' ? 'selected' : '' }}>Profesionālis</option>
                        </select>
                    </div>

                    <div>
                        <label>Ierašanās laiks</label><br>
                        <input type="time" name="ride_time" value="{{ old('ride_time') }}" required>
                    </div>

                    <button type="submit">Pieteikties</button>
                </form>
            </section>

            <section style="flex: 1 1 320px; border: 1px solid #d1d5db; padding: 12px;">
                <h3>Pieteikušies sportisti</h3>

                @forelse($track->riders as $rider)
                    <div style="border-top: 1px solid #e5e7eb; padding-top: 8px; margin-top: 8px;">
                        <strong>{{ $rider->name }} {{ $rider->surname }}</strong><br>
                        <span>{{ $rider->category }}, {{ $rider->experience_level }}</span><br>
                        <span>{{ $rider->ride_time }}</span>
                        @if($rider->club)
                            <span> · {{ $rider->club }}</span>
                        @endif
                    </div>
                @empty
                    <p>Neviens neplāno ierasties.</p>
                @endforelse
            </section>
        </div>

        <section style="border: 1px solid #d1d5db; padding: 12px; margin-top: 16px;">
            <h3>Komentāri</h3>
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
                <article style="border-top: 1px solid #e5e7eb; margin-top: 12px; padding-top: 12px;">
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
</x-layout>
