<?php

use App\Models\Track;
use Database\Seeders\DatabaseSeeder;

it('can safely run the starting-data seeder more than once', function () {
    $this->seed(DatabaseSeeder::class);

    $initialTrackCount = Track::count();
    $track = Track::where('slug', 'aizpute')->firstOrFail();
    $track->update(['name' => 'Pielāgots trases nosaukums']);

    $this->seed(DatabaseSeeder::class);

    expect(Track::count())->toBe($initialTrackCount);
    expect($track->fresh()->name)->toBe('Pielāgots trases nosaukums');
});
