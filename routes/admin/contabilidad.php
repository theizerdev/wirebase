<?php

use Illuminate\Support\Facades\Route;

// Contabilidad
Route::prefix('contabilidad')->as('contabilidad.')->middleware(['checkAdminPermission:access contabilidad'])->group(function () {
    Route::get('/plan-cuentas', \App\Livewire\Admin\Contabilidad\PlanCuentas::class)->name('plan-cuentas');
    Route::get('/asientos', \App\Livewire\Admin\Contabilidad\Asientos::class)->name('asientos');
    Route::get('/balance-comprobacion', \App\Livewire\Admin\Contabilidad\BalanceComprobacion::class)->name('balance-comprobacion');
    Route::get('/balance-general', \App\Livewire\Admin\Contabilidad\BalanceGeneral::class)->name('balance-general');
    Route::get('/estado-resultados', \App\Livewire\Admin\Contabilidad\EstadoResultados::class)->name('estado-resultados');
    Route::get('/libro-mayor', \App\Livewire\Admin\Contabilidad\LibroMayor::class)->name('libro-mayor');
    Route::get('/libro-diario', \App\Livewire\Admin\Contabilidad\LibroDiario::class)->name('libro-diario');
    Route::get('/conciliacion-bancaria', \App\Livewire\Admin\Contabilidad\ConciliacionBancaria::class)->name('conciliacion-bancaria');

    // Exportaciones PDF
    Route::get('/balance-comprobacion/pdf', [\App\Http\Controllers\Admin\ContabilidadPdfController::class, 'balanceComprobacion'])->name('balance-comprobacion.pdf');
    Route::get('/balance-general/pdf', [\App\Http\Controllers\Admin\ContabilidadPdfController::class, 'balanceGeneral'])->name('balance-general.pdf');
    Route::get('/estado-resultados/pdf', [\App\Http\Controllers\Admin\ContabilidadPdfController::class, 'estadoResultados'])->name('estado-resultados.pdf');
    Route::get('/libro-mayor/pdf', [\App\Http\Controllers\Admin\ContabilidadPdfController::class, 'libroMayor'])->name('libro-mayor.pdf');
    Route::get('/libro-diario/pdf', [\App\Http\Controllers\Admin\ContabilidadPdfController::class, 'libroDiario'])->name('libro-diario.pdf');
    
    // Exportaciones Excel
    Route::get('/libro-diario/excel', [\App\Http\Controllers\Admin\ContabilidadExcelController::class, 'libroDiario'])->name('libro-diario.excel');
    Route::get('/libro-mayor/excel', [\App\Http\Controllers\Admin\ContabilidadExcelController::class, 'libroMayor'])->name('libro-mayor.excel');
    Route::get('/balance-comprobacion/excel', [\App\Http\Controllers\Admin\ContabilidadExcelController::class, 'balanceComprobacion'])->name('balance-comprobacion.excel');
    
    // Otros módulos contables
    Route::get('/cierre-contable', \App\Livewire\Admin\Contabilidad\CierreContable::class)->name('cierre-contable');
    Route::get('/honorarios-medicos', \App\Livewire\Admin\Contabilidad\HonorariosMedicos::class)->name('honorarios-medicos');
});
