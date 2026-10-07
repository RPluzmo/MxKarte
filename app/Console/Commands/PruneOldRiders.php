<?php

namespace App\Console\Commands;

use App\Models\Rider;
use Illuminate\Console\Command;

class PruneOldRiders extends Command
{
    protected $signature = 'riders:prune-old';

    protected $description = 'Dzēš treniņu pieteikumus, kuru pieteiktā diena jau ir pagājusi';

    public function handle(): int
    {
        $deleted = Rider::deleteExpired();

        $this->info("Dzēsti pieteikumi: {$deleted}");

        return self::SUCCESS;
    }
}
