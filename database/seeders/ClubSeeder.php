<?php

namespace Database\Seeders;

use App\Models\Club;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClubSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         $clubs = [
            ['name' => 'MX Ādaži', 'logo_path' => 'clubs/mxadazi.png'],
            ['name' => 'AG XTREME', 'logo_path' => 'clubs/agxtreme.png'],
            ['name' => 'Tukuma motoklubs', 'logo_path' => 'clubs/tukumamotoklubs.png'],
            ['name' => 'Adventure Team Latvia', 'logo_path' => 'clubs/atl.png'],
            ['name' => 'MT Racing team', 'logo_path' => 'clubs/mtracingteam.png'],
            ['name' => 'Bruno Racing Team', 'logo_path' => 'clubs/brunoracingteam.png'],
            ['name' => 'FIM Motocamp Latvia', 'logo_path' => 'clubs/fimmotocamp.png'],
            ['name' => 'Jāņa Vintera Moto Team Riga', 'logo_path' => 'clubs/jvmototeam.png'],
            ['name' => 'MX Delveri', 'logo_path' => 'clubs/mxdelveri.png'],
            ['name' => 'Karro MX', 'logo_path' => 'clubs/karromx.png'],
            ['name' => 'MK Livonija', 'logo_path' => 'clubs/mklivonija.png'],
            ['name' => 'LatvianBaja', 'logo_path' => 'clubs/latvianbaja.png'],
            ['name' => 'MK Aloja', 'logo_path' => 'clubs/mkaloja.png'],
            ['name' => 'MK ASB', 'logo_path' => 'clubs/mkasb.png'],
            ['name' => 'AKA Team', 'logo_path' => 'clubs/akateam.png'],
            ['name' => 'Moto Mafia', 'logo_path' => 'clubs/motomafia.png'],
            ['name' => 'apPasaule', 'logo_path' => 'clubs/appasaule.png'],
            ['name' => 'Motoklubs Ape', 'logo_path' => 'clubs/mkape.png'],
            ['name' => 'Kalsnava MB', 'logo_path' => 'clubs/kalsnavamb.png'],
            ['name' => 'Motoklubs Litene', 'logo_path' => 'clubs/mklitene.png'],
            ['name' => 'MK Stende', 'logo_path' => 'clubs/mkstende.png'],
            ['name' => 'Motorparks Dimanti', 'logo_path' => 'clubs/motoparksdimanti.png'],
            ['name' => 'motoSALA', 'logo_path' => 'clubs/motosala.png'],
            ['name' => 'RaceON', 'logo_path' => 'clubs/raceon.png'],
            ['name' => 'Riga Speedway Club', 'logo_path' => 'clubs/rigaspeedway.png'],
            ['name' => 'Saldus motoklubs', 'logo_path' => 'clubs/saldusmk.png'],
            ['name' => 'True MX', 'logo_path' => 'clubs/truemx.png'],
            ['name' => 'VV Moto Racing Team', 'logo_path' => 'clubs/vvmotoracing.png'],
            ['name' => 'Motosport racing club', 'logo_path' => 'clubs/motosportracing.png'],
            ['name' => 'Latvijas Motoklubu Asociācija', 'logo_path' => 'clubs/lma.png'],
            ['name' => 'Motovidzeme', 'logo_path' => 'clubs/motovidzeme.png'],
            ['name' => 'Rambas R', 'logo_path' => 'clubs/rambasr.png'],
            ['name' => 'Moto AZ', 'logo_path' => 'clubs/motoaz.png'],
            ['name' => 'Valkas Vanagi', 'logo_path' => 'clubs/valkasvanagi.png'],
            ['name' => 'Reišulis MX', 'logo_path' => 'clubs/reisulismx.png'],
            ['name' => 'Speedway Grand Prix of Latvia', 'logo_path' => 'clubs/speedwaygpoflatvia.png'],
            ['name' => 'Elkšņi Sporta klubs', 'logo_path' => 'clubs/elksnisportaklubs.png'],
            ['name' => 'F.F.F. Sporta klubs', 'logo_path' => 'clubs/fff.png'],
            ['name' => 'RAMO Motorsport', 'logo_path' => 'clubs/ramomotosport.png'],
            ['name' => 'SFRT Motorsports', 'logo_path' => 'clubs/sfrtmotosports.png'],
            ['name' => 'Pālādzis TSK', 'logo_path' => 'clubs/piladzistsk.png'],
            ['name' => 'Rodeo MX', 'logo_path' => 'clubs/rodeomx.png'],
            ['name' => 'Viking Trial', 'logo_path' => 'clubs/vikingtrial.png'],
            ['name' => 'Xskill Racing Team', 'logo_path' => 'clubs/xskillracing.png'],
            ['name' => 'ATK Sherco Latvija', 'logo_path' => 'clubs/atksherco.png'],
            ['name' => 'Gulbenes Moto', 'logo_path' => 'clubs/gulbenesmoto.png'],
            ['name' => 'Kristera Serģa Motoklubs', 'logo_path' => 'clubs/ksmotoklubs.png'],
            ['name' => 'Motoaplis', 'logo_path' => 'clubs/motoaplis.png'],
            ['name' => 'Motosports Racing Team', 'logo_path' => 'clubs/motosportsracingteam.png'],
            ['name' => 'MX4 Dobele', 'logo_path' => 'clubs/mx4dobele.png'],
            ['name' => 'Suzuki Latvia-VILDERS', 'logo_path' => 'clubs/suzukivilders.png'],
            ['name' => '979 Buba Racing', 'logo_path' => 'clubs/979buba.png'],
            ['name' => 'Camk Latgale', 'logo_path' => 'clubs/camklatgale.png'],
            ['name' => 'CrossMoto Racing Team', 'logo_path' => 'clubs/crossmoto.png'],
            ['name' => 'Daugavpils BJC Jaunība', 'logo_path' => 'clubs/daugavpilsbjc.png'],
            ['name' => 'Drider motocross team', 'logo_path' => 'clubs/dridermotocross.png'],
            ['name' => 'GF Racing Team', 'logo_path' => 'clubs/gfracing.png'],
            ['name' => 'JTSC "ERIŅI"', 'logo_path' => 'clubs/jtscerini.png'],
            ['name' => 'LaMSF administrÄcija', 'logo_path' => 'clubs/lamsf.png'],
            ['name' => 'Latgales Enduro', 'logo_path' => 'clubs/latgalesenduro.png'],
            ['name' => 'Mārupes AMK BieriÅ†i', 'logo_path' => 'clubs/marupesamk.png'],
            ['name' => 'ML MOTO', 'logo_path' => 'clubs/mlmoto.png'],
            ['name' => 'Moto Klubs "Austrumu zibens"', 'logo_path' => 'clubs/austrumuzibens.png'],
            ['name' => 'MX BAUSKA', 'logo_path' => 'clubs/mxbauska.png'],
            ['name' => 'MX DRUVAS', 'logo_path' => 'clubs/mxdruvas.png'],
            ['name' => 'MX Factory Latvia', 'logo_path' => 'clubs/mxfactory.png'],
            ['name' => 'MX Jaunpils', 'logo_path' => 'clubs/mxjaunpils.png'],
            ['name' => 'MX ķekava', 'logo_path' => 'clubs/mxkekava.png'],
            ['name' => 'MX SALDUS', 'logo_path' => 'clubs/mxsaldus.png'],
            ['name' => 'MK Olaine', 'logo_path' => 'clubs/mkolaine.png'],
            ['name' => 'Orange Racing', 'logo_path' => 'clubs/orangeracing.png'],
            ['name' => 'Q Team Liepāja', 'logo_path' => 'clubs/qteamliepaja.png'],
            ['name' => 'Rīgas Jauno tehniÄ·u centrs', 'logo_path' => 'clubs/rigasjaunotehniku.png'],
            ['name' => 'Rīgas bērnu un jauniešu centrs Auseklis¯', 'logo_path' => 'clubs/auseklis.png'],
            ['name' => 'Saldus ActionSports/DIRTCORE.lv', 'logo_path' => 'clubs/dirtcore.png'],
            ['name' => 'Saldus BJC', 'logo_path' => 'clubs/saldusbjc.png'],
            ['name' => 'Saules sporta klubs (SSK)', 'logo_path' => 'clubs/saulessportaklubs.png'],
            ['name' => 'SIA D4K', 'logo_path' => 'clubs/siad4k.png'],
            ['name' => 'Sigulda Racing Team', 'logo_path' => 'clubs/siguldaracingteam.png'],
            ['name' => 'MX Ozolnieki', 'logo_path' => 'clubs/mxozolnieki.png'],
            ['name' => 'DAKO Ziemeļvidzeme', 'logo_path' => 'clubs/dakoziemelvidzeme.png'],
            ['name' => 'Team Gulbene', 'logo_path' => 'clubs/teamgulbene.png'],
            ['name' => 'V.S. Riga Racing', 'logo_path' => 'clubs/vsracing.png'],
            ['name' => 'KB team', 'logo_path' => 'clubs/kbteam.png']
        ];

        foreach ($clubs as $club) {
            $logoPath = $club['logo_path'];

            if (!empty($logoPath) && !file_exists(public_path($logoPath))) {
                $logoPath = null;
            }

            Club::updateOrCreate(
                ['name' => $club['name']],
                ['logo_path' => $logoPath]
            );
        }
    }
}
