<?php

use App\Livewire\Admin\Permissions\Create as PermissionsCreate;
use App\Livewire\Admin\Permissions\Edit as PermissionsEdit;
use App\Livewire\Admin\Permissions\Index as PermissionsIndex;
use App\Livewire\Admin\Roles\Create as RolesCreate;
use App\Livewire\Admin\Roles\Edit as RolesEdit;
use App\Livewire\Admin\Roles\Index as RolesIndex;
use App\Livewire\Admin\Roles\Show as RolesShow;
use Illuminate\Support\Facades\Route;

// Roles
Route::middleware(['checkAdminPermission:access roles'])->group(function () {
    Route::get('/roles', RolesIndex::class)->name('roles.index');
    Route::get('/roles/crear', RolesCreate::class)->name('roles.create');
    Route::get('/roles/{role}/editar', RolesEdit::class)->name('roles.edit');
    Route::get('/roles/{role}', RolesShow::class)->name('roles.show');
});

// Permisos
Route::middleware(['checkAdminPermission:access permissions'])->group(function () {
    Route::get('/permisos', PermissionsIndex::class)->name('permissions.index');
    Route::get('/permisos/crear', PermissionsCreate::class)->name('permissions.create');
    Route::get('/permisos/{permission}/editar', PermissionsEdit::class)->name('permissions.edit');
});
