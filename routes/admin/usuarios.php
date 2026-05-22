<?php

use App\Livewire\Admin\Users\Create as UsersCreate;
use App\Livewire\Admin\Users\Edit as UsersEdit;
use App\Livewire\Admin\Users\Index as UsersIndex;
use Illuminate\Support\Facades\Route;

// Usuarios
Route::middleware(['checkAdminPermission:access users'])->group(function () {
    Route::get('/usuarios', UsersIndex::class)->name('users.index');
    Route::get('/usuarios/crear', UsersCreate::class)->name('users.create');
    Route::get('/usuarios/{user}/editar', UsersEdit::class)->name('users.edit');
});

// Perfil de usuario (Acceso para todos los autenticados, sin permiso específico requerido)
Route::prefix('profile')->group(function () {
    Route::get('/', \App\Livewire\Admin\Users\Profile\Index::class)->name('users.profile');
    Route::get('/{user_id}/password', \App\Livewire\Admin\Users\Profile\ChangePassword::class)->name('users.password');
    Route::get('/{user_id}/history', \App\Livewire\Admin\Users\Profile\HistoryUser::class)->name('users.history');
});
