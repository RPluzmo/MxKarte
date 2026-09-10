<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
 public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@mxkarte.lv'],
            [
                'name' => 'Admin',
                'surname' => 'Admin',
                'password' => Hash::make('1'),
                'role' => 'admin',
                'category' => '',
                'experience_level' => '',
            ]
        );

        $owners = [
            'aizpute' => 'Aizputes',
            'aloja' => 'Alojas',
            'ape' => 'Apes',
            'adazi' => 'Ādažu',
            'adazupol' => 'Ādažupol',
            'burtnieki' => 'Burtnieku',
            'cēsis' => 'Cēsu',
            'daugavpils' => 'Daugavpils',
            'dobele' => 'Dobeles',
            'elkšņi' => 'Elkšņu',
            'gulbene' => 'Gulbenes',
            'jaunmarupe' => 'Jaunmārupes',
            'jaunpils' => 'Jaunpils',
            'jurkalne' => 'Jurkalnes',
            'kegums' => 'Ķeguma',
            'liepaja' => 'Liepājas',
            'limbaži' => 'Limbažu',
            'lubana' => 'Lubanās',
            'madona' => 'Madonas',
            'nereta' => 'Neretas',
            'pilsblidene' => 'Pilsblīdenes',
            'rauna' => 'Raunas',
            'rujiena' => 'Rūjienas',
            'saldus' => 'Saldus',
            'sigulda' => 'Siguldas',
            'staicele' => 'Staiceles',
            'stameriena' => 'Stamerienes',
            'stelpe' => 'Stelpes',
            'stende' => 'Stendes',
            'vaveres' => 'Vāveres',
        ];

        foreach ($owners as $emailPrefix => $firstName) {
            User::updateOrCreate(
                ['email' => $emailPrefix . '@mxkarte.lv'],
                [
                    'name' => $firstName,
                    'surname' => 'Saimnieks',
                    'password' => Hash::make('1'),
                    'role' => 'owner', 
                    'category' => '',
                    'experience_level' => '',
                ]
            );
        }
    }
}
