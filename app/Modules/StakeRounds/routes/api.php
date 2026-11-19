<?php

use App\Modules\StakeRounds\Http\Controllers\Admin\StakeAdminController;
use App\Modules\StakeRounds\Http\Controllers\StakeController;
use Illuminate\Support\Facades\Route;

/** @var string $routePrefix */
Route::prefix($routePrefix)->middleware(['auth:sanctum', 'active'])->group(function () {
    Route::get('', [StakeController::class, 'current']);
    Route::get('current', [StakeController::class, 'current']);
    Route::post('enter', [StakeController::class, 'enter'])->middleware('throttle:30,1');
    Route::post('cashout', [StakeController::class, 'cashOut'])->middleware('throttle:60,1');
    Route::get('history', [StakeController::class, 'history']);
    Route::get('rounds', [StakeController::class, 'rounds']);
    Route::get('rounds/{round}/verify', [StakeController::class, 'verify']);

    Route::get('profile', [StakeController::class, 'profile']);
    Route::post('profile/confirm-adult', [StakeController::class, 'confirmAdult']);
    Route::post('profile/limit', [StakeController::class, 'setLimit']);
    Route::post('profile/exclude', [StakeController::class, 'selfExclude']);
});

Route::prefix('admin/'.$routePrefix)->middleware(array_merge(['auth:sanctum', 'active'], config('stake_rounds.admin_middleware')))->group(function () {
    Route::get('summary', [StakeAdminController::class, 'summary']);
    Route::post('settings', [StakeAdminController::class, 'updateSettings']);
    Route::get('rounds', [StakeAdminController::class, 'rounds']);
    Route::get('rounds/{round}/stakes', [StakeAdminController::class, 'stakes']);
    Route::post('pause', [StakeAdminController::class, 'pause']);
});
