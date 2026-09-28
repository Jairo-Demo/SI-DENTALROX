<?php

use App\Http\Controllers\EspecialidadController;
use App\Http\Controllers\PacienteController;
use App\Http\Controllers\PaqueteController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rutas Web Principales (DentalRox)
|--------------------------------------------------------------------------
| Flujo MVC: Ruta -> Controlador -> Vista
| Estándar de la materia: Solo verbos GET (mostrar) y POST (acciones con ID oculto).
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

    // Vista detallada de cada Paquete al hacer clic (P1..P5)
    Route::get('/paquetes/{codigo}', [PaqueteController::class, 'show'])->name('paquetes.mostrar');

    // Módulo: Catálogo de Especialidades (Paquete 2)
    Route::prefix('especialidades')->controller(EspecialidadController::class)->group(function () {
        Route::get('/', 'index')->name('especialidades.listar');
        Route::post('/', 'store')->name('especialidades.guardar');
        Route::post('/update', 'update')->name('especialidades.modificar');
        Route::post('/delete', 'delete')->name('especialidades.eliminar');
        Route::get('/{id}', 'show')->whereNumber('id')->name('especialidades.mostrar');
    });

    // Módulo: Gestión de Pacientes (Paquete 3)
    Route::prefix('pacientes')->controller(PacienteController::class)->group(function () {
        Route::get('/', 'index')->name('pacientes.listar');
        Route::post('/', 'store')->name('pacientes.guardar');
        Route::post('/update', 'update')->name('pacientes.modificar');
        Route::post('/delete', 'delete')->name('pacientes.eliminar');
        Route::post('/activar', 'activar')->name('pacientes.habilitar');
        Route::get('/{id}', 'show')->whereNumber('id')->name('pacientes.mostrar');
    });

});

// Importar rutas de autenticación (Login / Logout)
require __DIR__.'/auth.php';
