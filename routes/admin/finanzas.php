<?php

use Illuminate\Support\Facades\Route;

// Rutas para el módulo de Finanzas independiente
Route::prefix('finanzas')->name('finanzas.')->group(function () {
    // Listado principal
    Route::get('/', \App\Livewire\Admin\Finanzas\Index::class)->name('index');
    
    // Crear nueva transacción
    Route::get('/crear', \App\Livewire\Admin\Finanzas\Create::class)->name('create');
    
    // Editar transacción existente
    Route::get('/{id}/editar', \App\Livewire\Admin\Finanzas\Edit::class)->name('edit');
    
    // Ver detalle de transacción
    Route::get('/{id}', \App\Livewire\Admin\Finanzas\Show::class)->name('show');
});
