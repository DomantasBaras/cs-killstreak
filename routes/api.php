<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ServerStatusController;
use App\Http\Controllers\Api\VipPaymentController;
use App\Http\Controllers\Api\AppealsController;
use App\Http\Controllers\Api\StatsController;


Route::get('/server/status', [ServerStatusController::class, 'index']);

Route::post('/vip/checkout', [VipPaymentController::class, 'createCheckout']);
Route::post('/vip/webhook', [VipPaymentController::class, 'webhook']);

Route::post('/appeals', [AppealsController::class, 'store']);

Route::get('/stats', [StatsController::class, 'index']);
Route::post('/stats', [StatsController::class, 'store']);
