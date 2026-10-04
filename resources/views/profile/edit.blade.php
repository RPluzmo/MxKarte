<x-layout>
    <x-slot:title>Profils</x-slot:title>

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

                <x-choice-picker
                    key="club"
                    name="club"
                    label="Kluba piederība"
                    title="Izvēlies motoklubu"
                    search="Meklēt klubu"
                    button="Atzīmēt motokluba piederību"
                    empty="Klubi nav atrasti."
                    none="Privāti"
                    none-hint="Nav kluba logo"
                    :options="$clubs"
                    :selected="old('club', $user->club)"
                />

                <x-choice-picker
                    key="category"
                    name="category"
                    label="Motocikla kategorija"
                    title="Izvēlies motocikla kategoriju"
                    search="Meklēt kategoriju"
                    button="Izvēlēties kategoriju"
                    empty="Kategorijas nav atrastas."
                    none="Nav izvēlēts"
                    :show-image="false"
                    :options="$categories"
                    :selected="old('category', $user->category)"
                />

                <x-choice-picker
                    key="experience"
                    name="experience_level"
                    label="Pieredze"
                    title="Izvēlies pieredzes līmeni"
                    search="Meklēt pieredzi"
                    button="Izvēlēties pieredzi"
                    empty="Pieredzes līmeņi nav atrasti."
                    none="Nav izvēlēts"
                    :show-image="false"
                    :options="$experienceLevels"
                    :selected="old('experience_level', $user->experience_level)"
                />

                <label class="profile-field">Jauna parole
                    <input type="password" name="password">
                </label>

                <label class="profile-field">Jauna parole atkārtoti
                    <input type="password" name="password_confirmation">
                </label>

                <button type="submit">Saglabāt</button>
            </form>
        </section>
    </main>
</x-layout>
