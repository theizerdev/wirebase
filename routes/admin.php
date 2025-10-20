<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;

// Proteger todas las rutas de administración con el middleware de autenticación
Route::prefix('admin')
    ->middleware(['auth', 'verified'])
    ->name('admin.')
    ->group(function () {
        // Dashboard de administración
        Route::get('/dashboard', \App\Livewire\Dashboard::class)->name('dashboard');

        // Ruta para sesiones activas usando Livewire
        Route::get('/active-sessions', \App\Livewire\Admin\ActiveSessions::class)->name('active-sessions.index');

        // Rutas para empresas
        Route::prefix('empresas')->group(function () {
            Route::get('/', \App\Livewire\Admin\Empresas\Index::class)->name('empresas.index');
            Route::get('/create', \App\Livewire\Admin\Empresas\Create::class)->name('empresas.create');
            Route::get('/{empresa}', \App\Livewire\Admin\Empresas\Show::class)->name('empresas.show');
            Route::get('/{empresa}/edit', \App\Livewire\Admin\Empresas\Edit::class)->name('empresas.edit');
        });

        // Rutas para sucursales
        Route::prefix('sucursales')->group(function () {
            Route::get('/', \App\Livewire\Admin\Sucursales\Index::class)->name('sucursales.index');
            Route::get('/create', \App\Livewire\Admin\Sucursales\Create::class)->name('sucursales.create');
            Route::get('/{sucursal}', \App\Livewire\Admin\Sucursales\Show::class)->name('sucursales.show');
            Route::get('/{sucursal}/edit', \App\Livewire\Admin\Sucursales\Edit::class)->name('sucursales.edit');
        });

    Route::prefix('empresas')->group(function () {
        Route::get('/', \App\Livewire\Admin\Empresas\Index::class)->name('empresas.index');
        Route::get('/create', \App\Livewire\Admin\Empresas\Create::class)->name('empresas.create');
        Route::get('/{empresa}', \App\Livewire\Admin\Empresas\Show::class)->name('empresas.show');
        Route::get('/{empresa}/edit', \App\Livewire\Admin\Empresas\Edit::class)->name('empresas.edit');
    });
});
