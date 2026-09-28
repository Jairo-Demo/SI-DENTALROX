<?php

use App\Http\Controllers\BitacoraController;
use App\Http\Controllers\EspecialidadController;
use App\Http\Controllers\PacienteController;
use App\Http\Controllers\PaqueteController;
use App\Http\Controllers\UsuarioController;
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

// Rutas protegidas con cuenta activa, control de inactividad y contraseña obligatoria
Route::middleware(['auth', 'cuenta.activa', 'inactividad', 'cambio.contrasena'])->group(function () {
    
    // Panel Principal (Dashboard con los 5 paquetes principales)
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Vista detallada de cada Paquete al hacer clic (P1..P5)
    Route::get('/paquetes/{codigo}', [PaqueteController::class, 'show'])->name('paquetes.mostrar');

    // CU04 Gestionar usuarios, CU05 Asignar rol y CU06 Restablecer acceso (Solo Administrador)
    Route::prefix('usuarios')->middleware('rol:administrador')
        ->controller(UsuarioController::class)->group(function () {
            Route::get('/', 'index')->name('usuarios.listar');
            Route::post('/', 'store')->name('usuarios.guardar');
            Route::post('/update', 'update')->name('usuarios.modificar');
            Route::post('/delete', 'delete')->name('usuarios.eliminar');
            Route::post('/activar', 'activar')->name('usuarios.habilitar');
            Route::post('/rol', 'asignarRol')->name('usuarios.asignarRol');
            Route::post('/restablecer', 'restablecerAcceso')->name('usuarios.restablecer');
            Route::get('/{id}', 'show')->whereNumber('id')->name('usuarios.mostrar');
        });

    // CU07 Consultar bitácora del sistema (Solo Administrador - Solo Lectura)
    Route::prefix('bitacora')->middleware('rol:administrador')
        ->controller(BitacoraController::class)->group(function () {
            Route::get('/', 'index')->name('bitacora.listar');
            Route::get('/{id}', 'show')->whereNumber('id')->name('bitacora.mostrar');
        });

    // Módulo: Catálogo de Especialidades (Paquete 2 - Solo Administrador)
    Route::prefix('especialidades')->middleware('rol:administrador')
        ->controller(EspecialidadController::class)->group(function () {
            Route::get('/', 'index')->name('especialidades.listar');
            Route::post('/', 'store')->name('especialidades.guardar');
            Route::post('/update', 'update')->name('especialidades.modificar');
            Route::post('/delete', 'delete')->name('especialidades.eliminar');
            Route::get('/{id}', 'show')->whereNumber('id')->name('especialidades.mostrar');
        });

    // CU08 Gestionar pacientes y CU09 Buscar paciente (Paquete 3 - Ambos roles)
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
