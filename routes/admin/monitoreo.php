<?php

use Illuminate\Support\Facades\Route;

// Monitoreo
Route::prefix('monitoreo')->as('monitoreo.')->group(function () {
    Route::get('/servidor', \App\Livewire\Admin\Monitoreo\Servidor::class)->name('servidor')->middleware('checkAdminPermission:view monitoreo servidor');
    Route::get('/base-datos', \App\Livewire\Admin\Monitoreo\BaseDatos::class)->name('base-datos')->middleware('checkAdminPermission:view monitoreo base-datos');
    Route::get('/estudiantes', \App\Livewire\Admin\Monitoreo\Estudiantes::class)->name('estudiantes')->middleware('checkAdminPermission:view monitoreo estudiantes');
    Route::get('/accesos', \App\Livewire\Admin\Monitoreo\Accesos::class)->name('accesos')->middleware('checkAdminPermission:view monitoreo accesos');
});
