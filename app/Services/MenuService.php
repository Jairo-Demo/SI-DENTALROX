<?php

namespace App\Services;

use App\Models\User;

/**
 * Servicio de Menú y Paquetes del Sistema (DentalRox).
 * Gestiona la definición y filtrado de los 5 paquetes del sistema.
 */
class MenuService
{
    /**
     * Retorna los paquetes y opciones filtrados por el rol del usuario autenticado.
     *
     * @return array<int, array{codigo: string, nombre: string, corto: string, descripcion: string, icono: string, opciones: array}>
     */
    public static function paquetes(User $usuario): array
    {
        $esAdministrador = $usuario->rol === 'administrador';
        $paquetesVisibles = [];

        foreach (self::todos() as $paquete) {
            $opcionesFiltradas = array_values(array_filter(
                $paquete['opciones'],
                fn ($opcion) => $esAdministrador || ! $opcion['solo_admin']
            ));

            if (! empty($opcionesFiltradas)) {
                $paquete['opciones'] = $opcionesFiltradas;
                $paquetesVisibles[] = $paquete;
            }
        }

        return $paquetesVisibles;
    }

    /**
     * Busca un paquete específico por su código (p1, p2, etc.) validando permisos del usuario.
     */
    public static function buscar(string $codigo, User $usuario): ?array
    {
        $codigoNormalizado = strtoupper(trim($codigo));
        $paquetes = self::paquetes($usuario);

        foreach ($paquetes as $paquete) {
            if (strtoupper($paquete['codigo']) === $codigoNormalizado) {
                return $paquete;
            }
        }

        return null;
    }

    /**
     * Definición completa de los 5 Paquetes del Sistema con iconos y descripciones.
     */
    private static function todos(): array
    {
        return [
            [
                'codigo' => 'P1',
                'nombre' => 'Administración de Usuarios y Seguridad',
                'corto' => 'Usuarios y Seguridad',
                'icono' => '👥',
                'descripcion' => 'Gestión integral de usuarios, asignación de roles, control de accesos y bitácora de auditoría.',
                'opciones' => [
                    self::opcion('Gestionar Usuarios', 'usuarios.listar', 'usuarios.*', 'Registro, modificación y control de cuentas de usuario.', true),
                    self::opcion('Bitácora del Sistema', 'bitacora.listar', 'bitacora.*', 'Registro histórico de accesos y eventos de seguridad.', true),
                ],
            ],
            [
                'codigo' => 'P2',
                'nombre' => 'Catálogos Clínicos y de Servicios',
                'corto' => 'Catálogos Clínicos',
                'icono' => '📋',
                'descripcion' => 'Administración de catálogos base: especialidades, tratamientos, tarifas, diagnósticos y materiales.',
                'opciones' => [
                    self::opcion('Especialidades Odontológicas', 'especialidades.listar', 'especialidades.*', 'Catálogo de ramas de especialidad dental.', true),
                    self::opcion('Catálogo de Tratamientos', null, null, 'Servicios odontológicos disponibles.', true),
                    self::opcion('Tarifas y Aranceles', null, null, 'Precios y costos de los procedimientos.', true),
                    self::opcion('Diagnósticos Clínicos', null, null, 'Catálogo de diagnósticos y afecciones.', true),
                    self::opcion('Materiales e Insumos', null, null, 'Insumos dentales utilizados en tratamientos.', true),
                ],
            ],
            [
                'codigo' => 'P3',
                'nombre' => 'Gestión de Pacientes y Agenda',
                'corto' => 'Pacientes y Agenda',
                'icono' => '📅',
                'descripcion' => 'Registro de pacientes, datos de contacto, agenda de citas y asignación horaria.',
                'opciones' => [
                    self::opcion('Gestión de Pacientes', 'pacientes.listar', 'pacientes.*', 'Registro clínico, datos personales y búsqueda de pacientes.'),
                    self::opcion('Programación de Citas', null, null, 'Agendamiento y control de estado de citas médicas.'),
                    self::opcion('Agenda del Día', null, null, 'Visualización de citas programadas por horario y odontólogo.'),
                ],
            ],
            [
                'codigo' => 'P4',
                'nombre' => 'Atención Clínica y Odontología',
                'corto' => 'Atención Clínica',
                'icono' => '🩺',
                'descripcion' => 'Expedientes clínicos, odontograma digital, planes de tratamiento y sesiones clínicas.',
                'opciones' => [
                    self::opcion('Historia Clínica y Antecedentes', null, null, 'Expediente médico individual y antecedentes patológicos.'),
                    self::opcion('Odontograma Digital', null, null, 'Mapeo gráfico por pieza dental y estado de piezas.'),
                    self::opcion('Planes de Tratamiento', null, null, 'Propuesta de tratamientos y procedimientos requeridos.'),
                    self::opcion('Sesiones de Tratamiento', null, null, 'Evolución y seguimiento de visitas clínicas.'),
                    self::opcion('Estudios Diagnósticos', null, null, 'Radiografías, tomografías e imágenes 3D.'),
                ],
            ],
            [
                'codigo' => 'P5',
                'nombre' => 'Pagos, Seguimiento y Reportes',
                'corto' => 'Pagos y Reportes',
                'icono' => '💳',
                'descripcion' => 'Cobros de tratamientos, controles posteriores a procedimientos, alertas del sistema y reportes.',
                'opciones' => [
                    self::opcion('Gestión de Pagos', null, null, 'Cobros, recibos y métodos de pago.'),
                    self::opcion('Controles y Seguimiento', null, null, 'Revisiones posteriores a intervenciones o implantes.'),
                    self::opcion('Avisos y Notificaciones', null, null, 'Alertas automáticas para odontólogos y pacientes.'),
                    self::opcion('Reportes Estadísticos', null, null, 'Reportes de atención, ingresos y atenciones.'),
                ],
            ],
        ];
    }

    /**
     * Construye una opción de paquete con descripción y permisos.
     */
    private static function opcion(string $nombre, ?string $ruta = null, ?string $activa = null, string $detalle = '', bool $soloAdmin = false): array
    {
        return [
            'nombre' => $nombre,
            'ruta' => $ruta,
            'activa' => $activa,
            'detalle' => $detalle,
            'solo_admin' => $soloAdmin,
        ];
    }
}
