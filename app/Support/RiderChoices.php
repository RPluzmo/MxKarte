<?php

namespace App\Support;

use App\Models\Club;
use Illuminate\Support\Facades\Storage;

class RiderChoices
{
    public static function clubs(): array
    {
        return Club::orderBy('name')->get()->map(fn (Club $club) => [
            'name' => $club->name,
            'image_url' => $club->logo_path ? asset('storage/' . $club->logo_path) : null,
            'alt' => $club->name . ' logo',
            'hint' => 'Nav logo',
        ])->all();
    }

    public static function categories(): array
    {
        return self::fromFiles([
            'MX 50' => 'categories/mx-50.png',
            'MX 65' => 'categories/mx-65.png',
            'MX 85' => 'categories/mx-85.png',
            'MX 125' => 'categories/mx-125.png',
            'MX 250' => 'categories/mx-250.png',
            'MX 450' => 'categories/mx-450.png',
            'Kvadri' => 'categories/kvadri.png',
            'Blakusvāģi' => 'categories/blakusvagi.png',
        ], ' motocikls');
    }

    public static function experienceLevels(): array
    {
        return self::fromFiles([
            'Iesācējs' => 'experience/iesacejs.png',
            'Amatieris' => 'experience/amatieris.png',
            'Veterāns' => 'experience/veterans.png',
            'Profesionālis' => 'experience/profesionalis.png',
        ], ' braucējs');
    }

    private static function fromFiles(array $files, string $altSuffix): array
    {
        $disk = Storage::disk('public');

        return collect($files)->map(fn (string $path, string $name) => [
            'name' => $name,
            'image_url' => $disk->exists($path) ? $disk->url($path) : null,
            'alt' => $name . $altSuffix,
            'hint' => 'Attēls tiks pievienots',
        ])->values()->all();
    }
}
