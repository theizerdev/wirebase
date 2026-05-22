<?php

use App\Livewire\Admin\Zonas\Create as ZonasCreate;
use App\Livewire\Admin\Zonas\Edit as ZonasEdit;
use App\Livewire\Admin\Zonas\Index as ZonasIndex;
use Illuminate\Support\Facades\Route;

// Zonas
Route::middleware(['checkAdminPermission:access zonas'])->group(function () {
    Route::get('/zonas', ZonasIndex::class)->name('zonas.index');
    Route::get('/zonas/crear', ZonasCreate::class)->name('zonas.create');
    Route::get('/zonas/{zona}/editar', ZonasEdit::class)->name('zonas.edit');
});
