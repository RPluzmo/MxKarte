<x-layout>
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
            <input type="password" name="password" autocomplete="current-password" required>
            <x-field-error field="password" />
        </label>

        <button type="submit">Ienākt</button>
        <x-field-error field="rate_limit" />
    </form>
    </section>
    </div>
</x-layout>