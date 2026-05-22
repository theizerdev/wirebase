<?php

use Illuminate\Support\Facades\Route;

// Países
Route::middleware(['checkAdminPermission:access paises'])->group(function () {
    Route::get('/paises', \App\Livewire\Admin\Paises\PaisIndex::class)->name('paises.index');
    Route::get('/paises/crear', \App\Livewire\Admin\Paises\Create::class)->name('paises.create');
    Route::get('/paises/{pais}/editar', \App\Livewire\Admin\Paises\Edit::class)->name('paises.edit');
});
