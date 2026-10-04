<x-layout>
    <div class="page-shell page-shell-narrow">
    <section class="panel">
    <h1>Reģistrēties</h1>

    <form class="stack-form" action="{{ route('register') }}" method="POST">
        @csrf

        <x-alerts />

        <label>Vārds
            <input name="name" value="{{ old('name') }}" autocomplete="given-name" required>
        </label>

        <label>Uzvārds
            <input name="surname" value="{{ old('surname') }}" autocomplete="family-name" required>
        </label>

        <label>E-pasts
            <input type="email" name="email" value="{{ old('email') }}" autocomplete="email" required>
        </label>

        <label>Parole
            <input type="password" name="password" autocomplete="new-password" required>
        </label>

        <label>Parole atkārtoti
            <input type="password" name="password_confirmation" autocomplete="new-password" required>
        </label>

        <button type="submit">Izveidot lietotāj profilu</button>
    </form>
    </section>
    </div>
</x-layout>