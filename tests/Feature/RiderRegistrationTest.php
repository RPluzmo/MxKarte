<?php

use App\Models\Track;

it('pārbauda braucēja pieteikuma saglabāšanu un attēlošanu trases sarakstā', function () {
    $track = Track::create([
        'name' => 'Pieteikuma testa trase',
        'slug' => 'pieteikuma-testa-trase',
        'lat' => 56.94600,
        'lng' => 24.10500,
    ]);
    $rideDate = now()->addDay()->toDateString();

    $response = $this->post(route('riders.store', $track), [
        'name' => 'Anna',
        'surname' => 'Bērziņa',
        'category' => 'MX 85',
        'experience_level' => 'Amatieris',
        'ride_date' => $rideDate,
        'ride_time' => '10:30',
    ]);

    // Sagaidāmais rezultāts: pēc iesniegšanas lietotājs atgriežas trases lapā.
    $response->assertRedirect(route('tracks.show', $track));

    // Sagaidāmais rezultāts: pieteikums ir saglabāts datubāzē.
    $this->assertDatabaseHas('riders', [
        'track_id' => $track->id,
        'name' => 'Anna',
        'surname' => 'Bērziņa',
        'ride_date' => $rideDate.' 00:00:00',
        'ride_time' => '10:30',
    ]);

    // Sagaidāmais rezultāts: lapā redzams braucējs un viens pieteikums.
    $this->get(route('tracks.show', $track))
        ->assertOk()
        ->assertSee('Anna')
        ->assertSee('Bērziņa')
        ->assertSee('Pieteikumi: 1');
});
