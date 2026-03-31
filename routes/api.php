<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ServerStatusController;
use App\Http\Controllers\Api\VipPaymentController;

Route::get('/server/status', [ServerStatusController::class, 'index']);

Route::post('/vip/checkout', [VipPaymentController::class, 'createCheckout']);
Route::post('/vip/webhook', [VipPaymentController::class, 'webhook']);