<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ isset($title) && $title !== 'MxKarte' ? $title . ' | MxKarte' : 'MxKarte' }}</title>
    @if (file_exists(public_path('images/MxK.png')))
        <link rel="icon" type="image/png" href="{{ asset('images/MxK.png') }}">
    @else
        <link rel="icon" href="{{ asset('favicon.ico') }}">
    @endif
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body>
    <x-navbar />

    <main class="main-content">
        {{ $slot }}
    </main>

    @stack('scripts')
    @stack('dialogs')
</body>
</html>
