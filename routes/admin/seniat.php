<?php

use Illuminate\Support\Facades\Route;

// Libro de Ventas SENIAT
Route::prefix('seniat')->as('seniat.')->middleware(['checkAdminPermission:access pagos'])->group(function () {
    Route::get('/libro-ventas', \App\Livewire\Admin\Seniat\LibroVentas::class)->name('libro-ventas');
    Route::get('/libro-compras', \App\Livewire\Admin\Seniat\LibroCompras::class)->name('libro-compras');
    
    // Exportaciones
    Route::get('/libro-ventas/txt', [\App\Http\Controllers\Admin\LibroVentasController::class, 'exportTxt'])->name('libro-ventas.export-txt');
    Route::get('/libro-ventas/excel', [\App\Http\Controllers\Admin\ContabilidadExcelController::class, 'libroVentas'])->name('libro-ventas.excel');
    Route::get('/libro-compras/excel', [\App\Http\Controllers\Admin\ContabilidadExcelController::class, 'libroCompras'])->name('libro-compras.excel');
});
