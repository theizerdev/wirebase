<?php

use App\Livewire\Admin\ActiveSessions;
use Illuminate\Support\Facades\Route;

// Sesiones activas
Route::get('/active-sessions', ActiveSessions::class)->name('active-sessions.index')->middleware('checkAdminPermission:view active sessions');
