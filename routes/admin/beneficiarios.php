<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Admin\Beneficiarios\Index;
use App\Livewire\Admin\Beneficiarios\Create;
use App\Livewire\Admin\Beneficiarios\Edit;
use App\Livewire\Admin\Beneficiarios\Show;

Route::prefix('beneficiarios')->middleware(['auth'])->name('beneficiarios.')->group(function () {
    Route::get('/', Index::class)->name('index');
    Route::get('/create', Create::class)->name('create');
    Route::get('/{beneficiario}/edit', Edit::class)->name('edit');
    Route::get('/{beneficiario}', Show::class)->name('show');
});