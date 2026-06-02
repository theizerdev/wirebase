<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Admin\Responsables\Index;
use App\Livewire\Admin\Responsables\Create;
use App\Livewire\Admin\Responsables\Edit;
use App\Livewire\Admin\Responsables\Show;

Route::prefix('responsables')->middleware(['auth'])->name('responsables.')->group(function () {
    Route::get('/', Index::class)->name('index');
    Route::get('/create', Create::class)->name('create');
    Route::get('/{responsable}/edit', Edit::class)->name('edit');
    Route::get('/{responsable}', Show::class)->name('show');
});