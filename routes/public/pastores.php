<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Admin\Pastores\ConfigurarSeguridadPastor;

// Rutas públicas para autorización de pastores
Route::get('/pastor/autorizar/{token}', \App\Livewire\Public\PastorAutorizacion::class)
    ->name('pastor.autorizar');


Route::get('/pastor/{pastor}/configurar-seguridad', \App\Livewire\Admin\Pastores\ConfigurarSeguridadPastor::class)->name('pastor.configurar-seguridad');


Route::get('/busqueda', App\Livewire\Public\Pastores\Busqueda::class)->name('public.pastores.busqueda');
Route::get('/pastor/registrar', \App\Livewire\Public\Pastores\Registrar::class)->name('public.pastores.registrar');
Route::get('/pastor/{pastor}/actualizar', \App\Livewire\Public\Pastores\Editar::class)
    ->middleware('verify.pastor.security')
    ->name('public.pastores.editar');
Route::get('/pastor/{pastor}/completado', \App\Livewire\Public\Completado::class)->name('public.completado');
Route::get('/pastor/{pastor}/iglesia/crear', \App\Livewire\Public\Iglesias\Editar::class)->name('public.iglesias.crear');
Route::get('/pastor/{pastor}/iglesia/{iglesia}/actualizar', \App\Livewire\Public\Iglesias\Editar::class)->name('public.iglesias.editar');
Route::get('/pastor/{pastor}/iglesia/{iglesia}/inventario', \App\Livewire\Public\Iglesias\InventarioManager::class)->name('public.iglesias.inventario');