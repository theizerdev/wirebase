<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Admin\Solicitudes\Dashboard;
use App\Livewire\Admin\Solicitudes\Index;
use App\Livewire\Admin\Solicitudes\Show;

/*
|--------------------------------------------------------------------------
| Rutas de Gestión de Solicitudes
|--------------------------------------------------------------------------
*/

Route::prefix('solicitudes')->name('solicitudes.')->group(function () {
    // Dashboard principal
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    
    // Lista de solicitudes con filtros
    Route::get('/', Index::class)->name('index');
    
    // Detalle de una solicitud específica
    Route::get('/{solicitud}', Show::class)->name('show');
});
