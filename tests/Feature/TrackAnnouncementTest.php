<?php

use App\Models\Track;
use App\Models\User;

it('pārbauda trases saimnieka paziņojuma publicēšanu un attēlošanu', function () {
    $owner = User::factory()->create(['role' => 'owner']);
    $track = Track::create([
        'user_id' => $owner->id,
        'name' => 'Paziņojuma testa trase',
        'slug' => 'pazinojuma-testa-trase',
        'lat' => 56.94600,
        'lng' => 24.10500,
    ]);

    // Sagaidāmais rezultāts: trases saimnieka paziņojums tiek pieņemts.
    $this->actingAs($owner)
        ->post(route('announcements.store', $track), [
            'title' => 'Treniņš pārcelts',
            'body' => 'Treniņš sāksies plkst. 11.00.',
            'is_pinned' => true,
        ])
        ->assertRedirect();

    // Sagaidāmais rezultāts: paziņojums ir saglabāts pie pareizās trases.
    $this->assertDatabaseHas('track_announcements', [
        'track_id' => $track->id,
        'user_id' => $owner->id,
        'title' => 'Treniņš pārcelts',
        'is_pinned' => true,
    ]);

    $this->get(route('tracks.show', $track))
        ->assertOk()
        ->assertSee('Treniņš pārcelts')
        ->assertSee('Treniņš sāksies plkst. 11.00.');
});
