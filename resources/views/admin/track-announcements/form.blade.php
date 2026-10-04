<x-layout>
    <div class="page-shell page-shell-narrow"><section class="panel">
        <h1>{{ $announcement->exists ? 'Rediģēt trases paziņojumu' : 'Izveidot trases paziņojumu' }}</h1>
        @include('admin.partials.nav')

        <x-alerts />

        <form method="POST" action="{{ $announcement->exists ? route('admin.track-announcements.update', $announcement) : route('admin.track-announcements.store') }}">
            @csrf
            @if ($announcement->exists)
                @method('PUT')
            @endif

            <p>
                <label>Trase<br>
                    <select name="track_id" required>
                        <option value="">Izvēlies trasi</option>
                        @foreach ($tracks as $track)
                            <option value="{{ $track->id }}" @selected((string) old('track_id', $announcement->track_id) === (string) $track->id)>
                                {{ $track->name }}
                            </option>
                        @endforeach
                    </select>
                </label>
            </p>
            <p><label>Virsraksts<br><input name="title" value="{{ old('title', $announcement->title) }}" maxlength="255" required></label></p>
            <p><label>Ziņojums<br><textarea name="body" rows="6" maxlength="5000" required>{{ old('body', $announcement->body) }}</textarea></label></p>
            <p><label>Rādīt līdz<br><input type="datetime-local" name="expires_at" value="{{ old('expires_at', $announcement->expires_at?->format('Y-m-d\\TH:i')) }}"></label><br><small>Atstāj tukšu, lai rādītu bez termiņa.</small></p>
            <label><input type="checkbox" name="is_pinned" value="1" @checked(old('is_pinned', $announcement->is_pinned))> Atzīmēt kā svarīgu</label>

            <p>
                <button type="submit">Saglabāt</button>
                <a href="{{ route('admin.track-announcements.index') }}">Atcelt</a>
            </p>
        </form>
    </section></div>
</x-layout>
