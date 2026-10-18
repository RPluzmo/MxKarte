<h1>Kvalifikācijas darbs - MxKarte</h1>

<p>Šis ir Laravel projekts, kurā var apskatīt Latvijas mototrases interaktīvā OpenStreetMap kartē, pieteikties treniņiem, apskatīt citu braucēju pieteikumus, sekot līdzi kluba biedriem un sazināties ar trašu saimniekiem. Projektā ir arī komentāri un administratora sadaļa.</p>

<h2>Kā es plānoju šo projektu palaist lokāli</h2>

<p>Es izmantoju Laragon, Cmder termināli un parasti MySQL datubāzi. Projektu var palaist arī ar SQLite, kas ir norādīta <b>.env.example</b> failā.</p>

<p>Projektam vajadzīgs PHP 8.3 vai jaunāks, Composer, kā arī Node.js un npm, lai ielādētu un palaistu projekta CSS un JavaScript resursus.</p><br>

<p>Sākumā klonēju projektu Laragon <b>www</b> mapē:</p>
<pre><code>git clone &lt;repository-url&gt; MxKarte
cd MxKarte</code></pre>

<p>Pēc tam uzstādu PHP atkarības:</p>
<pre><code>composer install</code></pre>
<p>Šī komanda uzstāda arī Pest, kas šajā projektā tiek izmantots testiem.</p>

<p>Kamēr Composer ielādē nepieciešamo, ar <i>code .</i> atveru projektu Visual Studio Code.</p><br>

<h3>Vides konfigurācija</h3>

<p>Pēc klonēšanas izveidoju savu <b>.env</b> failu no parauga:</p>
<pre><code>copy .env.example .env</code></pre>

<p>Noklusētā datubāze ir MySQL kurai izveidoju datubāzi ar nosaukumu <b>mxkarte</b> un <b>.env</b> failā norādu:</p>
<pre><code>DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mxkarte
DB_USERNAME=root
DB_PASSWORD=</code></pre>

<p>Pēc tam izveidoju <i>Laravel</i> lietotnes atslēgu:</p>
<pre><code>php artisan key:generate</code></pre><br>

<h3>Migrācijas un sākuma dati</h3>

<p>Izveidoju datubāzes tabulas ar migrācijām:</p>
<pre><code>php artisan migrate</code></pre>
<p>Parādoties apstiprinājuma lidziņam spiežu Y</p>

<p>Lai jaunā datubāzē pievienotu projektā nepieciešamos sākuma datus — trases, klubus, lietotājus, attēlus un demonstrācijas pieteikumus - palaižu parasto seed komandu:</p>
<pre><code>php artisan db:seed</code></pre>
<p>Šī komanda pievieno sākuma datus un nedzēš esošās tabulas. Seed komandu jaunā, tukšā datubāzē palaižu vienu reizi. Ikdienas lokālai palaišanai <i>php artisan migrate:fresh --seed</i> nav vajadzīga, jo <i>migrate:fresh</i> dzēš tabulas un to datus.</p><br>

<p>Lai pārlūkā būtu redzami attēli, izveidoju saiti:</p>
<pre><code>php artisan storage:link</code></pre><br>

<h3>Palaišana pārlūkā</h3>

<p>Terminālī palaižu Laravel serveri:</p>
<pre><code>php artisan serve</code></pre>

<p>Pēc noklusējuma projektu atveru adresē <a href="http://127.0.0.1:8000/">http://127.0.0.1:8000/</a>.</p>

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