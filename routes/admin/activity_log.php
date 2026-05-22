<?php

use Illuminate\Support\Facades\Route;

// Registro de Actividad
Route::get('/activity-log', \App\Livewire\Admin\ActivityLog::class)->name('activity-log')->middleware('checkAdminPermission:access activity log');
