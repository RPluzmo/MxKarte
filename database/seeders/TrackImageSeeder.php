<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use App\Models\Track;
use App\Models\TrackImage;

class TrackImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (Track::all() as $track) {
            $images = [
                ['type' => 'cover', 'filename' => 'cover.png', 'sort_order' => 0],
                ['type' => 'gallery', 'filename' => 'gallery_1.png', 'sort_order' => 1],
                ['type' => 'gallery', 'filename' => 'gallery_2.png', 'sort_order' => 2],
                ['type' => 'gallery', 'filename' => 'gallery_3.png', 'sort_order' => 3],
                ['type' => 'gallery', 'filename' => 'gallery_4.png', 'sort_order' => 4],
            ];

            foreach ($images as $image) {
                $path = "tracks/{$track->slug}/{$image['filename']}";

                if (! Storage::disk('public')->exists($path)) {
                    continue;
                }

                TrackImage::updateOrCreate(
                    [
                        'track_id' => $track->id,
                        'type' => $image['type'],
                        'sort_order' => $image['sort_order'],
                    ],
                    ['path' => $path]
                );
            }
        }
    }
}