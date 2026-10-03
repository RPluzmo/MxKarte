<header class="site-header">
    <nav class="site-nav" aria-label="Galvenā navigācija">
        <ul class="site-nav-list">
            <li><a href="{{ route('home') }}">Karte</a></li>

            @auth
                <li><a href="{{ route('profile.edit') }}">Profils</a></li>
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
                <button id="toggle-color-vision" type="button" aria-pressed="false">
                    Krāsu pieejamības režīms: izslēgts
                </button>
            </div>
        @endif
    </nav>
</header>