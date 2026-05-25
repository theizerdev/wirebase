<?php

use Illuminate\Support\Facades\Route;



// Componentes para pastores
use App\Livewire\Admin\Pastores\Index as PastoresIndex;
use App\Livewire\Admin\Pastores\Create as PastoresCreate;
use App\Livewire\Admin\Pastores\Edit as PastoresEdit;
use App\Livewire\Admin\Pastores\Show as PastoresShow;



// Pastores
Route::get('/pastores', PastoresIndex::class)->name('pastores.index');
Route::get('/pastores/crear', PastoresCreate::class)->name('pastores.create');
Route::get('/pastores/{pastor}/editar', PastoresEdit::class)->name('pastores.edit');
Route::get('/pastores/{pastor}', PastoresShow::class)->name('pastores.show');
Route::get('/pastores/{pastor}/planilla', [App\Http\Controllers\Admin\PastorPlanillaController::class, 'planilla'])->name('pastores.planilla');
