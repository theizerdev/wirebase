<?php

use App\Livewire\Admin\Empresas\Create as EmpresasCreate;
use App\Livewire\Admin\Empresas\Edit as EmpresasEdit;
use App\Livewire\Admin\Empresas\Index as EmpresasIndex;
use Illuminate\Support\Facades\Route;

// Empresas
Route::middleware(['checkAdminPermission:access empresas'])->group(function () {
    Route::get('/empresas', EmpresasIndex::class)->name('empresas.index');
    Route::get('/empresas/crear', EmpresasCreate::class)->name('empresas.create');
    Route::get('/empresas/{empresa}/editar', EmpresasEdit::class)->name('empresas.edit');
});
