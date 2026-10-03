<x-layout>
    <main style="max-width: 800px; margin: 0 auto; padding: 20px 16px;">
        <h1>{{ $announcement->exists ? 'Rediģēt admin ziņojumu' : 'Publicēt admin ziņojumu' }}</h1>
        @include('admin.partials.nav')

        @if ($errors->any())
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        @endif

        <form method="POST" action="{{ $announcement->exists ? route('admin.announcements.update', $announcement) : route('admin.announcements.store') }}">
            @csrf
            @if ($announcement->exists)
                @method('PUT')
            @endif

            <p><label>Virsraksts<br><input name="title" value="{{ old('title', $announcement->title) }}" maxlength="255" required></label></p>
            <p><label>Ziņojums<br><textarea name="body" rows="6" maxlength="5000" required>{{ old('body', $announcement->body) }}</textarea></label></p>
            <p><label>Rādīt līdz<br><input type="datetime-local" name="expires_at" value="{{ old('expires_at', $announcement->expires_at?->format('Y-m-d\\TH:i')) }}"></label><br><small>Atstāj tukšu, lai rādītu bez termiņa.</small></p>

            <p>
                <button type="submit">Saglabāt un publicēt</button>
                <a href="{{ route('admin.announcements.index') }}">Atcelt</a>
            </p>
        </form>
    </main>
</x-layout>
