@if (session('status'))
    <p class="alert alert-success" role="status">{{ session('status') }}</p>
@endif

@if ($errors->any())
    <ul class="alert alert-error" role="alert">
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
@endif
