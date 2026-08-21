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
use App\Http\Controllers\Api\User;
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

// ─── User Routes ──────────────────────────────────────────────
// Public (no auth)
Route::get('/public-settings', [SettingController::class, 'publicSettings']);
Route::get('/plans', [User\InvestmentController::class, 'plans']);
Route::get('/deposit-settings', [User\InvestmentController::class, 'depositSettings']);
Route::post('/user/register', [User\AuthController::class, 'register']);
Route::post('/user/login', [User\AuthController::class, 'login']);
Route::post('/user/forgot-password', [User\AuthController::class, 'forgotPassword']);
Route::post('/user/reset-password', [User\AuthController::class, 'resetPassword']);

// Protected (auth:sanctum + auth.user middleware)
Route::middleware('auth:sanctum', 'auth.user')->prefix('user')->group(function () {
    Route::post('/logout', [User\AuthController::class, 'logout']);
    Route::get('/me', [User\AuthController::class, 'me']);

    Route::get('/dashboard', [User\DashboardController::class, 'index']);

    Route::get('/profile', [User\ProfileController::class, 'show']);
    Route::put('/profile', [User\ProfileController::class, 'update']);
    Route::put('/password', [User\ProfileController::class, 'changePassword']);

    Route::get('/deposits', [User\DepositController::class, 'index']);
    Route::get('/deposits/{id}', [User\DepositController::class, 'show']);
    Route::post('/deposits', [User\DepositController::class, 'store']);
    Route::get('/deposits-stats', [User\DepositController::class, 'stats']);

    Route::get('/withdrawals', [User\WithdrawalController::class, 'index']);
    Route::get('/withdrawals/{id}', [User\WithdrawalController::class, 'show']);
    Route::post('/withdrawals', [User\WithdrawalController::class, 'store']);
    Route::get('/withdrawals-stats', [User\WithdrawalController::class, 'stats']);

    Route::get('/plans', [User\InvestmentController::class, 'plans']);
    Route::get('/plans/{id}', [User\InvestmentController::class, 'plan']);
    Route::post('/invest', [User\InvestmentController::class, 'invest']);
    Route::get('/investments', [User\InvestmentController::class, 'myInvestments']);
    Route::get('/investments/{id}', [User\InvestmentController::class, 'showInvestment']);
    Route::get('/investments-stats', [User\InvestmentController::class, 'stats']);

    Route::get('/trades', [User\TradeController::class, 'index']);
    Route::get('/trades/{id}', [User\TradeController::class, 'show']);
    Route::post('/trades/open', [User\TradeController::class, 'open']);
    Route::post('/trades/{id}/close', [User\TradeController::class, 'close']);
    Route::get('/trades-stats', [User\TradeController::class, 'stats']);

    Route::get('/copy-traders', [User\CopyTradingController::class, 'traders']);
    Route::get('/copy-traders/{id}', [User\CopyTradingController::class, 'trader']);
    Route::post('/copy-traders/{traderId}/subscribe', [User\CopyTradingController::class, 'subscribe']);
    Route::get('/my-copy-trades', [User\CopyTradingController::class, 'myCopyTrades']);
    Route::post('/copy-trades/{id}/close', [User\CopyTradingController::class, 'closeCopyTrade']);
    Route::get('/copy-trades-stats', [User\CopyTradingController::class, 'stats']);

    Route::get('/kyc', [User\KycController::class, 'index']);
    Route::get('/kyc/{id}', [User\KycController::class, 'show']);
    Route::post('/kyc', [User\KycController::class, 'store']);
    Route::get('/kyc-status', [User\KycController::class, 'status']);

    Route::get('/announcements', [User\AnnouncementController::class, 'index']);
    Route::get('/announcements/{id}', [User\AnnouncementController::class, 'show']);
});
