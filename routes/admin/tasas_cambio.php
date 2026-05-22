<?php

use App\Livewire\Admin\ExchangeRates;
use App\Livewire\Admin\ExchangeRateConfig\Index as ExchangeRateConfigIndex;
use Illuminate\Support\Facades\Route;

// Tasas de Cambio
Route::get('/tasas-cambio', ExchangeRates::class)->name('exchange-rates')->middleware('checkAdminPermission:view exchange-rates');
Route::get('/tasas-cambio/configuracion', ExchangeRateConfigIndex::class)->name('exchange-rate-config.index')->middleware('checkAdminPermission:edit exchange-rates');
