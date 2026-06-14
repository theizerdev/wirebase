<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CitaConfirmationController;
use App\Livewire\Dashboard;
use App\Livewire\Auth\TwoFactorLogin;
use App\Livewire\SuperAdmin\Dashboard as SuperAdminDashboard;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;



Route::get('/', function () {
         return redirect()->route('public.pastores.busqueda');
});
// Include auth routes
require __DIR__.'/auth.php';
require __DIR__.'/public/pastores.php';



// Super Admin routes
Route::group(['prefix' => 'superadmin', 'as' => 'superadmin.', 'middleware' => ['auth', 'verified']], function () {
    Route::get('/dashboard', SuperAdminDashboard::class)->name('dashboard');
});

// Admin routes
Route::group(['prefix' => 'admin', 'as' => 'admin.'], function () {
   Route::middleware(['auth', 'verified'])->group(function () {
        Route::get('/dashboard', \App\Livewire\Admin\Dashboard::class)->name('dashboard');
        

        require __DIR__.'/admin.php';
   });
});


