<x-layout>
    <div class="page-shell"><section class="panel">
        <h1>Lietotāji</h1>
        @include('admin.partials.nav')

        <x-alerts />

        <p><a href="{{ route('admin.users.create') }}">Izveidot lietotāju</a></p>

        <table>
            <thead>
                <tr><th>Vārds</th><th>E-pasts</th><th>Loma</th><th>Trases</th><th>Darbības</th></tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr>
                        <td>{{ $user->name }} {{ $user->surname }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->role }}</td>
                        <td>{{ $user->tracks_count }}</td>
                        <td>
                            <a href="{{ route('admin.users.edit', $user) }}">Rediģēt</a>
                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="inline-form" onsubmit="return confirm('Dzēst šo lietotāju?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit">Dzēst</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{ $users->links() }}
    </section></div>
</x-layout>
