<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Track;
use Illuminate\Support\Facades\Hash;

class TrackOwnerSeeder extends Seeder
{
    public function run(): void
    {
        $trackOwners = [
            'Aizpute' => 'aizpute',
            'Aloja' => 'aloja',
            'Ape' => 'ape',
            'Ādaži' => 'adazi',
            'Ādažu poligons' => 'adazupol',
            'Burtnieki' => 'burtnieki',
            'Cēsis' => 'cēsis',
            'Daugavpils' => 'daugavpils',
            'Dobele' => 'dobele',
            'Elkšņi' => 'elkšņi',
            'Gulbene' => 'gulbene',
            'Jaunmārupe' => 'jaunmarupe',
            'Jaunpils' => 'jaunpils',
            'Jurkalne' => 'jurkalne',
            'Ķegums' => 'kegums',
            'Liepāja' => 'liepaja',
            'Limbaži' => 'limbaži',
            'Lubāna' => 'lubana',
            'Madona' => 'madona',
            'Nereta' => 'nereta',
            'Pilsblīdene' => 'pilsblidene',
            'Rauna' => 'rauna',
            'Rūjiena' => 'rujiena',
            'Saldus' => 'saldus',
            'Sigulda' => 'sigulda',
            'Staicele' => 'staicele',
            'Stāmeriena' => 'stameriena',
            'Stelpe' => 'stelpe',
            'Stende' => 'stende',
            'Vāveres' => 'vaveres',
        ];

        foreach ($trackOwners as $trackName => $emailPrefix) {
            $owner = User::updateOrCreate(
                ['email' => $emailPrefix . '@mxkarte.lv'],
                [
                    'name' => $trackName,
                    'surname' => 'Saimnieks',
                    'password' => Hash::make('1'),
                    'role' => 'owner',
                    'category' => '',
                    'experience_level' => '',
                ]
            );

            Track::where('name', $trackName)->update(['user_id' => $owner->id]);
        }
    }
}