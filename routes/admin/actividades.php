<?php

use App\Livewire\Admin\Actividades\Index as ActividadesIndex;
use Illuminate\Support\Facades\Route;

// Actividades
// Route::middleware(['checkAdminPermission:access actividades'])->group(function () {
    Route::get('/actividades', ActividadesIndex::class)->name('actividades.index');
   
// });
