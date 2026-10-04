<x-layout>
    <div class="page-shell"><section class="panel">
        <h1>Trases</h1>
        @include('admin.partials.nav')

        <x-alerts />

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
                            <form method="POST" action="{{ route('admin.tracks.destroy', $track) }}" class="inline-form" onsubmit="return confirm('Dzēšot trasi, tiks dzēsti tās pieteikumi, komentāri un paziņojumi. Turpināt?');">
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
    </section></div>
</x-layout>
