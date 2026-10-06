<header class="site-header">
    <nav class="site-nav" aria-label="Galvenā navigācija">
        <a class="site-brand" href="{{ route('home') }}">MxKarte</a>

        <ul class="site-nav-list">
            <li><a href="{{ route('home') }}" @if (request()->routeIs('home')) aria-current="page" @endif>Karte</a></li>

            @auth
                <li><a href="{{ route('profile.edit') }}" @if (request()->routeIs('profile.edit')) aria-current="page" @endif>Profils</a></li>
                @if (auth()->user()->role === 'admin')
                    <li><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                @endif
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit">Iziet</button>
                    </form>
                </li>
            @else
                <li><a href="{{ route('login') }}">Ienākt</a></li>
                <li><a href="{{ route('register') }}">Reģistrēties</a></li>
            @endauth
        </ul>

        @if (request()->routeIs('home'))
            <div class="site-nav-accessibility">
                <button
                    id="toggle-color-vision"
                    class="accessibility-toggle"
                    type="button"
                    aria-label="Krāsu pieejamības režīms: izslēgts"
                    aria-pressed="false"
                    data-tooltip="Krāsu pieejamības režīms: izslēgts"
                >
                    <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M2.5 12s3.4-6.5 9.5-6.5 9.5 6.5 9.5 6.5-3.4 6.5-9.5 6.5S2.5 12 2.5 12Z" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                        <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2" />
                    </svg>
                </button>
            </div>
        @endif
    </nav>
</header>
