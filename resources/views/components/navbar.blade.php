<header>
    <nav>
        <ul>
            <li><a href="{{ route('home') }}">Karte</a></li>
            @auth
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
    </nav>
</header>