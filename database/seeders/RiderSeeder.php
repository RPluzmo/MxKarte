<?php

namespace Database\Seeders;

use App\Models\Club;
use App\Models\Track;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class RiderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'MX 50', 'MX 65', 'MX 85', 'MX 125',
            'MX 250', 'MX 450', 'Kvadri', 'Blakusvāģi',
        ];
        $experienceLevels = ['Iesācējs', 'Amatieris', 'Veterāns', 'Profesionālis'];
        $clubs = Club::orderBy('name')->pluck('name')->all();
        $faker = Faker::create('lv_LV');

        foreach (Track::orderBy('slug')->get() as $track) {
            $riderCount = random_int(0, 12);
            $trackPeople = [];

            while (count($trackPeople) < $riderCount) {
                $name = $faker->firstName();
                $surname = $faker->lastName();
                $trackPeople[$name . '|' . $surname] = [$name, $surname];
            }

            foreach ($trackPeople as [$name, $surname]) {
                $arrivalHour = random_int(6, 23);
                $club = $clubs !== [] && random_int(1, 100) <= 70
                    ? $faker->randomElement($clubs)
                    : 'Privāti';

                $track->riders()->firstOrCreate(
                    [
                        'name' => $name,
                        'surname' => $surname,
                    ],
                    [
                        'user_id' => null,
                        'club' => $club,
                        'category' => $faker->randomElement($categories),
                        'experience_level' => $faker->randomElement($experienceLevels),
                        'ride_time' => sprintf('%02d:00', $arrivalHour),
                    ]
                );
            }
        }
    }
}
