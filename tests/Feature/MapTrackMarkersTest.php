<?php

use App\Models\Rider;
use App\Models\Track;

it('pārbauda kartē attēlotās trases un katras trases pieteikumu skaitu', function () {
    $track = Track::create([
        'name' => 'Kartes testa trase',
        'slug' => 'kartes-testa-trase',
        'lat' => 56.94600,
        'lng' => 24.10500,
    ]);

    // Izveido divus pieteikumus, kuru skaitam jāparādās kartes datos.
    foreach ([['Pirmais', 'Braucējs'], ['Otrais', 'Braucējs']] as [$name, $surname]) {
        Rider::create([
            'track_id' => $track->id,
            'name' => $name,
            'surname' => $surname,
            'category' => 'MX 85',
            'experience_level' => 'Amatieris',
            'ride_date' => now()->addDay()->toDateString(),
            'ride_time' => '10:00',
        ]);
    }

    // Sagaidāmais rezultāts: kartes datos trasei ir divi pieteikumi.
    $this->get(route('home'))
        ->assertOk()
        ->assertViewHas('mapData', function (array $mapData) use ($track): bool {
            $mapTrack = $mapData['tracks']->firstWhere('id', $track->id);

            return $mapTrack !== null && (int) $mapTrack->riders_count === 2;
        })
        ->assertSee('map-data', false)
        ->assertSee('"riders_count":2', false);
});
