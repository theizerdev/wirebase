<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Admin\CasasAlimentacion\Index;
use App\Livewire\Admin\CasasAlimentacion\Create;
use App\Livewire\Admin\CasasAlimentacion\Edit;
use App\Livewire\Admin\CasasAlimentacion\Show;

Route::prefix('casas-alimentacion')->middleware(['auth'])->name('casas_alimentacion.')->group(function () {
    Route::get('/', Index::class)->name('index');
    Route::get('/create', Create::class)->name('create');
    Route::get('/{casa}', Show::class)->name('show');
    Route::get('/{casa}/edit', Edit::class)->name('edit');
});
