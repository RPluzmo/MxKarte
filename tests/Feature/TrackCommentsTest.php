<?php

use App\Models\Track;
use App\Models\User;

it('pārbauda komentāra publicēšanu un attēlošanu pieslēgtam lietotājam', function () {
    $track = Track::create([
        'name' => 'Komentāra testa trase',
        'slug' => 'komentara-testa-trase',
        'lat' => 56.94600,
        'lng' => 24.10500,
    ]);

    // Sagaidāmais rezultāts: viesis tiek novirzīts uz pieslēgšanās lapu.
    $this->post(route('comments.store', $track), ['body' => 'Viesa komentārs'])
        ->assertRedirect(route('login'));

    $author = User::factory()->create();

    // Sagaidāmais rezultāts: pieslēgtais lietotājs var publicēt komentāru.
    $this->actingAs($author)
        ->post(route('comments.store', $track), [
            'body' => 'Trase bija lieliskā stāvoklī!',
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('track_comments', [
        'track_id' => $track->id,
        'user_id' => $author->id,
        'body' => 'Trase bija lieliskā stāvoklī!',
    ]);

    $this->get(route('tracks.show', $track))
        ->assertOk()
        ->assertSee('Trase bija lieliskā stāvoklī!')
        ->assertSee($author->name);
});
