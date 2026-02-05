<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WhatsAppController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});



// WhatsApp API Routes
Route::prefix('whatsapp')->group(function () {
    Route::get('/status', [WhatsAppController::class, 'status']);
    Route::get('/qr-code', [WhatsAppController::class, 'qrCode']);
    Route::post('/send-message', [WhatsAppController::class, 'sendMessage']);
    Route::get('/messages', [WhatsAppController::class, 'messages']);
    Route::post('/connect', [WhatsAppController::class, 'connect']);
    Route::post('/disconnect', [WhatsAppController::class, 'disconnect']);
    Route::post('/webhook', [WhatsAppController::class, 'webhook']);
});