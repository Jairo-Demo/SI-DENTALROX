<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Form Request para validar y autenticar el inicio de sesión de DentalRox.
 * Maneja el campo 'usuario' (en lugar de email) y la protección contra ataques de fuerza bruta.
 */
class LoginRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado a realizar esta petición.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Reglas de validación para los campos del formulario.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'usuario' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Mensajes de error personalizados en español.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'usuario.required' => 'El campo Usuario es obligatorio.',
            'password.required' => 'El campo Contraseña es obligatorio.',
        ];
    }

    /**
     * Intenta autenticar las credenciales contra la base de datos MySQL.
     *
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        // 1. Verifica si la cuenta o IP está bloqueada por exceso de intentos
        $this->ensureIsNotRateLimited();

        // 2. Intento de autenticación usando las columnas 'usuario' y 'password'
        $credenciales = [
            'usuario' => $this->input('usuario'),
            'password' => $this->input('password'),
        ];

        if (! Auth::attempt($credenciales, $this->boolean('remember'))) {
            // Incrementa contador de intentos fallidos
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'usuario' => 'Usuario o contraseña incorrectos.',
            ]);
        }

        // 3. Limpia el contador de intentos al ingresar con éxito
        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Asegura que la petición no haya excedido el límite de intentos (Rate Limiting).
     *
     * @throws ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        // Límite de 5 intentos por minuto
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $segundos = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'usuario' => "Demasiados intentos de acceso. Por favor intente de nuevo en {$segundos} segundos.",
        ]);
    }

    /**
     * Clave única para el control de intentos (usuario + IP de origen).
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->input('usuario')).'|'.$this->ip());
    }
}
