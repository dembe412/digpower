<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ContentController;
use App\Http\Controllers\DepositController;
use App\Http\Controllers\ForexController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\StatementController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\TransferController;
use App\Http\Controllers\VipController;
use App\Http\Controllers\WithdrawController;
use Illuminate\Support\Facades\Route;

// ---- Public
Route::post('auth/register', [AuthController::class, 'register'])->middleware('throttle:10,1');
Route::post('auth/login', [AuthController::class, 'login'])->middleware('throttle:10,1');
Route::post('auth/forgot-password', [AuthController::class, 'forgotPassword'])->middleware('throttle:5,1');
Route::post('auth/reset-password', [AuthController::class, 'resetPassword'])->middleware('throttle:5,1');
Route::post('webhooks/deposit', [DepositController::class, 'webhook']);

Route::get('faqs', [ContentController::class, 'faqs']);
Route::get('about', [ContentController::class, 'about']);
Route::get('app', [ContentController::class, 'app']);
Route::get('support/contact', [ContentController::class, 'supportContact']);
Route::get('vip', [VipController::class, 'index']);
Route::get('products', [ProductController::class, 'index']);
Route::get('home-public', [HomeController::class, 'index']);
Route::get('prices', [HomeController::class, 'prices'])->middleware('throttle:60,1');
Route::get('forex/rates', [ForexController::class, 'rates'])->middleware('throttle:60,1');
Route::get('chat/messages', [ChatController::class, 'messages']);
Route::get('chat/members', [ChatController::class, 'members']);

// ---- Authenticated member
Route::middleware(['auth:sanctum', 'active'])->group(function () {
    Route::post('auth/logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'me']);

    Route::post('chat/messages', [ChatController::class, 'send'])->middleware('throttle:60,1');
    Route::delete('chat/messages/{id}', [ChatController::class, 'delete']);

    Route::get('home', [HomeController::class, 'index']);
    Route::get('events', [ContentController::class, 'events']);

    Route::get('deposit/options', [DepositController::class, 'options']);
    Route::get('deposits', [DepositController::class, 'index']);
    Route::post('deposits', [DepositController::class, 'store']);
    Route::post('deposits/{deposit}/txid', [DepositController::class, 'submitTxid']);

    Route::get('withdraw/options', [WithdrawController::class, 'options']);
    Route::get('withdrawals', [WithdrawController::class, 'index']);
    Route::post('withdrawals', [WithdrawController::class, 'store'])->middleware('throttle:10,1');

    Route::post('forex/convert', [ForexController::class, 'convert'])->middleware('throttle:30,1');

    Route::post('transfer', [TransferController::class, 'store'])->middleware('throttle:20,1');
    Route::get('statements', [StatementController::class, 'index']);

    Route::get('products/owned', [ProductController::class, 'owned']);
    Route::post('products/{product}/purchases', [PurchaseController::class, 'store']);
    Route::post('purchases/{purchase}/cash-out', [PurchaseController::class, 'cashOut']);
    Route::get('vip/owned', [VipController::class, 'owned']);
    Route::get('vip/{level}/qualification', [VipController::class, 'qualification']);
    Route::post('vip/{level}/purchases', [VipController::class, 'store']);
    Route::post('vip/purchases/{vipPurchase}/salary', [VipController::class, 'claimSalary']);

    Route::get('team', [TeamController::class, 'show']);
    Route::get('team/commissions', [TeamController::class, 'commissions']);
    Route::get('team/level/{level}', [TeamController::class, 'members']);

    Route::post('account/password', [AccountController::class, 'changePassword']);
    Route::post('account/pin', [AccountController::class, 'setPin']);
    Route::post('account/totp/setup', [AccountController::class, 'totpSetup']);
    Route::post('account/totp/enable', [AccountController::class, 'totpEnable']);
    Route::post('account/totp/disable', [AccountController::class, 'totpDisable']);
    Route::post('account/avatar', [AccountController::class, 'updateAvatar']);
    Route::delete('account/avatar', [AccountController::class, 'deleteAvatar']);
    Route::patch('account/preferences', [AccountController::class, 'preferences']);
    Route::get('notifications', [AccountController::class, 'notifications']);
    Route::post('notifications/read', [AccountController::class, 'markNotificationsRead']);

    Route::get('support/tickets', [ContentController::class, 'tickets']);
    Route::post('support/tickets', [ContentController::class, 'createTicket']);
    Route::get('support/tickets/{ticket}', [ContentController::class, 'showTicket']);
    Route::post('support/tickets/{ticket}/reply', [ContentController::class, 'replyTicket']);
});

// ---- Admin
Route::prefix('admin')->middleware(['auth:sanctum', 'active', 'admin'])->group(function () {
    Route::get('overview', [AdminController::class, 'overview']);
    Route::get('prices', [AdminController::class, 'prices']);
    Route::put('prices', [AdminController::class, 'savePrices']);

    Route::get('deposits', [AdminController::class, 'deposits']);
    Route::post('deposits/{deposit}/confirm', [AdminController::class, 'confirmDeposit']);
    Route::post('deposits/{deposit}/reject', [AdminController::class, 'rejectDeposit']);

    Route::get('withdrawals', [AdminController::class, 'withdrawals']);
    Route::post('withdrawals/{withdrawal}/approve', [AdminController::class, 'approveWithdrawal']);
    Route::post('withdrawals/{withdrawal}/complete', [AdminController::class, 'completeWithdrawal']);
    Route::post('withdrawals/{withdrawal}/reject', [AdminController::class, 'rejectWithdrawal']);

    Route::get('yield-batches', [AdminController::class, 'yieldBatches']);
    Route::post('yield-batches', [AdminController::class, 'createYieldBatch']);
    Route::post('yield-batches/{batch}/distribute', [AdminController::class, 'distributeYield']);

    Route::get('users', [AdminController::class, 'users']);
    Route::post('users/{user}/toggle', [AdminController::class, 'toggleUser']);
    Route::delete('users/{user}', [AdminController::class, 'archiveUser']);
    Route::post('users/{user}/assign-product', [AdminController::class, 'assignProduct']);
    Route::get('vip-levels', [AdminController::class, 'vipLevels']);
    Route::get('vip-purchases', [AdminController::class, 'vipPurchases']);
    Route::post('vip-purchases/{vipPurchase}/activate', [AdminController::class, 'activateVipPurchase']);
    Route::post('vip-purchases/{vipPurchase}/reject', [AdminController::class, 'rejectVipPurchase']);
    Route::get('products', [AdminController::class, 'products']);
    Route::post('products', [AdminController::class, 'saveProduct']);
    Route::put('products/{product}', [AdminController::class, 'saveProduct']);
    Route::delete('products/{product}', [AdminController::class, 'deleteProduct']);
    Route::get('purchases', [AdminController::class, 'purchases']);
    Route::post('purchases/{purchase}/activate', [AdminController::class, 'activatePurchase']);
    Route::post('purchases/{purchase}/reject', [AdminController::class, 'rejectPurchase']);

    Route::post('announcements', [AdminController::class, 'saveAnnouncement']);
    Route::put('announcements/{announcement}', [AdminController::class, 'saveAnnouncement']);
    Route::delete('announcements/{announcement}', [AdminController::class, 'deleteAnnouncement']);
    Route::post('faqs', [AdminController::class, 'saveFaq']);
    Route::put('faqs/{faq}', [AdminController::class, 'saveFaq']);
    Route::delete('faqs/{faq}', [AdminController::class, 'deleteFaq']);
    Route::put('settings', [AdminController::class, 'saveSettings']);
    Route::put('vip-levels/{level}', [AdminController::class, 'saveVipLevel']);

    Route::get('tickets', [AdminController::class, 'tickets']);
    Route::post('tickets/{ticket}/reply', [AdminController::class, 'replyTicket']);
    Route::get('chat/private-threads', [ChatController::class, 'privateThreads']);
    Route::get('audit-log', [AdminController::class, 'auditLog']);
});

Route::prefix('super-admin')->middleware(['auth:sanctum', 'active', 'super-admin'])->group(function () {
    Route::get('admins', [AdminController::class, 'admins']);
    Route::post('admins', [AdminController::class, 'createAdmin']);
    Route::put('admins/{admin}', [AdminController::class, 'updateAdmin']);
    Route::post('admins/{admin}/toggle', [AdminController::class, 'toggleAdmin']);
});
