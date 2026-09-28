<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rutas Web Principales (DentalRox)
|--------------------------------------------------------------------------
| Flujo MVC: Ruta -> Controlador -> Vista
*/

// Redirección inicial: Si entra a la raíz '/', lo enviamos al login
Route::get('/', function () {
    return redirect()->route('login');
});

// Panel Principal (Dashboard protegido por autenticación)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

// Importar rutas de autenticación (Login / Logout)
require __DIR__.'/auth.php';
