<?php

use Illuminate\Support\Facades\Route;

// WhatsApp (Ruta legacy fuera del grupo)
Route::get('/whatsapp', \App\Livewire\Admin\Whatsapp\Index::class)->name('whatsapp.index')->middleware('checkAdminPermission:access whatsapp');
