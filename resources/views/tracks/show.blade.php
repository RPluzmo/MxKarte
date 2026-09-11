<x-layout>
    <a href="/">Atpakaļ</a>
    
    <h1>{{ $track->name }}</h1>

    
    <div>
        <p><strong>Apraksts:</strong> {{ $track->description }}</p>

        <p><strong>Segums:</strong> {{ $track->surface_type ?? 'bruh ganjau smiltis vai dubļi lmao' }}</p>
    </div>

        <div>
            <h3>Pieteikties treniņam</h3>
            <form method="POST" action="{{ route('riders.store', $track) }}">
                @csrf
                @php
                    $authUser = auth()->user();
                @endphp
                <div>
                    <label>Vārds</label><br>
                    <input type="text" name="name" value="{{ old('name', $authUser?->name ?? '') }}" required>
                </div>

                <div>
                    <label>Uzvārds</label><br>
                    <input type="text" name="surname" value="{{ old('surname', $authUser?->surname ?? '') }}" required>
                </div>
                
                <div>
                    <label>Klubs (neobligāti)</label><br>
                    <input type="text" name="club" value="{{ old('club', $authUser?->club ?? '') }}">
                </div>

                <div>
                    <label>Klase</label><br>
                    <select name="category" required>
                        <option value="">Izvēlieties</option>
                        <option value="MX 50" {{ old('category', $authUser?->category) === 'MX 50' ? 'selected' : '' }}>MX 50</option>
                        <option value="MX 65" {{ old('category', $authUser?->category) === 'MX 65' ? 'selected' : '' }}>MX 65</option>
                        <option value="MX 85" {{ old('category', $authUser?->category) === 'MX 85' ? 'selected' : '' }}>MX 85</option>
                        <option value="MX 125" {{ old('category', $authUser?->category) === 'MX 125' ? 'selected' : '' }}>MX 125</option>
                        <option value="MX 250" {{ old('category', $authUser?->category) === 'MX 250' ? 'selected' : '' }}>MX 250</option>
                        <option value="MX 450" {{ old('category', $authUser?->category) === 'MX 450' ? 'selected' : '' }}>MX 450</option>
                        <option value="Kvadri" {{ old('category', $authUser?->category) === 'Kvadri' ? 'selected' : '' }}>Kvadri</option>
                        <option value="Blakusvāģi" {{ old('category', $authUser?->category) === 'Blakusvāģi' ? 'selected' : '' }}>Blakusvāģi</option>
                    </select>
                </div>

                <div>
                    <label>Pieredze</label><br>
                    <select name="experience_level" required>
                        <option value="">Izvēlieties</option>
                        <option value="Iesācējs" {{ old('experience_level', $authUser?->experience_level) === 'Iesācējs' ? 'selected' : '' }}>Iesācējs</option>
                        <option value="Amatieris" {{ old('experience_level', $authUser?->experience_level) === 'Amatieris' ? 'selected' : '' }}>Amatieris</option>
                        <option value="Veterāns" {{ old('experience_level', $authUser?->experience_level) === 'Veterāns' ? 'selected' : '' }}>Veterāns</option>
                        <option value="Profesionālis" {{ old('experience_level', $authUser?->experience_level) === 'Profesionālis' ? 'selected' : '' }}>Profesionālis</option>
                    </select>
                </div>

                <div>
                    <label>Ierašanās laiks</label><br>
                    <input type="time" name="ride_time" value="{{ old('ride_time') }}" required>
                </div>

                <button type="submit" >Pieteikties</button>
            </form>
        </div>

        <div>
            <h3>Pieteikušies sportisti</h3>

            @forelse($track->riders as $rider)
                <div>
                    <strong>{{ $rider->name }} {{ $rider->surname }}</strong>
                    <span>{{ $rider->category }}, {{ $rider->experience_level }}</span>
                    <span>{{ $rider->ride_time }}</span>
                    @if($rider->club)
                        <span>{{ $rider->club }}</span>
                    @endif
                </div>
            @empty
                <p>Neveins neplāno ierasties.</p>
            @endforelse
        </div>
    </div>


</x-layout>
