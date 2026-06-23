<?php

use App\Livewire\Admin\Asistencias\Index as AsistenciasIndex;
use Illuminate\Support\Facades\Route;

Route::get('/asistencias', AsistenciasIndex::class)->name('asistencias.index');
