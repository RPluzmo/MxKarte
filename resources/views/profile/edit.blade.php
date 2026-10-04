<x-layout>
    <x-slot:title>Profils</x-slot:title>

    @php($selectedClub = old('club', $user->club))
    @php($selectedClubModel = $clubs->firstWhere('name', $selectedClub))
    @php($selectedCategory = old('category', $user->category))
    @php($selectedCategoryData = collect($categories)->firstWhere('name', $selectedCategory))

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
                        <div class="profile-selected-choice">
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

                <div class="profile-field">
                    <span>Motocikla kategorija</span>
                    <input id="profile-category-value" type="hidden" name="category" value="{{ $selectedCategory }}">
                    <div class="profile-club-control">
                        <div class="profile-selected-choice">
                            <img
                                id="selected-category-image"
                                src="{{ $selectedCategoryData['image_url'] ?? '' }}"
                                alt=""
                                @if (!$selectedCategoryData || !$selectedCategoryData['image_url']) hidden @endif
                            >
                            <output id="selected-category-label">{{ $selectedCategory ?: 'Nav izvēlēts' }}</output>
                        </div>
                        <button id="open-category-picker" type="button">Izvēlēties kategoriju</button>
                    </div>
                </div>

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
            <input id="club-search" class="choice-search" type="search" placeholder="Ieraksti kluba nosaukumu">

            <div class="choice-grid">
                <button class="choice-option" type="button" data-choice-option data-choice-value="" data-choice-name="Privāti" data-choice-image="" aria-pressed="{{ $selectedClub ? 'false' : 'true' }}">
                    <span class="choice-option-placeholder">Nav kluba logo</span>
                    <span class="choice-option-label">Privāti</span>
                </button>

                @foreach ($clubs as $club)
                    <button
                        class="choice-option"
                        type="button"
                        data-choice-option
                        data-choice-value="{{ $club->name }}"
                        data-choice-name="{{ $club->name }}"
                        data-choice-image="{{ $club->logo_path ? asset('storage/' . $club->logo_path) : '' }}"
                        aria-pressed="{{ $selectedClub === $club->name ? 'true' : 'false' }}"
                    >
                        @if ($club->logo_path)
                            <img src="{{ asset('storage/' . $club->logo_path) }}" alt="" loading="lazy">
                        @else
                            <span class="choice-option-placeholder">Nav logo</span>
                        @endif
                        <span class="choice-option-label">{{ $club->name }}</span>
                    </button>
                @endforeach
            </div>

            <p id="club-search-empty" hidden>Klubi nav atrasti.</p>
        </dialog>

        <dialog id="category-picker-dialog" class="profile-dialog" aria-labelledby="category-picker-title">
            <div class="profile-dialog-header">
                <h2 id="category-picker-title">Izvēlies motocikla kategoriju</h2>
                <form method="dialog">
                    <button type="submit">Aizvērt</button>
                </form>
            </div>

            <label class="profile-field" for="category-search">Meklēt kategoriju</label>
            <input id="category-search" class="choice-search" type="search" placeholder="Ieraksti kategoriju">

            <div class="choice-grid">
                <button class="choice-option" type="button" data-choice-option data-choice-value="" data-choice-name="Nav izvēlēts" data-choice-image="" aria-pressed="{{ $selectedCategory ? 'false' : 'true' }}">
                    <span class="choice-option-placeholder">Nav attēla</span>
                    <span class="choice-option-label">Nav izvēlēts</span>
                </button>

                @foreach ($categories as $category)
                    <button
                        class="choice-option"
                        type="button"
                        data-choice-option
                        data-choice-value="{{ $category['name'] }}"
                        data-choice-name="{{ $category['name'] }}"
                        data-choice-image="{{ $category['image_url'] ?? '' }}"
                        aria-pressed="{{ $selectedCategory === $category['name'] ? 'true' : 'false' }}"
                    >
                        @if ($category['image_url'])
                            <img src="{{ $category['image_url'] }}" alt="{{ $category['name'] }} motocikls" loading="lazy">
                        @else
                            <span class="choice-option-placeholder">Attēls tiks pievienots</span>
                        @endif
                        <span class="choice-option-label">{{ $category['name'] }}</span>
                    </button>
                @endforeach
            </div>

            <p id="category-search-empty" hidden>Kategorijas nav atrastas.</p>
        </dialog>
    </main>
</x-layout>