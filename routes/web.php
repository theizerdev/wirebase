<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CitaConfirmationController;
use App\Livewire\Dashboard;
use App\Livewire\Auth\TwoFactorLogin;
use App\Livewire\SuperAdmin\Dashboard as SuperAdminDashboard;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;


Route::get('/lang/{locale}', function ($locale) {
    if (in_array($locale, ['es', 'en'])) {
        Session::put('locale', $locale);
    }
    return redirect()->back();
})->name('lang.switch');

Route::get('/', function () {
   if (\Auth::check() && \Auth::user()->id == 1) {
      return redirect()->to('superadmin/dashboard');
   } elseif (\Auth::check()) {
      // Verificar si es médico
      
      // Verificar si es administrador
      if (\Auth::user()->hasRole('Administrador')) {
         return redirect()->to('admin/dashboard');
      }
      // Verificar si es recepcion
      if (\Auth::user()->hasRole('Recepcion')) {
         return redirect()->route('admin.recepcion.dashboard');
      }
   }
   return redirect()->to('admin/dashboard');
})->middleware('auth');

// Include auth routes
require __DIR__.'/auth.php';

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


