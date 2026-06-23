<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WhatsAppController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


// Asistencia API Routes
Route::get('actividades/activas', [\App\Http\Controllers\Api\AsistenciaController::class, 'activas']);
Route::get('asistencias/estadisticas', [\App\Http\Controllers\Api\AsistenciaController::class, 'estadisticas']);
Route::post('asistencias/registrar', [\App\Http\Controllers\Api\AsistenciaController::class, 'registrar']);
Route::post('asistencias/registrar-nuevo-pastor', [\App\Http\Controllers\Api\AsistenciaController::class, 'registrarNuevoPastor']);