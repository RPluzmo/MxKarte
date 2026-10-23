<h1>Kvalifikācijas darbs - MxKarte</h1>

<p>MxKarte ir Laravel tīmekļa vietne, kurā var apskatīt Latvijas mototrases interaktīvā kartē, pieteikties treniņiem, redzēt braucēju pieteikumus, lasīt trašu paziņojumus un publicēt komentārus. Trases saimnieki var pārvaldīt savas trases informāciju, bet administrators pārvalda lietotājus, trases un piekļuves pieteikumus.</p>

<h2>Lokāla projekta palaišana</h2>

<p>Projektu lokāli palaižu ar Laragon, Cmder termināli un MySQL datubāzi. Nepieciešams PHP 8.3 vai jaunāks, Composer, Node.js un npm.</p>

<p>Klonēju projektu Laragon <b>www</b> mapē un atveru projekta mapi:</p>
<pre><code>git clone &lt;repository-url&gt; MxKarte
cd MxKarte</code></pre>

<p>Uzstādu projekta atkarības:</p>
<pre><code>composer install
npm ci
npm run build</code></pre>

<p>Izveidoju savu <b>.env</b> failu no parauga un sagatavoju lietotnes atslēgu:</p>
<pre><code>copy .env.example .env
php artisan key:generate</code></pre>

<p>MySQL vidē izveidoju datubāzi <b>mxkarte</b> un <b>.env</b> failā norādu datubāzes savienojuma datus:</p>
<pre><code>DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mxkarte
DB_USERNAME=root
DB_PASSWORD=</code></pre>

<p>Izveidoju datubāzes tabulas un ielādēju projektā iekļautos sākuma datus:</p>
<pre><code>php artisan migrate
php artisan db:seed</code></pre>

<p><i>db:seed</i> pievieno trases, klubus un pieejamo trašu attēlu informāciju. Atkārtota komandas palaišana nedublē trases un klubus, kā arī neizdzēš datubāzes tabulas. Tā neveido lietotāju kontus. Ikdienas palaišanai <i>migrate:fresh --seed</i> nav vajadzīga, jo <i>migrate:fresh</i> izdzēš tabulas un to datus.</p>

<p>Lai pārlūkā būtu pieejami trases attēli, izveidoju publisko simbolisko saiti:</p>
<pre><code>php artisan storage:link</code></pre>

<p>Administratora kontu izveidoju no termināļa:</p>
<pre><code>php artisan mxkarte:admin:setup</code></pre>
<p>Komanda pieprasa e-pastu, vārdu, uzvārdu un paroli. Trases saimnieks reģistrē savu kontu un iesniedz pieteikumu trases piekļuvei; administrators to pārbauda un apstiprina.</p>

<p>Palaidu Laravel serveri:</p>
<pre><code>php artisan serve</code></pre>
<p>Vietne būs pieejama adresē <a href="http://127.0.0.1:8000/">http://127.0.0.1:8000/</a>.</p>

<h3>Plānotie apkopes uzdevumi</h3>
<p>Projektā plānotājs reizi dienā tīra vecos treniņu pieteikumus un reizi stundā mēģina vēlreiz izdzēst attēlu failus, kuru dzēšana iepriekš neizdevās. Lai šie uzdevumi darbotos lokāli, atveru vēl vienu termināli projekta mapē un palaižu:</p>
<pre><code>php artisan schedule:work</code></pre>

<h2>Pest testi</h2>

<p>Visus testus palaižu no projekta saknes mapes:</p>
<pre><code>vendor\bin\pest.bat</code></pre>

<p>Atsevišķu testa datni var palaist, norādot tās ceļu:</p>
<pre><code>vendor\bin\pest.bat tests\Feature\RiderRegistrationTest.php</code></pre>

<p>Piecas dokumentācijā aplūkotās funkcionālo testu datnes:</p>
<ul>
    <li><code>RiderRegistrationTest.php</code> — pieteikšanās treniņam un pieteikuma attēlošana;</li>
    <li><code>MapTrackMarkersTest.php</code> — trašu marķieri un pieteikumu skaits kartes datos;</li>
    <li><code>TrackAnnouncementTest.php</code> — trases paziņojuma publicēšana;</li>
    <li><code>ClubRegistrationsTest.php</code> — kluba biedru pieteikumi un trašu izcelšana;</li>
    <li><code>TrackCommentsTest.php</code> — komentāra publicēšana un attēlošana.</li>
</ul>

<p>Papildu testi pārbauda piekļuves drošību, attēlu pārvaldību, sākuma datu atkārtotu ielādi un plānotās tīrīšanas kļūmju apstrādi. Testiem tiek izmantota atsevišķa SQLite datubāze atmiņā, tāpēc tie nemaina vietnes ikdienas datubāzi.</p>
