<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\WhatsApp\WhatsAppReactController;

Route::prefix('whatsapp/api')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/status', [WhatsAppReactController::class, 'getStatus'])->name('whatsapp.api.status');
    Route::get('/conversations', [WhatsAppReactController::class, 'getConversations'])->name('whatsapp.api.conversations');
    Route::get('/thread', [WhatsAppReactController::class, 'getThread'])->name('whatsapp.api.thread');
    Route::post('/send', [WhatsAppReactController::class, 'sendMessage'])->name('whatsapp.api.send');
    Route::post('/connect', [WhatsAppReactController::class, 'connect'])->name('whatsapp.api.connect');
    Route::delete('/disconnect', [WhatsAppReactController::class, 'disconnect'])->name('whatsapp.api.disconnect');
    Route::get('/qr', [WhatsAppReactController::class, 'getQr'])->name('whatsapp.api.qr');
    Route::get('/contacts', [WhatsAppReactController::class, 'getContacts'])->name('whatsapp.api.contacts');
    Route::get('/stats', [WhatsAppReactController::class, 'getStats'])->name('whatsapp.api.stats');
});
