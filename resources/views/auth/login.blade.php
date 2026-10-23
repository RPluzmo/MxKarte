<x-layout>
    <x-slot:title>Ienākt</x-slot:title>
    <div class="page-shell page-shell-narrow">
    <section class="panel">
    <h1>Ienākt</h1>

    <form class="stack-form" action="{{ route('login') }}" method="POST">
        @csrf

        <x-alerts />

        <label>E-pasts
            <input type="email" name="email" value="{{ old('email') }}" autocomplete="email" required>
            <x-field-error field="email" />
        </label>

        <label>Parole
            <span class="password-input-group">
                <input type="password" name="password" autocomplete="current-password" required data-password-visibility-input>
                <button class="button-secondary password-visibility-toggle" type="button" data-password-visibility-toggle aria-pressed="false">
                    Rādīt paroli
                </button>
            </span>
            <x-field-error field="password" />
        </label>

        <button type="submit">Ienākt</button>
        <x-field-error field="rate_limit" />
    </form>
    </section>
    </div>
</x-layout>
