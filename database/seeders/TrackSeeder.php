<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Track;

class TrackSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tracks = [
            [
                'name' => 'Aizpute',
                'lat' => 56.71152,
                'lng' => 21.60604,
                'description' => 'Misiņkalna mototrase',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Aloja',
                'lat' => 57.80586,
                'lng' => 24.90342,
                'description' => 'Alojas mototrase',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Ape',
                'lat' => 57.54569,
                'lng' => 26.71477,
                'description' => 'Apes mototrase',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Ādaži',
                'lat' => 57.10137,
                'lng' => 24.32102,
                'description' => 'Ādažu mototrase',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Ādažu poligons',
                'lat' => 57.11500,
                'lng' => 24.36163,
                'description' => 'Ādažu poligona mototrase',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Burtnieki',
                'lat' => 57.68364,
                'lng' => 25.28872,
                'description' => 'Burtnieku mototrase',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Cēsis',
                'lat' => 57.33386,
                'lng' => 25.30718,
                'description' => 'Ezerkalnu mototrase',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Daugavpils',
                'lat' => 55.85037,
                'lng' => 26.53385,
                'description' => 'Daugavpils mototrase',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Dobele',
                'lat' => 56.60626,
                'lng' => 23.21040,
                'description' => 'Dobeles mototrase',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Elkšņi',
                'lat' => 56.82148,
                'lng' => 24.53354,
                'description' => 'Elkšņu mototrase',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Gulbene',
                'lat' => 57.12819,
                'lng' => 26.70070,
                'description' => 'Gulbenes mototrase',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Jaunmārupe',
                'lat' => 56.892263,
                'lng' => 23.916807,
                'description' => 'Moto trase Vilciņi',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Jaunpils',
                'lat' => 56.70768,
                'lng' => 23.01214,
                'description' => 'Jaunpils mototrase',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Jurkalne',
                'lat' => 57.01789,
                'lng' => 21.38956,
                'description' => 'Jurkalnes mototrase',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Ķegums',
                'lat' => 56.75074,
                'lng' => 24.74242,
                'description' => 'Ķeguma mototrase',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Liepāja',
                'lat' => 56.540292,
                'lng' => 21.020740,
                'description' => 'Motoparks Lauma',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Limbaži',
                'lat' => 57.50135,
                'lng' => 24.69640,
                'description' => 'Limbažu mototrase',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Lubāna',
                'lat' => 56.889644,
                'lng' => 26.702442,
                'description' => 'Trases braucamās daļas platums: 5-7m. Starta taisnes garums: 80m.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Madona',
                'lat' => 56.82853,
                'lng' => 26.17856,
                'description' => 'Madonas mototrase',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Nereta',
                'lat' => 56.22099,
                'lng' => 25.29731,
                'description' => 'Neretas mototrase',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Pilsblīdene',
                'lat' => 56.69830,
                'lng' => 22.67847,
                'description' => 'Trases platums 5-8m.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Praviņas',
                'lat' => 56.90718,
                'lng' => 23.17927,
                'description' => 'Praviņu mototrase',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Rauna',
                'lat' => 57.33700,
                'lng' => 25.56580,
                'description' => 'Raunas mototrase',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Rūjiena',
                'lat' => 57.90345,
                'lng' => 25.36069,
                'description' => 'Mototrasē tiek rīkotas B un C kategorijas motokrosa sacensības.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Saldus',
                'lat' => 56.650093,
                'lng' => 22.433127,
                'description' => 'Silavotiņu mototrase',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Sigulda',
                'lat' => 57.151018,
                'lng' => 24.937688,
                'description' => 'Ozolkalna mototrase',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Staicele',
                'lat' => 57.90247,
                'lng' => 24.85461,
                'description' => 'Līciema mototrase',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Stāmeriena',
                'lat' => 57.249230,
                'lng' => 26.878132,
                'description' => 'Motorparks Dimanti',
                'created_at' => now(),
                'updated_at' => now(),  
            ],
            [
                'name' => 'Stelpe',
                'lat' => 56.53381,
                'lng' => 24.51742,
                'description' => 'Stelpes mototrase',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Stende',
                'lat' => 57.13237,
                'lng' => 22.54143,
                'description' => 'Mototrase STENDE atrodas pilsētas teritorijā. Kopgarums 1500 m.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Vāveres',
                'lat' => 56.92128,
                'lng' => 24.67285,
                'description' => 'Vāveres mototrase',
                'created_at' => now(),
                'updated_at' => now(),
            ]
    ];

        Track::insert($tracks);
    }
}
