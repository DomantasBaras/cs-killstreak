<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ServerStatusController;
use App\Http\Controllers\Api\VipPaymentController;
use App\Http\Controllers\Api\AppealsController;

Route::get('/server/status', [ServerStatusController::class, 'index']);

Route::post('/vip/checkout', [VipPaymentController::class, 'createCheckout']);
Route::post('/vip/webhook', [VipPaymentController::class, 'webhook']);

Route::post('/appeals', [AppealsController::class, 'store']);

