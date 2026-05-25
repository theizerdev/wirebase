<?php

use Illuminate\Support\Facades\Route;

// Rutas para el módulo de Inventario independiente
Route::prefix('extension/inventario')->name('inventario.')->group(function () {
    // Listado principal
    Route::get('/', \App\Livewire\Admin\Inventario\Index::class)->name('index');
    
    // Crear nuevo ítem
    Route::get('/crear', \App\Livewire\Admin\Inventario\Create::class)->name('create');
    
    // Editar ítem existente
    Route::get('/{id}/editar', \App\Livewire\Admin\Inventario\Edit::class)->name('edit');
    
    // Ver detalle de ítem
    Route::get('/{id}', \App\Livewire\Admin\Inventario\Show::class)->name('show');
});
