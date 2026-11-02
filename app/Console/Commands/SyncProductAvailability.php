<?php

namespace App\Console\Commands;

use App\Services\ProductAvailabilityService;
use Illuminate\Console\Command;

class SyncProductAvailability extends Command
{
    protected $signature = 'iyma:sync-availability';

    protected $description = 'Update scheduled product availability statuses';

    public function handle(ProductAvailabilityService $service): int
    {
        $count = $service->syncScheduled();
        $this->info("Updated {$count} products");

        return self::SUCCESS;
    }
}
