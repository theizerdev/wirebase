<?php

use App\Http\Controllers\Admin\WhatsAppController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin/whatsapp')->name('admin.whatsapp.')->group(function () {
    Route::get('/dashboard', [WhatsAppController::class, 'dashboard'])->name('dashboard');
    Route::post('/create-company', [WhatsAppController::class, 'createCompany'])->name('create-company');
    Route::get('/status/{companyId}/{apiKey}', [WhatsAppController::class, 'getStatus'])->name('status');
    Route::get('/qr/{companyId}/{apiKey}', [WhatsAppController::class, 'getQR'])->name('qr');
    Route::post('/send/{companyId}/{apiKey}', [WhatsAppController::class, 'sendMessage'])->name('send');
});