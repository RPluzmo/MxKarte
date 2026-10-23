<x-layout>
    <x-slot:title>Pieteikties trases saimnieka piekļuvei</x-slot:title>
    <div class="page-shell page-shell-narrow"><section class="panel">
        <h1>Pieteikties trases saimnieka piekļuvei</h1>
        <x-alerts />
        <p>!Pieteikuma iesniegšana nepiešķir piekļuvi automātiski. Administrators pārbaudīs saistību ar trasi un pēc apstiprināšanas piešķirs saimnieka lomu!</p>

        @if ($tracks->isEmpty())
            <p>Pašlaik nav trašu.</p>
        @else
            <form class="stack-form" method="POST" action="{{ route('owner-requests.store') }}">
                @csrf
                <label class="field">Trase
                    <select name="track_id" required>
                        <option value="">Izvēlies trasi</option>
                        @foreach ($tracks as $track)
                            <option value="{{ $track->id }}" @selected(old('track_id') == $track->id)>{{ $track->name }}</option>
                        @endforeach
                    </select>
                    <x-field-error field="track_id" />
                </label>

                <label class="field">Pamatojums un pārbaudei noderīga kontaktinformācija
                    <textarea name="message" rows="6" minlength="20" maxlength="2000" required>{{ old('message') }}</textarea>
                    <small>Norādi, kā esi saistīts ar trasi un kā administrators var sazināties ar jums.</small>
                    <x-field-error field="message" />
                </label>

                <button type="submit">Nosūtīt pieteikumu</button>
            </form>
        @endif
    </section></div>
</x-layout>
