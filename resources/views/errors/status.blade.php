<x-layout>
    <div class="page-shell page-shell-narrow">
        <section class="panel error-page">
            <p class="error-page-code">{{ $status }}</p>
            <h1>{{ $title }}</h1>
            <p>{{ $message }}</p>
            <a class="button button-secondary" href="{{ route('home') }}">Atgriezties sākumlapā</a>
        </section>
    </div>
</x-layout>
