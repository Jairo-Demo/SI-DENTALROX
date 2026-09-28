<?php

use App\Http\Controllers\PaqueteController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rutas Web Principales (DentalRox)
|--------------------------------------------------------------------------
| Flujo MVC: Ruta -> Controlador -> Vista
*/

// Redirección inicial: Si entra a la raíz '/', enviamos al login
Route::get('/', function () {
    return redirect()->route('login');
});

// Rutas protegidas por autenticación
Route::middleware(['auth'])->group(function () {
    // Panel Principal (Dashboard con los 5 paquetes principales)
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Vista detallada de cada Paquete al hacer clic (P1, P2, P3, P4, P5)
    Route::get('/paquetes/{codigo}', [PaqueteController::class, 'show'])->name('paquetes.mostrar');
});

// Importar rutas de autenticación (Login / Logout)
require __DIR__.'/auth.php';
