<style>
    .site-header {
        padding: 12px 16px;
    }

    .site-nav-list {
        align-items: center;
        display: flex;
        flex-wrap: wrap;
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .site-nav-list li + li {
        border-left: 1px solid #9ca3af;
        margin-left: 12px;
        padding-left: 12px;
    }

    .site-nav-list a,
    .site-nav-list button {
        background: none;
        border: 0;
        color: inherit;
        cursor: pointer;
        font: inherit;
        padding: 0;
    }

    .site-nav-list form {
        margin: 0;
    }
</style>

<header class="site-header">
    <nav aria-label="Galvenā navigācija">
        <ul class="site-nav-list">
            <li><a href="{{ route('home') }}">Karte</a></li>

            @auth
                <li><a href="{{ route('profile.edit') }}">Profils</a></li>
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