<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rutas de Autenticación (DentalRox)
|--------------------------------------------------------------------------
| Manejo exclusivo de inicio y cierre de sesión con verbos GET y POST.
*/

// Rutas para visitantes (sin sesión activa)
Route::middleware('guest')->group(function () {
    // Mostrar formulario de inicio de sesión
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');

    // Procesar formulario de inicio de sesión
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
});

// Rutas para usuarios autenticados
Route::middleware('auth')->group(function () {
    // Cerrar sesión
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});
