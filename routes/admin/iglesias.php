<?php

use Illuminate\Support\Facades\Route;

use App\Livewire\Admin\Iglesias\Index as IglesiasIndex;
use App\Livewire\Admin\Iglesias\Create as IglesiasCreate;
use App\Livewire\Admin\Iglesias\Edit as IglesiasEdit;
use App\Livewire\Admin\Iglesias\Show as IglesiasShow;



// Iglesias
Route::get('/iglesias', IglesiasIndex::class)->name('iglesias.index');
Route::get('/iglesias/crear', IglesiasCreate::class)->name('iglesias.create');
Route::get('/iglesias/{iglesia}/editar', IglesiasEdit::class)->name('iglesias.edit');
Route::get('/iglesias/{iglesia}', IglesiasShow::class)->name('iglesias.show');
Route::get('/iglesia/mapa-distribucion', \App\Livewire\Admin\Iglesias\MapaDistribucion::class)->name('iglesias.mapa-distribucion');
Route::get('/iglesia/mapa-distribucion/geojson', [\App\Livewire\Admin\Iglesias\MapaDistribucion::class, 'getGeoJsonData'])->name('iglesias.mapa-distribucion.geojson');

// Inventario - Iglesia
Route::get('/extension/{iglesiaId}/inventario/crear', \App\Livewire\Admin\Iglesias\Inventario\Create::class)->name('iglesias.inventario.create');
Route::get('/extension/{iglesiaId}/inventario/{itemId}/editar', \App\Livewire\Admin\Iglesias\Inventario\Edit::class)->name('iglesias.inventario.edit');
Route::get('/extension/{iglesiaId}/inventario/', \App\Livewire\Admin\Iglesias\Inventario\Index::class)->name('iglesias.inventario.index');

// Finanzas - Iglesia
Route::get('/extension/{iglesiaId}/finanzas/', \App\Livewire\Admin\Iglesias\Finanzas\Index::class)->name('iglesias.finanzas.index');
Route::get('/extension/{iglesiaId}/finanzas/crear', \App\Livewire\Admin\Iglesias\Finanzas\Create::class)->name('iglesias.finanzas.create');
