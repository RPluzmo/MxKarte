<?php

use App\Support\ExpiredContentPruner;
use Illuminate\Support\Facades\Cache;

it('can retry expired-content cleanup after a failure on the same day', function () {
    Cache::flush();

    $pruner = new class extends ExpiredContentPruner
    {
        public int $calls = 0;

        public function prune(): array
        {
            $this->calls++;

            if ($this->calls === 1) {
                throw new RuntimeException('Simulēta tīrīšanas kļūme.');
            }

            return ['Novecojis saturs' => 0];
        }
    };

    expect(fn () => $pruner->pruneOncePerDay())->toThrow(RuntimeException::class);
    expect($pruner->pruneOncePerDay())->toBe(['Novecojis saturs' => 0]);
    expect($pruner->pruneOncePerDay())->toBeNull();
    expect($pruner->calls)->toBe(2);
});
