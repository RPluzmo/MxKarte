<x-layout>
    <div class="page-shell"><section class="panel">
        <h1>Admin ziņojumi</h1>
        @include('admin.partials.nav')

        <x-alerts />

        <p><a href="{{ route('admin.announcements.create') }}">Publicēt ziņojumu</a></p>

        <table>
            <thead>
                <tr><th>Virsraksts</th><th>Autors</th><th>Publicēts</th><th>Termiņš</th><th>Darbības</th></tr>
            </thead>
            <tbody>
                @forelse ($announcements as $announcement)
                    <tr>
                        <td>{{ $announcement->title }}</td>
                        <td>{{ $announcement->user?->name ?? 'Dzēsts lietotājs' }}</td>
                        <td>{{ $announcement->published_at->format('d.m.Y H:i') }}</td>
                        <td>{{ $announcement->expires_at?->format('d.m.Y H:i') ?? 'Bez termiņa' }}</td>
                        <td>
                            <a href="{{ route('admin.announcements.edit', $announcement) }}">Rediģēt</a>
                            <form method="POST" action="{{ route('admin.announcements.destroy', $announcement) }}" class="inline-form" onsubmit="return confirm('Dzēst šo admin ziņojumu?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit">Dzēst</button>
                            </form>
                        </td>
                    </tr>
                    <tr><td colspan="5">{{ $announcement->body }}</td></tr>
                @empty
                    <tr><td colspan="5">Admin ziņojumu vēl nav.</td></tr>
                @endforelse
            </tbody>
        </table>

        {{ $announcements->links() }}
    </section></div>
</x-layout>
