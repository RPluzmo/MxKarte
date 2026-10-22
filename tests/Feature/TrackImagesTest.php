<?php

use App\Models\Track;
use App\Models\TrackImage;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('public');
});

function createTrackForImageTest(array $attributes = []): Track
{
    return Track::create(array_merge([
        'name' => 'Attēlu testa trase',
        'slug' => 'attelu-testa-trase',
        'lat' => 56.94600,
        'lng' => 24.10500,
    ], $attributes));
}

function addTrackImageForTest(Track $track, string $type, int $sortOrder, string $name): TrackImage
{
    $path = "tracks/{$track->slug}/{$name}";
    Storage::disk('public')->put($path, 'test-image');

    return $track->images()->create([
        'type' => $type,
        'path' => $path,
        'sort_order' => $sortOrder,
    ]);
}

it('saglabā jaunas trases attēlus kopā ar trasi un izmanto trases slug mapes nosaukumam', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)
        ->post(route('admin.tracks.store'), [
            'name' => 'Jaunā trase',
            'slug' => 'jauna-trase',
            'lat' => 56.94600,
            'lng' => 24.10500,
            'cover_image' => UploadedFile::fake()->image('cover.jpg'),
            'gallery_images' => [UploadedFile::fake()->image('gallery.jpg')],
        ])
        ->assertRedirect(route('admin.tracks.index'));

    $track = Track::where('slug', 'jauna-trase')->firstOrFail();
    $this->assertDatabaseCount('track_images', 2);

    foreach ($track->images as $image) {
        expect($image->path)->toStartWith('tracks/jauna-trase/');
        Storage::disk('public')->assertExists($image->path);
    }
});

it('atceļ trases izveidi un iztīra failu, ja attēla metadatus neizdodas saglabāt', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    TrackImage::creating(function (): void {
        throw new RuntimeException('Simulēta datubāzes kļūme attēla metadatu saglabāšanā.');
    });

    try {
        $response = $this->actingAs($admin)
            ->post(route('admin.tracks.store'), [
                'name' => 'Trase ar kļūmi',
                'slug' => 'trase-ar-klumi',
                'lat' => 56.94600,
                'lng' => 24.10500,
                'cover_image' => UploadedFile::fake()->image('cover.jpg'),
            ]);
    } finally {
        TrackImage::flushEventListeners();
    }

    $response->assertServerError();
    $this->assertDatabaseMissing('tracks', ['slug' => 'trase-ar-klumi']);
    $this->assertDatabaseCount('track_images', 0);
    expect(Storage::disk('public')->allFiles('tracks/trase-ar-klumi'))->toBeEmpty();
});

it('atceļ visu galerijas atjauninājumu ja tas pārsniegtu četru attēlu limitu', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $track = createTrackForImageTest();
    $existingImages = collect(range(1, 4))
        ->map(fn (int $order) => addTrackImageForTest($track, 'gallery', $order, "gallery-{$order}.jpg"));

    $this->actingAs($admin)
        ->put(route('admin.tracks.update', $track), [
            'name' => 'Mainīts nosaukums',
            'slug' => $track->slug,
            'lat' => $track->lat,
            'lng' => $track->lng,
            'gallery_images' => [UploadedFile::fake()->image('too-many.jpg')],
        ])
        ->assertSessionHasErrors('gallery_images');

    expect($track->fresh()->name)->toBe('Attēlu testa trase');
    $this->assertDatabaseCount('track_images', 4);

    foreach ($existingImages as $image) {
        Storage::disk('public')->assertExists($image->path);
    }

    expect(Storage::disk('public')->allFiles("tracks/{$track->slug}"))->toHaveCount(4);
});

it('ļauj aizstāt pilnas galerijas attēlus vienā saglabāšanas reizē un izdzēš vecos failus', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $track = createTrackForImageTest();
    $images = collect(range(1, 4))
        ->map(fn (int $order) => addTrackImageForTest($track, 'gallery', $order, "gallery-{$order}.jpg"));
    $replacedImages = $images->take(2);

    $this->actingAs($admin)
        ->put(route('admin.tracks.update', $track), [
            'name' => $track->name,
            'slug' => $track->slug,
            'lat' => $track->lat,
            'lng' => $track->lng,
            'remove_gallery_ids' => $replacedImages->pluck('id')->all(),
            'gallery_images' => [
                UploadedFile::fake()->image('replacement-1.jpg'),
                UploadedFile::fake()->image('replacement-2.jpg'),
            ],
        ])
        ->assertRedirect(route('admin.tracks.index'));

    $this->assertDatabaseCount('track_images', 4);
    foreach ($replacedImages as $image) {
        Storage::disk('public')->assertMissing($image->path);
    }

    $remainingImages = TrackImage::where('track_id', $track->id)->get();
    foreach ($remainingImages as $image) {
        Storage::disk('public')->assertExists($image->path);
    }
});

it('prasa atzīmēt esošā titulattēla noņemšanu pirms tā aizstāšanas', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $track = createTrackForImageTest();
    $cover = addTrackImageForTest($track, 'cover', 0, 'cover.jpg');

    $this->actingAs($admin)
        ->put(route('admin.tracks.update', $track), [
            'name' => $track->name,
            'slug' => $track->slug,
            'lat' => $track->lat,
            'lng' => $track->lng,
            'cover_image' => UploadedFile::fake()->image('replacement.jpg'),
        ])
        ->assertSessionHasErrors('cover_image');

    expect($track->images()->where('type', 'cover')->first()->path)->toBe($cover->path);
    Storage::disk('public')->assertExists($cover->path);
    expect(Storage::disk('public')->allFiles("tracks/{$track->slug}"))->toHaveCount(1);

    $this->actingAs($admin)
        ->put(route('admin.tracks.update', $track), [
            'name' => $track->name,
            'slug' => $track->slug,
            'lat' => $track->lat,
            'lng' => $track->lng,
            'remove_cover' => '1',
            'cover_image' => UploadedFile::fake()->image('replacement.jpg'),
        ])
        ->assertRedirect(route('admin.tracks.index'));

    $newCover = $track->fresh()->images()->where('type', 'cover')->firstOrFail();
    expect($newCover->path)->not->toBe($cover->path);
    Storage::disk('public')->assertMissing($cover->path);
    Storage::disk('public')->assertExists($newCover->path);
});

it('atjauno iepriekšējos datus un iztīra jaunos failus, ja attēla ieraksts neizdodas', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $track = createTrackForImageTest();
    $oldGalleryImage = addTrackImageForTest($track, 'gallery', 1, 'old-gallery.jpg');
    $oldPath = $oldGalleryImage->path;

    TrackImage::creating(function (TrackImage $image): void {
        if ($image->type === 'gallery' && $image->path !== 'tracks/attelu-testa-trase/old-gallery.jpg') {
            throw new RuntimeException('Simulēta datubāzes kļūme attēla saglabāšanā.');
        }
    });

    try {
        $response = $this->actingAs($admin)
            ->put(route('admin.tracks.update', $track), [
                'name' => 'Nosaukums, ko nedrīkst saglabāt',
                'slug' => $track->slug,
                'lat' => $track->lat,
                'lng' => $track->lng,
                'remove_gallery_ids' => [$oldGalleryImage->id],
                'gallery_images' => [UploadedFile::fake()->image('new-gallery.jpg')],
            ]);
    } finally {
        TrackImage::flushEventListeners();
    }

    $response->assertServerError();
    expect($track->fresh()->name)->toBe('Attēlu testa trase');
    $this->assertDatabaseHas('track_images', ['id' => $oldGalleryImage->id, 'path' => $oldPath]);
    Storage::disk('public')->assertExists($oldPath);
    expect(Storage::disk('public')->allFiles("tracks/{$track->slug}"))->toHaveCount(1);
});

it('noņem trases ierakstus un attēlu failus, kad administrators izdzēš trasi', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $track = createTrackForImageTest();
    $images = collect([
        addTrackImageForTest($track, 'cover', 0, 'cover.jpg'),
        addTrackImageForTest($track, 'gallery', 1, 'gallery-1.jpg'),
    ]);

    $this->actingAs($admin)
        ->delete(route('admin.tracks.destroy', $track))
        ->assertRedirect(route('admin.tracks.index'));

    $this->assertDatabaseMissing('tracks', ['id' => $track->id]);
    $this->assertDatabaseMissing('track_images', ['track_id' => $track->id]);
    foreach ($images as $image) {
        Storage::disk('public')->assertMissing($image->path);
    }
});
