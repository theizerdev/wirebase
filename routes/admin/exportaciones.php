<?php

use Illuminate\Support\Facades\Route;

// Exportador de Base de Datos
Route::get('/exportar-base-datos', \App\Livewire\Admin\DatabaseExport::class)->name('database-export')->middleware('checkAdminPermission:access database export');
Route::get('/exportar-base-datos/download/{file}', [\App\Http\Controllers\Admin\DatabaseDownloadController::class, 'download'])->name('database-download')->middleware('checkAdminPermission:access database export');
