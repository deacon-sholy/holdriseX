<?php

use App\Http\Controllers\Api\Admin\AnnouncementController;
use App\Http\Controllers\Api\Admin\AuditLogController;
use App\Http\Controllers\Api\Admin\AuthController;
use App\Http\Controllers\Api\Admin\CopyTradingController;
use App\Http\Controllers\Api\Admin\DashboardController;
use App\Http\Controllers\Api\Admin\DepositController;
use App\Http\Controllers\Api\Admin\KycController;
use App\Http\Controllers\Api\Admin\PlanController;
use App\Http\Controllers\Api\Admin\SettingController;
use App\Http\Controllers\Api\Admin\TradeController;
use App\Http\Controllers\Api\Admin\UserController;
use App\Http\Controllers\Api\Admin\WithdrawalController;
use Illuminate\Support\Facades\Route;

Route::post('/admin/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum', 'admin')->prefix('admin')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::get('/dashboard', [DashboardController::class, 'index']);

    Route::apiResource('users', UserController::class);
    Route::get('/users-stats', [UserController::class, 'stats']);
    Route::patch('/users/{id}/toggle-status', [UserController::class, 'toggleStatus']);

    Route::apiResource('deposits', DepositController::class)->only(['index', 'show']);
    Route::post('/deposits/{id}/approve', [DepositController::class, 'approve']);
    Route::post('/deposits/{id}/reject', [DepositController::class, 'reject']);
    Route::get('/deposits-stats', [DepositController::class, 'stats']);

    Route::apiResource('withdrawals', WithdrawalController::class)->only(['index', 'show']);
    Route::post('/withdrawals/{id}/approve', [WithdrawalController::class, 'approve']);
    Route::post('/withdrawals/{id}/reject', [WithdrawalController::class, 'reject']);
    Route::get('/withdrawals-stats', [WithdrawalController::class, 'stats']);

    Route::apiResource('trades', TradeController::class)->only(['index', 'show']);
    Route::post('/trades/{id}/close', [TradeController::class, 'close']);
    Route::get('/trades-stats', [TradeController::class, 'stats']);

    Route::apiResource('plans', PlanController::class);
    Route::patch('/plans/{id}/toggle-status', [PlanController::class, 'toggleStatus']);

    Route::apiResource('copy-traders', CopyTradingController::class);
    Route::patch('/copy-traders/{id}/toggle-status', [CopyTradingController::class, 'toggleStatus']);
    Route::get('/copy-trading/activity', [CopyTradingController::class, 'activity']);

    Route::apiResource('kyc', KycController::class)->only(['index', 'show']);
    Route::post('/kyc/{id}/approve', [KycController::class, 'approve']);
    Route::post('/kyc/{id}/reject', [KycController::class, 'reject']);
    Route::get('/kyc-stats', [KycController::class, 'stats']);

    Route::get('/settings', [SettingController::class, 'index']);
    Route::put('/settings', [SettingController::class, 'update']);

    Route::apiResource('announcements', AnnouncementController::class);
    Route::post('/announcements/{id}/publish', [AnnouncementController::class, 'publish']);
    Route::get('/announcements-stats', [AnnouncementController::class, 'stats']);

    Route::get('/audit-logs', [AuditLogController::class, 'index']);
    Route::get('/audit-logs/{id}', [AuditLogController::class, 'show']);
    Route::get('/audit-logs-stats', [AuditLogController::class, 'stats']);
});
