<?php

use App\Livewire\Admin\Sucursales\Create as SucursalesCreate;
use App\Livewire\Admin\Sucursales\Edit as SucursalesEdit;
use App\Livewire\Admin\Sucursales\Index as SucursalesIndex;
use App\Livewire\Admin\Sucursales\Show as SucursalesShow;
use Illuminate\Support\Facades\Route;

// Sucursales
Route::middleware(['checkAdminPermission:access sucursales'])->group(function () {
    Route::get('/sucursales', SucursalesIndex::class)->name('sucursales.index');
    Route::get('/sucursales/crear', SucursalesCreate::class)->name('sucursales.create');
    Route::get('/sucursales/{sucursal}/editar', SucursalesEdit::class)->name('sucursales.edit');
    Route::get('/sucursales/{sucursal}', SucursalesShow::class)->name('sucursales.show');
});
