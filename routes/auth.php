<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\PasswordController;
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
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.ingresar');
});

// Rutas para usuarios autenticados
Route::middleware('auth')->group(function () {
    // Cerrar sesión
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    // CU03 Cambiar contraseña
    Route::controller(PasswordController::class)->group(function () {
        Route::get('/contrasena/cambiar', 'edit')->name('contrasena.cambiar');
        Route::post('/contrasena/cambiar', 'update')->name('contrasena.guardar');
    });
});

