<?php

use App\Models\Rider;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $this->replaceRegistrationKeys(fn (array $attributes): string => Rider::registrationKeyFor($attributes));
    }

    public function down(): void
    {
        $this->replaceRegistrationKeys(fn (array $attributes): string => $this->trackRegistrationKeyFor($attributes));
    }

    private function replaceRegistrationKeys(callable $keyFor): void
    {
        DB::table('riders')->orderBy('id')->chunkById(200, function ($riders): void {
            foreach ($riders as $rider) {
                DB::table('riders')->where('id', $rider->id)->update([
                    'registration_key' => hash('sha256', 'rider-key-migration:'.$rider->id),
                ]);
            }
        });

        $usedKeys = [];

        DB::table('riders')->orderBy('id')->chunkById(200, function ($riders) use ($keyFor, &$usedKeys): void {
            foreach ($riders as $rider) {
                $attributes = (array) $rider;
                $key = $keyFor($attributes);

                while (isset($usedKeys[$key])) {
                    $key = hash('sha256', $key.'|legacy:'.$rider->id);
                }

                $usedKeys[$key] = true;
                DB::table('riders')->where('id', $rider->id)->update([
                    'registration_key' => $key,
                ]);
            }
        });
    }

    private function trackRegistrationKeyFor(array $attributes): string
    {
        $identity = ! empty($attributes['user_id'])
            ? 'user:'.$attributes['user_id']
            : 'guest:'.mb_strtolower(trim($attributes['name'] ?? '')).':'.mb_strtolower(trim($attributes['surname'] ?? ''));

        return hash('sha256', implode('|', [
            $attributes['track_id'] ?? '',
            substr((string) ($attributes['ride_date'] ?? ''), 0, 10),
            $identity,
        ]));
    }
};
