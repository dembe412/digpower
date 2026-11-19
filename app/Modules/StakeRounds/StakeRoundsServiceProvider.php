<?php

namespace App\Modules\StakeRounds;

use App\Modules\StakeRounds\Console\RunRounds;
use App\Modules\StakeRounds\Contracts\WalletGateway;
use App\Modules\StakeRounds\Services\RateIndex;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

/** Self-contained module: config, migrations, routes, command. Remove this provider to remove the feature. */
class StakeRoundsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/config/stake_rounds.php', 'stake_rounds');
        $this->app->singleton(RateIndex::class);
        $this->app->bind(WalletGateway::class, fn ($app) => $app->make(config('stake_rounds.wallet_gateway')));
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');
        foreach (config('stake_rounds.route_prefixes') as $prefix) {
            Route::prefix('api')->middleware('api')->group(function () use ($prefix) {
                $routePrefix = $prefix; // consumed by routes/api.php
                require __DIR__.'/routes/api.php';
            });
        }
        if ($this->app->runningInConsole()) {
            $this->commands([RunRounds::class]);
        }
    }
}
