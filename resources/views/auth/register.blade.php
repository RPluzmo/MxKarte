<x-layout>
    <x-slot:title>Reģistrēties</x-slot:title>
    <div class="page-shell page-shell-narrow">
    <section class="panel">
    <h1>Reģistrēties</h1>

    <form class="stack-form" action="{{ route('register') }}" method="POST">
        @csrf

        <x-alerts />

        <label>Vārds
            <input name="name" value="{{ old('name') }}" autocomplete="given-name" required>
            <x-field-error field="name" />
        </label>

        <label>Uzvārds
            <input name="surname" value="{{ old('surname') }}" autocomplete="family-name" required>
            <x-field-error field="surname" />
        </label>

        <label>E-pasts
            <input type="email" name="email" value="{{ old('email') }}" autocomplete="email" required>
            <x-field-error field="email" />
        </label>

        <label>Parole
            <span class="password-input-group">
                <input type="password" name="password" autocomplete="new-password" required data-password-visibility-input>
                <button class="button-secondary password-visibility-toggle" type="button" data-password-visibility-toggle aria-pressed="false">
                    Rādīt paroli
                </button>
            </span>
            <small>Vismaz 9 rakstzīmes, tajā skaitā burti, cipari un īpašais simbols.</small>
            <x-field-error field="password" />
        </label>

        <label>Parole atkārtoti
            <span class="password-input-group">
                <input type="password" name="password_confirmation" autocomplete="new-password" required data-password-visibility-input>
                <button class="button-secondary password-visibility-toggle" type="button" data-password-visibility-toggle aria-pressed="false">
                    Rādīt paroli
                </button>
            </span>
            <x-field-error field="password_confirmation" />
        </label>

        <button type="submit">Izveidot lietotāj profilu</button>
        <x-field-error field="rate_limit" />
    </form>
    </section>
    </div>
</x-layout>
