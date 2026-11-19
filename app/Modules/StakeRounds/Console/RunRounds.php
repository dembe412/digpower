<?php

namespace App\Modules\StakeRounds\Console;

use App\Modules\StakeRounds\Services\RoundEngine;
use Illuminate\Console\Command;

class RunRounds extends Command
{
    protected $signature = 'stake:run {--once : Advance a single step and exit}';

    protected $description = 'Run the Stake Rounds engine (keep alive with supervisor/systemd)';

    public function handle(RoundEngine $engine): int
    {
        do {
            $round = $engine->advance();
            if ($this->option('once')) {
                $this->info($round ? "Round {$round->id}: {$round->status}" : 'Engine paused/disabled');

                return self::SUCCESS;
            }
            usleep(250_000);
        } while (true);
    }
}
