<?php

use App\Console\Commands\ExpireMiningRecords;
use App\Http\Middleware\AdminOnly;
use App\Http\Middleware\EnsureActive;
use App\Http\Middleware\SuperAdminOnly;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withCommands([ExpireMiningRecords::class])
    ->withSchedule(function (Schedule $schedule) {
        $schedule->command('mining:expire')->hourly();
    })
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias(['admin' => AdminOnly::class, 'active' => EnsureActive::class, 'super-admin' => SuperAdminOnly::class]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
