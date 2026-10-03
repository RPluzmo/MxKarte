<x-layout>
    <main style="max-width: 1200px; margin: 0 auto; padding: 20px 16px;">
        <h1>Trases</h1>
        @include('admin.partials.nav')

        @if (session('status'))
            <p>{{ session('status') }}</p>
        @endif

        <p><a href="{{ route('admin.tracks.create') }}">Izveidot trasi</a></p>

        <table>
            <thead>
                <tr><th>Nosaukums</th><th>Slug</th><th>Saimnieks</th><th>Darbības</th></tr>
            </thead>
            <tbody>
                @foreach ($tracks as $track)
                    <tr>
                        <td>{{ $track->name }}</td>
                        <td>{{ $track->slug }}</td>
                        <td>{{ $track->owner?->name ?? 'Nav piešķirts' }}</td>
                        <td>
                            <a href="{{ route('tracks.show', $track) }}">Skatīt</a>
                            <a href="{{ route('admin.tracks.edit', $track) }}">Rediģēt</a>
                            <form method="POST" action="{{ route('admin.tracks.destroy', $track) }}" style="display: inline;" onsubmit="return confirm('Dzēšot trasi, tiks dzēsti tās pieteikumi, komentāri un paziņojumi. Turpināt?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit">Dzēst</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{ $tracks->links() }}
    </main>
</x-layout>
