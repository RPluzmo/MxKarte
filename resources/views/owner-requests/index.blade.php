<x-layout>
    <x-slot:title>Mani trases saimnieka pieteikumi</x-slot:title>
    <div class="page-shell page-shell-narrow"><section class="panel">
        <h1>Trases saimnieka piekļuves pieteikumi</h1>
        <x-alerts />

        <p><a class="button button-secondary" href="{{ route('owner-requests.create') }}">Pieteikties trases pārvaldīšanai</a></p>
        <p>Pieteikumu izskata administrators. Saimnieka tiesības un trases piekļuve tiek piešķirta tikai pēc pārbaudes.</p>

        @forelse ($requests as $accessRequest)
            <article class="panel">
                <h2>{{ $accessRequest->track->name }}</h2>
                <p><strong>Statuss:</strong>
                    @switch($accessRequest->status)
                        @case('approved') Apstiprināts @break
                        @case('rejected') Noraidīts @break
                        @default Gaida administratora pārbaudi
                    @endswitch
                </p>
                <p>{{ $accessRequest->message }}</p>
                <small>Iesniegts {{ $accessRequest->created_at->format('d.m.Y H:i') }}</small>
            </article>
        @empty
            <p>Te vēl nav pieteikumu.</p>
        @endforelse

        {{ $requests->links() }}
    </section></div>
</x-layout>
