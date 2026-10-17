<?php

namespace App\Console\Commands;

use App\Support\ExpiredContentPruner;
use Illuminate\Console\Command;

class PruneOldRiders extends Command
{
    protected $signature = 'riders:prune-old';

    protected $description = 'Dzēš vecus pieteikumus, termiņā beigušos paziņojumus un vecus komentārus';

    public function handle(): int
    {
        $deleted = app(ExpiredContentPruner::class)->prune();

        $this->table(
            ['Datu tips', 'Dzēsto ierakstu skaits'],
            collect($deleted)->map(fn (int $count, string $type) => [$type, $count])->values()->all()
        );

        return self::SUCCESS;
    }
}
