<x-layout>
    <main style="max-width: 1200px; margin: 0 auto; padding: 20px 16px;">
        <h1>Trases paziņojumi</h1>
        @include('admin.partials.nav')

        @if (session('status'))
            <p>{{ session('status') }}</p>
        @endif

        <p><a href="{{ route('admin.track-announcements.create') }}">Izveidot trases paziņojumu</a></p>

        <table>
            <thead>
                <tr><th>Trase</th><th>Virsraksts</th><th>Autors</th><th>Publicēts</th><th>Termiņš</th><th>Svarīgs</th><th>Darbības</th></tr>
            </thead>
            <tbody>
                @forelse ($announcements as $announcement)
                    <tr>
                        <td>{{ $announcement->track?->name ?? 'Dzēsta trase' }}</td>
                        <td>{{ $announcement->title }}</td>
                        <td>{{ $announcement->user?->name ?? 'Dzēsts lietotājs' }}</td>
                        <td>{{ $announcement->published_at->format('d.m.Y H:i') }}</td>
                        <td>{{ $announcement->expires_at?->format('d.m.Y H:i') ?? 'Bez termiņa' }}</td>
                        <td>{{ $announcement->is_pinned ? 'Jā' : 'Nē' }}</td>
                        <td>
                            <a href="{{ route('admin.track-announcements.edit', $announcement) }}">Rediģēt</a>
                            <form method="POST" action="{{ route('admin.track-announcements.destroy', $announcement) }}" style="display: inline;" onsubmit="return confirm('Dzēst šo trases paziņojumu?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit">Dzēst</button>
                            </form>
                        </td>
                    </tr>
                    <tr><td colspan="7">{{ $announcement->body }}</td></tr>
                @empty
                    <tr><td colspan="7">Trases paziņojumu vēl nav.</td></tr>
                @endforelse
            </tbody>
        </table>

        {{ $announcements->links() }}
    </main>
</x-layout>
