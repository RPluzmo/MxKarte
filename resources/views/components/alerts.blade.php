@if (session('status'))
    <p class="alert alert-success" role="status">{{ session('status') }}</p>
@endif

@if ($errors->any())
    <p class="alert alert-error" role="alert">Ir kļūda. Lūdzu, pārbaudi norādi pie attiecīgā lauka.</p>
@endif
