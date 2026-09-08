<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Track;

class TrackSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         $tracks = [
            [
                'name' => 'Burtnieki',
                'lat' => 57.68364,
                'lng' => 25.28872,
                'description' => 'Burtnieku mototrase',
                'created_at' => now(),
                'updated_at' => now(),
            ],
         ];

        Track::insert($tracks);
    }
}
