<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Admin\Estados\Index;
use App\Livewire\Admin\Estados\Create;
use App\Livewire\Admin\Estados\Edit;

Route::middleware(['auth'])->group(function () {
    Route::get('/estados', Index::class)->name('estados.index');
    Route::get('/estados/crear', Create::class)->name('estados.create');
    Route::get('/estados/{id}/editar', Edit::class)->name('estados.edit');
});
