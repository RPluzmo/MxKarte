<x-layout>
    <x-slot:title>Profils</x-slot:title>

    @php($selectedClub = old('club', $user->club))
    @php($selectedClubModel = $clubs->firstWhere('name', $selectedClub))

    <main class="profile-page">
        <section class="profile-panel">
            <h1>Profils</h1>

            @if (session('status'))
                <p>{{ session('status') }}</p>
            @endif

            <form class="profile-form" action="{{ route('profile.update') }}" method="POST">
                @csrf
                @method('PUT')

                @if ($errors->any())
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                @endif

                <label class="profile-field">Vārds
                    <input name="name" value="{{ old('name', $user->name) }}" required>
                </label>

                <label class="profile-field">Uzvārds
                    <input name="surname" value="{{ old('surname', $user->surname ?? '') }}">
                </label>

                <label class="profile-field">E-pasts
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required>
                </label>

                <div class="profile-field">
                    <span>Kluba piederība</span>
                    <input id="profile-club-value" type="hidden" name="club" value="{{ $selectedClub }}">
                    <div class="profile-club-control">
                        <div class="profile-selected-club">
                            <img
                                id="selected-club-logo"
                                src="{{ $selectedClubModel?->logo_path ? asset('storage/' . $selectedClubModel->logo_path) : '' }}"
                                alt=""
                                @if (!$selectedClubModel?->logo_path) hidden @endif
                            >
                            <output id="selected-club-label">{{ $selectedClub ?: 'Privāti' }}</output>
                        </div>
                        <button id="open-club-picker" type="button">Atzīmēt motokluba piederību</button>
                    </div>
                </div>

                <label class="profile-field">Kategorija
                    <select name="category">
                        <option value="">Nav izvēlēts</option>
                        @foreach (['MX 50', 'MX 65', 'MX 85', 'MX 125', 'MX 250', 'MX 450', 'Kvadri', 'Blakusvāģi'] as $category)
                            <option value="{{ $category }}" @selected(old('category', $user->category) === $category)>{{ $category }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="profile-field">Pieredze
                    <select name="experience_level">
                        <option value="">Nav izvēlēts</option>
                        @foreach (['Iesācējs', 'Amatieris', 'Veterāns', 'Profesionālis'] as $level)
                            <option value="{{ $level }}" @selected(old('experience_level', $user->experience_level) === $level)>{{ $level }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="profile-field">Jauna parole
                    <input type="password" name="password">
                </label>

                <label class="profile-field">Jauna parole atkārtoti
                    <input type="password" name="password_confirmation">
                </label>

                <button type="submit">Saglabāt</button>
            </form>
        </section>

        <dialog id="club-picker-dialog" class="profile-dialog" aria-labelledby="club-picker-title">
            <div class="profile-dialog-header">
                <h2 id="club-picker-title">Izvēlies motoklubu</h2>
                <form method="dialog">
                    <button type="submit">Aizvērt</button>
                </form>
            </div>

            <label class="profile-field" for="club-search">Meklēt klubu</label>
            <input id="club-search" class="club-search" type="search" placeholder="Ieraksti kluba nosaukumu">

            <div class="club-grid">
                <button class="club-option" type="button" data-club-option data-club-value="" data-club-name="Privāti" aria-pressed="{{ $selectedClub ? 'false' : 'true' }}">
                    <span class="club-option-placeholder">Nav kluba logo</span>
                    <span class="club-option-label">Privāti</span>
                </button>

                @foreach ($clubs as $club)
                    <button
                        class="club-option"
                        type="button"
                        data-club-option
                        data-club-value="{{ $club->name }}"
                        data-club-name="{{ $club->name }}"
                        data-club-logo="{{ $club->logo_path ? asset('storage/' . $club->logo_path) : '' }}"
                        aria-pressed="{{ $selectedClub === $club->name ? 'true' : 'false' }}"
                    >
                        @if ($club->logo_path)
                            <img src="{{ asset('storage/' . $club->logo_path) }}" alt="" loading="lazy">
                        @else
                            <span class="club-option-placeholder">Nav logo</span>
                        @endif
                        <span class="club-option-label">{{ $club->name }}</span>
                    </button>
                @endforeach
            </div>

            <p id="club-search-empty" hidden>Klubi nav atrasti.</p>
        </dialog>
    </main>
</x-layout>