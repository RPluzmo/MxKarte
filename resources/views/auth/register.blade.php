<h1>Kvalifikācijas darbs - MxKarte</h1>

<p>Šis ir Laravel projekts, kurā var apskatīt Latvijas mototrases interaktīvā OpenStreetMap kartē, pieteikties treniņiem, apskatīt citu braucēju pieteikumus, sekot līdzi kluba biedriem un sazināties ar trašu saimniekiem. Projektā ir arī komentāri un administratora sadaļa.</p>

<h2>Kā es plānoju šo projektu palaist lokāli</h2>

<p>Es izmantoju <i>Laragon, Cmder</i> termināli un <i>MySQL</i> datubāzi.</p>


<p>Sākumā klonēju projektu no <i>GitHub</i> uz Laragon <b>www</b> pamat mapi:</p>
<pre><code>git clone <https://github.com/RPluzmo/MxKarte> MxKarte
cd MxKarte</code></pre>

<p>Pēc tam uzstādu <i>Laravel</i> nepieciešamās datnes:</p>
<pre><code>composer install</code></pre>
<p>Šī komanda uzstāda arī Pest, kas šajā projektā tiek izmantots testiem.</p>

<p>Kad ielāde pabeigta, ar <i>code .</i> atveru projektu <i>Visual Studio Code</i>.</p><br>

<h3>Vides konfigurācija</h3>

<p>Pēc klonēšanas izveidoju savu <b>.env</b> failu no parauga:</p>
<pre><code>copy .env.example .env</code></pre>

<p>Paraugā noklusētā datubāze ir SQLite. Ja vēlos izmantot Laragon MySQL, vispirms izveidoju datubāzi ar nosaukumu <b>mxkarte</b> phpMyAdmin vai HeidiSQL un <b>.env</b> failā norādu:</p>
<pre><code>DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mxkarte
DB_USERNAME=root
DB_PASSWORD=</code></pre>

<p>Ja izmantoju SQLite, šīs MySQL vērtības nav jānorāda. Pēc tam izveidoju Laravel lietotnes atslēgu:</p>
<pre><code>php artisan key:generate</code></pre><br>

<p>Vietnes nosaukumu un lokālo adresi varu norādīt arī <b>.env</b> failā:</p>
<pre><code>APP_NAME=MxKarte
APP_URL=http://127.0.0.1:8000</code></pre><br>

<h3>Migrācijas un sākuma dati</h3>

<p>Izveidoju datubāzes tabulas ar migrācijām:</p>
<pre><code>php artisan migrate</code></pre>
<p>Ja izmantoju SQLite un datubāzes fails vēl nav izveidots, Laravel var piedāvāt to izveidot. Tam piekrītu.</p>

<p>Lai jaunā datubāzē pievienotu projektā definētos sākuma datus — trases, klubus, lietotājus, attēlus un demonstrācijas pieteikumus — palaižu parasto seed komandu:</p>
<pre><code>php artisan db:seed</code></pre>
<p>Šī komanda pievieno sākuma datus un nedzēš esošās tabulas. Seed komandu jaunā, tukšā datubāzē palaižu vienu reizi. Ikdienas lokālai palaišanai <i>php artisan migrate:fresh --seed</i> nav vajadzīga, jo <i>migrate:fresh</i> dzēš tabulas un to datus.</p><br>

<p>Lai pārlūkā būtu redzami publiskajā glabātuvē esošie attēli, izveidoju simbolisko saiti:</p>
<pre><code>php artisan storage:link</code></pre><br>

<h3>Palaišana pārlūkā</h3>

<p>Vienā Cmder terminālī palaižu Vite, kas nodrošina projekta stilus un JavaScript izstrādes laikā:</p>
<pre><code>npm install
npm run dev</code></pre>
<p><i>npm run dev</i> termināli atstāju darboties. Otrā terminālī palaižu Laravel serveri:</p>
<pre><code>php artisan serve</code></pre>

<p>Pēc noklusējuma projektu atveru adresē <a href="http://127.0.0.1:8000/">http://127.0.0.1:8000/</a>.</p>
<p>Ja Vite izstrādes serveri neizmantoju, tā vietā varu vienreiz uzbūvēt statiskos resursus ar <i>npm run build</i>.</p><br>

<h2>Pest testi</h2>

<p>Visus projekta testus palaižu no projekta saknes mapes:</p>
<pre><code>vendor\bin\pest.bat</code></pre>

<p>Atsevišķu funkcionālo testu var palaist, norādot tā datni. Piemēram:</p>
<pre><code>vendor\bin\pest.bat tests\Feature\RiderRegistrationTest.php</code></pre>

<p>Piecas funkcionālo testu datnes pārbauda šīs funkcijas:</p>
<ul>
    <li><code>tests\Feature\RiderRegistrationTest.php</code> — braucēja pieteikšana un attēlošana;</li>
    <li><code>tests\Feature\MapTrackMarkersTest.php</code> — trases un pieteikumu skaits kartes datos;</li>
    <li><code>tests\Feature\TrackAnnouncementTest.php</code> — trases saimnieka paziņojuma publicēšana;</li>
    <li><code>tests\Feature\ClubRegistrationsTest.php</code> — kluba biedru pieteikumi un trašu izcelšana;</li>
    <li><code>tests\Feature\TrackCommentsTest.php</code> — komentāra publicēšana un attēlošana.</li>
</ul>

<p>Testi darbojas atsevišķā SQLite datubāzē atmiņā un nepievieno datus manai lokālajai vietnes datubāzei.</p>
