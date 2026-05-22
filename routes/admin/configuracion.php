<?php

use Illuminate\Support\Facades\Route;

// Configuración de Notificaciones
Route::get('/configuracion/notificaciones', \App\Livewire\Admin\Configuracion\ConfigurarNotificaciones::class)
    ->name('configuracion.notificaciones')
    ->middleware('checkAdminPermission:access empresas');
