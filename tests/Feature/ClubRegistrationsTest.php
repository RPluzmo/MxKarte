<?php

use App\Models\Club;
use App\Models\Rider;
use App\Models\Track;
use App\Models\User;

it('pārbauda sava kluba biedru pieteikumu trašu izcelšanu kartē', function () {
    $club = Club::create(['name' => 'MX Testa klubs']);
    $otherClub = Club::create(['name' => 'Cits klubs']);
    $member = User::factory()->create(['club_id' => $club->id]);

    $clubTrack = Track::create([
        'name' => 'Kluba trase',
        'slug' => 'kluba-trase',
        'lat' => 56.94600,
        'lng' => 24.10500,
    ]);
    $otherTrack = Track::create([
        'name' => 'Cita kluba trase',
        'slug' => 'cita-kluba-trase',
        'lat' => 57.00000,
        'lng' => 24.20000,
    ]);

    // Kartes izcēlums balstās uz pieteikumiem ar attiecīgā kluba ID.
    Rider::create([
        'track_id' => $clubTrack->id,
        'club_id' => $club->id,
        'name' => 'Kluba',
        'surname' => 'Biedrs',
        'category' => 'MX 85',
        'experience_level' => 'Amatieris',
        'ride_date' => now()->addDay()->toDateString(),
        'ride_time' => '10:00',
    ]);
    Rider::create([
        'track_id' => $otherTrack->id,
        'club_id' => $otherClub->id,
        'name' => 'Cits',
        'surname' => 'Biedrs',
        'category' => 'MX 65',
        'experience_level' => 'Iesācējs',
        'ride_date' => now()->addDay()->toDateString(),
        'ride_time' => '11:00',
    ]);

    // Sagaidāmais rezultāts: kartē izceļ tikai trasi ar sava kluba pieteikumu.
    $this->actingAs($member)
        ->get(route('home'))
        ->assertOk()
        ->assertViewHas('clubName', 'MX Testa klubs')
        ->assertViewHas('mapData', function (array $mapData) use ($clubTrack, $otherTrack): bool {
            return $mapData['clubTrackIds'] === [$clubTrack->id]
                && ! in_array($otherTrack->id, $mapData['clubTrackIds'], true);
        })
        ->assertSee('Izcelt MX Testa klubs pieteikumus');

    // Sagaidāmais rezultāts: trases sarakstā redzams kluba biedra pieteikums.
    $this->get(route('tracks.show', $clubTrack))
        ->assertOk()
        ->assertSee('Kluba')
        ->assertSee('Biedrs');
});
