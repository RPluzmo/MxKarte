<x-layout>
    <main style="max-width: 1200px; margin: 0 auto; padding: 20px 16px;">
        <h1>Lietotāji</h1>
        @include('admin.partials.nav')

        @if (session('status'))
            <p>{{ session('status') }}</p>
        @endif

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
                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}" style="display: inline;" onsubmit="return confirm('Dzēst šo lietotāju?');">
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
    </main>
</x-layout>
