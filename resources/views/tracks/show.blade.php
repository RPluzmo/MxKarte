<x-layout>
    <a href="/">Atpakaļ</a>
    
    <h1>{{ $track->name }}</h1>
    
    <div>
        <p><strong>Apraksts:</strong> {{ $track->description }}</p>

        <p><strong>Segums:</strong> {{ $track->surface_type ?? 'bruh ganjau smiltis vai dubļi lmao' }}</p>
    </div>

        <div>
            <h3>Pieteikties treniņam</h3>
            <form method="POST">
                @csrf
                <div>
                    <label>Jūsu vārds</label><br>
                    <input type="text" name="name" required>
                </div>

                <div>
                    <label>Jūsu uzvārds</label><br>
                    <input type="text" name="name" required>
                </div>
                
                <div>
                    <label>Klubs (neobligāti)</label><br>
                    <input type="text" name="club">
                </div>

                <div>
                    <label>Klase</label><br>
                    <select name="category" required>
                        <option value="MX 50">MX 50</option>
                        <option value="MX 65">MX 65</option>
                        <option value="MX 85">MX 85</option>
                        <option value="MX 125">MX 125</option>
                        <option value="MX 250">MX 250</option>
                        <option value="MX 450">MX 450</option>
                        <option value="Kvadri">Kvadri</option>
                        <option value="Blakusvāģi">Blakusvāģi</option>
                    </select>
                </div>

                <div>
                    <label>Pieredze</label><br>
                    <select name="experience_level" required>
                        <option value="Iesācējs">Iesācējs</option>
                        <option value="Amatieris">Amatieris</option>
                        <option value="Veterāns">Veterāns</option>
                        <option value="Profesionālis">Profesionālis</option>
                    </select>
                </div>

                <button type="submit" >Pieteikties</button>
            </form>
        </div>
    </div>


</x-layout>