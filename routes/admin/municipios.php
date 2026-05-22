<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Admin\Municipios\Index;
use App\Livewire\Admin\Municipios\Create;
use App\Livewire\Admin\Municipios\Edit;

// Rutas para Municipios
Route::middleware(['auth', 'verified'])
    ->prefix('municipios')
    ->name('municipios.')
    ->group(function () {
        Route::get('/', Index::class)->name('index');
        Route::get('/create', Create::class)->name('create');
        Route::get('/{id}/edit', Edit::class)->name('edit');
    });