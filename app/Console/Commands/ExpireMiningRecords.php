<?php

namespace App\Console\Commands;

use App\Services\YieldService;
use Illuminate\Console\Command;

class ExpireMiningRecords extends Command
{
    protected $signature = 'mining:expire';

    protected $description = 'Mark unclaimed earnings past their claim window as expired';

    public function handle(YieldService $svc): int
    {
        $this->info('Expired: '.$svc->expireStale());

        return self::SUCCESS;
    }
}
