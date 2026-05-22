<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Admin\Ciudades\Index;
use App\Livewire\Admin\Ciudades\Create;
use App\Livewire\Admin\Ciudades\Edit;

Route::middleware(['auth'])->group(function () {
    Route::get('/ciudades', Index::class)->name('ciudades.index');
    Route::get('/ciudades/crear', Create::class)->name('ciudades.create');
    Route::get('/ciudades/{id}/editar', Edit::class)->name('ciudades.edit');
});
