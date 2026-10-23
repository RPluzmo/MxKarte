<?php

namespace App\Console\Commands;

use App\Support\TrackImageManager;
use Illuminate\Console\Command;

class CleanupPendingTrackImageFiles extends Command
{
    protected $signature = 'tracks:cleanup-image-files';

    protected $description = 'Atkārtoti mēģina izdzēst trases attēlu failus, kuru dzēšana iepriekš neizdevās';

    public function handle(TrackImageManager $imageManager): int
    {
        $deletedCount = $imageManager->retryPendingDeletes();
        $this->info("Izdzēsti {$deletedCount} rindā reģistrēti trases attēlu faili.");

        return self::SUCCESS;
    }
}
