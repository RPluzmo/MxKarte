<h1>Kvalifikācijas darbs - MxKarte</h1>

<p>Šis ir Laravel projekts, kurā var apskatīt Latvijas mototrases interaktīvā OpenStreetMap kartē, pieteikties treniņiem, apskatīt citu braucēju pieteikumus, izcelt sava kluba biedrus, lasīt trašu saimnieku paziņojumus un publicēt komentārus. Projektā ir arī trašu pārvaldība un administratora sadaļa.</p>

<h2>Kā es plānoju šo projektu palaist lokāli</h2>

<p>Es izmantoju Laragon, Cmder termināli un parasti MySQL datubāzi. <b>.env.example</b> pašlaik ir sagatavots MySQL. Laravel konfigurācija atbalsta arī SQLite, ja <b>.env</b> failā norādu <i>DB_CONNECTION=sqlite</i> un <i>DB_DATABASE=database/database.sqlite</i> un izveidoju tukšu šo datubāzes failu.</p>

<p>Projektam vajadzīgs PHP 8.3 vai jaunāks un Composer. CSS un JavaScript resursiem vajadzīgs Node.js 20.19 vai jaunāks 20.x laidienā, vai Node.js 22.12 vai jaunāks, kā arī npm. Šīs versijas atbilst projektā izmantotajam Vite 8.</p><br>

<p>Sākumā klonēju projektu Laragon <b>www</b> mapē:</p>
<pre><code>git clone &lt;repository-url&gt; MxKarte
cd MxKarte</code></pre>

<p>Pēc tam uzstādu PHP atkarības:</p>
<pre><code>composer install</code></pre>
<p>Šī komanda uzstāda arī Pest, kas šajā projektā tiek izmantots testiem.</p>

<p>Uzstādu arī JavaScript atkarības un izveidoju pārlūkā izmantojamos CSS un JavaScript failus:</p>
<pre><code>npm ci
npm run build</code></pre>
<p><i>npm ci</i> izmanto projekta <b>package-lock.json</b>, lai uzstādītu tajā fiksētās atkarību versijas. Ja izstrādes laikā vēlos automātiski atjaunot resursus pēc koda izmaiņām, atsevišķā terminālī palaižu <i>npm run dev</i> un atstāju to darboties.</p>

<p>Kamēr Composer ielādē nepieciešamo, ar <i>code .</i> atveru projektu Visual Studio Code.</p><br>

<h3>Vides konfigurācija</h3>

<p>Pēc klonēšanas izveidoju savu <b>.env</b> failu no parauga:</p>
<pre><code>copy .env.example .env</code></pre>

<p>Šajā palaišanas piemērā izmantoju MySQL. Izveidoju datubāzi ar nosaukumu <b>mxkarte</b> un <b>.env</b> failā norādu:</p>
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

<p>Lai jaunā datubāzē pievienotu sākuma datus — trases, klubus, attēlus un nejauši izveidotus demonstrācijas pieteikumus — palaižu parasto seed komandu:</p>
<pre><code>php artisan db:seed</code></pre>
<p>Šī komanda pievieno sākuma datus un nedzēš esošās tabulas. Tā neveido administratora vai trases saimnieku kontus, nepiešķir lietotājiem trases un neveido trases paziņojumus. Sēklotāji nav paredzēti atkārtotai palaišanai: trases jau var būt datubāzē, bet demonstrācijas pieteikumi tiek ģenerēti nejauši. Tāpēc komandu palaižu vienu reizi jaunā, tukšā datubāzē. Ikdienas lokālai palaišanai <i>php artisan migrate:fresh --seed</i> nav vajadzīga, jo <i>migrate:fresh</i> dzēš tabulas un to datus.</p>

<p>Pēc migrācijām un sākuma datu ielādes droši izveidoju administratoru Laragon terminālī:</p>
<pre><code>php artisan mxkarte:admin:setup</code></pre>
<p>Komanda terminālī pieprasa administratora e-pastu, vārdu, uzvārdu un jaunu paroli. Parole netiek glabāta projekta kodā vai sēklotājos, un tai jāatbilst kopīgajām paroles prasībām. Drošības migrācija nomaina kontiem ar veco zināmo paroli <i>1</i> paroli uz nejaušu, lietotājam nezināmu vērtību, noņem to saimnieka lomu un trases piesaisti un izbeidz šo kontu sesijas. Tādēļ pēc migrācijas pirms pirmās administratora pieteikšanās jāpalaiž iepriekš minētā komanda.</p>

<p>Trases saimnieks vispirms izveido savu lietotāja kontu, tad iesniedz pieteikumu konkrētai brīvai trasei sadaļā <i>Saimnieka pieteikumi</i>. Administrators pārbauda lietotāja saistību ar trasi, piemēram, sazinoties ar trases pārstāvi, un tikai pēc tam apstiprina pieteikumu. Pēc apstiprināšanas lietotājs iegūst saimnieka lomu un norādītās trases piekļuvi. Lomas noņemšanas gadījumā trases tiek atsaistītas no lietotāja.</p><br>

<p>Lai pārlūkā būtu redzami attēli, izveidoju saiti:</p>
<pre><code>php artisan storage:link</code></pre><br>

<h3>Palaišana pārlūkā</h3>

<p>Pēc <i>npm run build</i> terminālī palaižu Laravel serveri:</p>
<pre><code>php artisan serve</code></pre>

<p>Pēc noklusējuma projektu atveru adresē <a href="http://127.0.0.1:8000/">http://127.0.0.1:8000/</a>.</p>

<h3>Publiskās vides sagatavošana</h3>
<p>Publiskā vidē <b>.env</b> failā iestatu <i>APP_ENV=production</i>, <i>APP_DEBUG=false</i>, īsto HTTPS <i>APP_URL</i>, unikālu slepenu <i>APP_KEY</i> un drošus datubāzes piekļuves datus. Pirms nodošanas palaižu migrācijas ar <i>php artisan migrate --force</i>, izveidoju administratoru no servera termināļa ar <i>php artisan mxkarte:admin:setup</i> un sagatavoju resursus ar <i>npm ci</i> un <i>npm run build</i>. Pārliecinos arī, ka servera <i>storage</i> un <i>bootstrap/cache</i> mapēs Laravel var rakstīt. Publiskā vidē parasto <i>php artisan db:seed</i> nepalaižu, jo tas paredzēts sākuma un demonstrācijas datiem.</p>

<h2>Pest testi</h2>

<p>Visus projekta testus palaižu no projekta saknes mapes:</p>
<pre><code>vendor\bin\pest.bat</code></pre>

<p>Atsevišķu funkcionālo testu var palaist, norādot tā datni. Piemēram:</p>
<pre><code>vendor\bin\pest.bat tests\Feature\RiderRegistrationTest.php</code></pre>
<p>Testu vide ir atdalīta no vietnes ikdienas datubāzes: <b>phpunit.xml</b> testiem norāda SQLite datubāzi atmiņā, un Pest pirms katra funkcionālā testa atjauno testa datubāzi.</p>

<p>Piecas dokumentācijā aplūkotās funkcionālo testu datnes pārbauda šīs funkcijas:</p>
<ul>
    <li><code>tests\Feature\RiderRegistrationTest.php</code> — braucēja pieteikšana un attēlošana;</li>
    <li><code>tests\Feature\MapTrackMarkersTest.php</code> — trases un pieteikumu skaits kartes datos;</li>
    <li><code>tests\Feature\TrackAnnouncementTest.php</code> — trases saimnieka paziņojuma publicēšana;</li>
    <li><code>tests\Feature\ClubRegistrationsTest.php</code> — kluba biedru pieteikumi un trašu izcelšana;</li>
    <li><code>tests\Feature\TrackCommentsTest.php</code> — komentāra publicēšana un attēlošana.</li>
</ul>
<p>Papildus <code>tests\Feature\TrackImagesTest.php</code> pārbauda attēlu saglabāšanu, galerijas limitu, aizstāšanu un kļūmju gadījumus. <code>tests\Feature\ExampleTest.php</code> ir Laravel sākotnējais pamatdarbības tests.</p>
