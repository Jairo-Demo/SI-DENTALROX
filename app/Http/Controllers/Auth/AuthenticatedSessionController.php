<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Controlador para la gestión del Inicio y Cierre de Sesión (MVC).
 * Conecta la vista Blade con el Modelo User y la base de datos.
 */
class AuthenticatedSessionController extends Controller
{
    /**
     * Muestra la vista del formulario de Login (Ruta GET).
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Procesa las credenciales del formulario e inicia la sesión (Ruta POST).
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // 1. Ejecuta la validación y autenticación en la BD (LoginRequest)
        $request->authenticate();

        // 2. Regenera el ID de sesión para prevenir ataques de Session Fixation
        $request->session()->regenerate();

        // 3. Redirige al panel principal (Dashboard)
        return redirect()->intended(route('dashboard'));
    }

    /**
     * Cierra la sesión activa del usuario (Ruta POST).
     */
    public function destroy(Request $request): RedirectResponse
    {
        // 1. Cierra la sesión en el Guard de autenticación
        Auth::logout();

        // 2. Invalida la sesión actual del usuario
        $request->session()->invalidate();

        // 3. Regenera el token CSRF para seguridad en el formulario
        $request->session()->regenerateToken();

        // 4. Redirige al Login con un mensaje flash de éxito
        return redirect()->route('login')->with('exito', 'Sesión cerrada correctamente.');
    }
}
