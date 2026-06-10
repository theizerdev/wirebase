<?php

use Illuminate\Support\Facades\Route;

// Guest routes (login, register, password reset)
Route::middleware('guest')->group(function () {
    Route::get('login', \App\Livewire\Auth\Login::class)->name('login');
    Route::get('register', \App\Livewire\Auth\Register::class)->name('register');
    Route::get('password/reset', \App\Livewire\Auth\ForgotPassword::class)->name('password.request');
    Route::get('password/reset/{token}', \App\Livewire\Auth\ResetPassword::class)->name('password.reset');
    Route::get('password/cambiar/{token}', \App\Livewire\Auth\ResetPassword::class)->name('password.reset.token');
});

// Authenticated verification routes
Route::middleware('auth')->group(function () {
    Route::get('verify-email', \App\Livewire\Auth\VerifyCode::class)->name('verification.notice');
    Route::get('verify-email/{id}/{hash}', function (string $id, string $hash) {
        // Esta ruta se usa para la verificación real del correo electrónico
        // Pero como estamos usando Livewire, simplemente redirigimos al componente Verify
        return redirect()->route('verification.notice');
    })->middleware(['signed', 'throttle:6,1'])->name('verification.verify');
});

// Logout route
Route::post('logout', function () {
    try {
        $user = auth()->user();
        
        // Log logout event if user exists
        if ($user) {
            \Log::info('User logging out', [
                'user_id' => $user->id,
                'email' => $user->email,
                'ip' => request()->ip()
            ]);
        }
        
        auth()->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        
        return redirect()->route('login')->with('status', 'Sesión cerrada exitosamente.');
    } catch (\Exception $e) {
        \Log::error('Logout error: ' . $e->getMessage());
        return redirect()->route('login');
    }
})->name('logout');

// Two-factor authentication route
Route::get('/two-factor-login', \App\Livewire\Auth\TwoFactorLogin::class)->name('two-factor.login');
