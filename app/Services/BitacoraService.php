<?php

namespace App\Services;

use App\Models\Bitacora;

/**
 * Servicio central para registrar acciones en la bitácora del sistema (Auditoría).
 * Se utiliza en todos los casos de uso para mantener trazabilidad de operaciones.
 */
class BitacoraService
{
    // Acciones de Autenticación
    public const INICIO_SESION_EXITOSO = 'INICIO_SESION_EXITOSO';
    public const INTENTO_LOGIN_DATOS_INVALIDOS = 'INTENTO_LOGIN_DATOS_INVALIDOS';
    public const LOGIN_FALLIDO_USUARIO_INEXISTENTE = 'LOGIN_FALLIDO_USUARIO_INEXISTENTE';
    public const LOGIN_FALLIDO_CUENTA_INACTIVA = 'LOGIN_FALLIDO_CUENTA_INACTIVA';
    public const LOGIN_FALLIDO_CONTRASENA = 'LOGIN_FALLIDO_CONTRASENA';
    public const CUENTA_BLOQUEADA = 'CUENTA_BLOQUEADA';
    public const LOGIN_RECHAZADO_CUENTA_BLOQUEADA = 'LOGIN_RECHAZADO_CUENTA_BLOQUEADA';
    public const CIERRE_SESION = 'CIERRE_SESION';
    public const CIERRE_SESION_INACTIVIDAD = 'CIERRE_SESION_INACTIVIDAD';
    public const CAMBIO_CONTRASENA = 'CAMBIO_CONTRASENA';

    // Acciones de Usuarios
    public const CREAR_USUARIO = 'CREAR_USUARIO';
    public const MODIFICAR_USUARIO = 'MODIFICAR_USUARIO';
    public const DESACTIVAR_USUARIO = 'DESACTIVAR_USUARIO';
    public const ACTIVAR_USUARIO = 'ACTIVAR_USUARIO';
    public const CAMBIO_ROL = 'CAMBIO_ROL';
    public const RESTABLECER_ACCESO = 'RESTABLECER_ACCESO';

    // Acciones de Especialidades
    public const CREAR_ESPECIALIDAD = 'CREAR_ESPECIALIDAD';
    public const MODIFICAR_ESPECIALIDAD = 'MODIFICAR_ESPECIALIDAD';
    public const ELIMINAR_ESPECIALIDAD = 'ELIMINAR_ESPECIALIDAD';

    // Acciones de Pacientes
    public const CREAR_PACIENTE = 'CREAR_PACIENTE';
    public const MODIFICAR_PACIENTE = 'MODIFICAR_PACIENTE';
    public const INHABILITAR_PACIENTE = 'INHABILITAR_PACIENTE';
    public const ACTIVAR_PACIENTE = 'ACTIVAR_PACIENTE';

    /**
     * Retorna la lista de todas las constantes de acción.
     *
     * @return array<int, string>
     */
    public static function acciones(): array
    {
        return array_values((new \ReflectionClass(self::class))->getConstants());
    }

    /**
     * Registra un nuevo evento en la bitácora.
     */
    public static function registrar(
        string $accion,
        ?int $usuarioId = null,
        ?string $tablaAfectada = null,
        ?int $registroId = null,
        ?string $usuarioDigitado = null
    ): Bitacora {
        return Bitacora::create([
            'usuario_id' => $usuarioId ?? auth()->id(),
            'usuario_digitado' => $usuarioDigitado,
            'accion' => $accion,
            'tabla_afectada' => $tablaAfectada,
            'registro_id' => $registroId,
            'fecha_hora' => now(),
            'ip' => request()->ip(),
        ]);
    }
}
