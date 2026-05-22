<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Admin\Parroquias\Index;
use App\Livewire\Admin\Parroquias\Create;
use App\Livewire\Admin\Parroquias\Edit;

// Rutas para Parroquias
Route::middleware(['auth', 'verified'])
    ->prefix('parroquias')
    ->name('parroquias.')
    ->group(function () {
        Route::get('/', Index::class)->name('index');
        Route::get('/create', Create::class)->name('create');
        Route::get('/{id}/edit', Edit::class)->name('edit');
    });