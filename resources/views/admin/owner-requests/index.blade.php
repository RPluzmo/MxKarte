<x-layout>
    <x-slot:title>Trases saimnieka piekļuves pieteikumi</x-slot:title>
    <div class="page-shell"><section class="panel">
        <h1>Trases saimnieka piekļuves pieteikumi</h1>
        @include('admin.partials.nav')
        <x-alerts />

        <table class="data-table data-table--interactive">
            <thead>
                <tr><th>Lietotājs</th><th>Trase</th><th>Pamatojums</th><th>Statuss</th><th>Iesniegts</th><th>Darbības</th></tr>
            </thead>
            <tbody>
                @forelse ($requests as $accessRequest)
                    <tr>
                        <td>{{ $accessRequest->user->name }} {{ $accessRequest->user->surname }}<br>{{ $accessRequest->user->email }}</td>
                        <td>{{ $accessRequest->track->name }}<br><small>{{ $accessRequest->track->owner?->email ?? 'Saimnieks nav piešķirts' }}</small></td>
                        <td>{{ $accessRequest->message }}</td>
                        <td>
                            @switch($accessRequest->status)
                                @case('approved') Apstiprināts @break
                                @case('rejected') Noraidīts @break
                                @default Gaida pārbaudi
                            @endswitch
                        </td>
                        <td>{{ $accessRequest->created_at->format('d.m.Y H:i') }}</td>
                        <td>
                            @if ($accessRequest->status === 'pending')
                                <form method="POST" action="{{ route('admin.owner-requests.approve', $accessRequest) }}" class="inline-form" onsubmit="return confirm('Pārbaudi lietotāja saistību ar trasi pirms piekļuves piešķiršanas. Apstiprināt?');">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit">Apstiprināt</button>
                                </form>
                                <form method="POST" action="{{ route('admin.owner-requests.reject', $accessRequest) }}" class="inline-form" onsubmit="return confirm('Noraidīt šo pieteikumu?');">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit">Noraidīt</button>
                                </form>
                            @else
                                {{ $accessRequest->reviewer?->name }}
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6">Pieteikumu vēl nav.</td></tr>
                @endforelse
            </tbody>
        </table>

        {{ $requests->links() }}
    </section></div>
</x-layout>
