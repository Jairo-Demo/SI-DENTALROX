<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\EstadoCita;
use App\Models\Paciente;
use App\Models\Tratamiento;
use App\Models\User;
use App\Services\BitacoraService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Control de Citas Médicas del Sistema (CU10, CU11, CU12).
 * Aplica la regla técnica RN-07 de Sillón Único para evitar cualquier cruce de horario.
 */
class CitaController extends Controller
{
    /**
     * Muestra el formulario para programar una nueva cita (CU10 pasos 1 a 3).
     */
    public function create(Request $request): View
    {
        $pacientePreseleccionado = $request->input('paciente_id')
            ? Paciente::find($request->input('paciente_id'))
            : null;

        $pacientes = Paciente::where('estado', 'activo')
            ->orderBy('apellidos')
            ->orderBy('nombres')
            ->get();

        $odontologos = User::where('estado', 'activo')
            ->whereIn('rol', ['odontologo', 'administrador'])
            ->with('especialidad')
            ->orderBy('apellidos')
            ->get();

        $tratamientos = Tratamiento::disponibles()
            ->with('especialidad')
            ->orderBy('nombre')
            ->get();

        $fechaInicial = $request->input('fecha', now()->toDateString());
        $horaInicial = $request->input('hora', '09:00');

        return view('citas.create', compact(
            'pacientes',
            'odontologos',
            'tratamientos',
            'pacientePreseleccionado',
            'fechaInicial',
            'horaInicial'
        ));
    }

    /**
     * Valida y registra una cita en el sillón dental (CU10 pasos 4 a 7).
     * Aplica RN-07 (Sillón Único), RN-08 (Duración) y RN-09 (Hora Fin).
     */
    public function store(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'paciente_id' => ['required', 'integer', 'exists:pacientes,id'],
            'odontologo_id' => ['required', 'integer', 'exists:users,id'],
            'tratamiento_id' => ['required', 'integer', 'exists:tratamientos,id'],
            'fecha' => ['required', 'date', 'after_or_equal:today'],
            'hora_inicio' => ['required', 'date_format:H:i'],
            'duracion_minutos' => ['required', 'integer', 'min:5', 'max:360'],
            'motivo' => ['required', 'string', 'max:150'],
            'observaciones' => ['nullable', 'string', 'max:500'],
        ], [
            'fecha.after_or_equal' => 'La fecha de la cita no puede ser anterior a hoy.',
            'duracion_minutos.min' => 'La duración mínima de una cita debe ser de 5 minutos.',
            'duracion_minutos.max' => 'La duración no puede superar los 360 minutos (6 horas).',
            'hora_inicio.date_format' => 'El formato de hora debe ser HH:mm.',
            'motivo.required' => 'El motivo de la cita es obligatorio.',
        ]);

        // Validación de hora no pasada si la cita es para el día de hoy
        if ($datos['fecha'] === now()->toDateString() && $datos['hora_inicio'] < now()->format('H:i')) {
            return redirect()->back()->withInput()->withErrors([
                'hora_inicio' => 'La hora de inicio no puede ser anterior al momento actual.',
            ]);
        }

        // RN-09: Hora de fin calculada
        $inicio = Carbon::createFromFormat('H:i', $datos['hora_inicio']);
        $fin = (clone $inicio)->addMinutes((int) $datos['duracion_minutos']);
        $horaInicioStr = $inicio->format('H:i:s');
        $horaFinStr = $fin->format('H:i:s');

        // RN-07: Verificación de Sillón Único
        $estadoProgramada = EstadoCita::where('nombre', EstadoCita::PROGRAMADA)->firstOrFail();

        $conflicto = Cita::where('fecha', $datos['fecha'])
            ->where('estado_cita_id', $estadoProgramada->id)
            ->where(function ($query) use ($horaInicioStr, $horaFinStr) {
                // Hay conflicto si: inicio_nueva < fin_existente AND fin_nueva > inicio_existente
                $query->where('hora_inicio', '<', $horaFinStr)
                      ->where('hora_fin', '>', $horaInicioStr);
            })
            ->with(['paciente', 'odontologo'])
            ->first();

        // Flujo alternativo A2: Franja horaria ocupada
        if ($conflicto) {
            $franjasLibres = $this->calcularFranjasLibres(
                $datos['fecha'],
                (int) $datos['duracion_minutos'],
                $estadoProgramada->id
            );

            return redirect()->back()->withInput()
                ->with('error_conflicto', [
                    'paciente' => "{$conflicto->paciente->nombres} {$conflicto->paciente->apellidos}",
                    'odontologo' => "{$conflicto->odontologo->nombres} {$conflicto->odontologo->apellidos}",
                    'hora_inicio' => $conflicto->horaInicioFormato(),
                    'hora_fin' => $conflicto->horaFinFormato(),
                    'franjas_libres' => $franjasLibres,
                ])
                ->withErrors([
                    'hora_inicio' => 'El sillón dental está ocupado en ese horario por otra cita programada.',
                ]);
        }

        // Guardado de la cita
        $cita = DB::transaction(function () use ($datos, $horaInicioStr, $estadoProgramada, $request) {
            $nuevaCita = Cita::create([
                'paciente_id' => $datos['paciente_id'],
                'odontologo_id' => $datos['odontologo_id'],
                'fecha' => $datos['fecha'],
                'hora_inicio' => $horaInicioStr,
                'duracion_minutos' => $datos['duracion_minutos'],
                'motivo' => $datos['motivo'],
                'estado_cita_id' => $estadoProgramada->id,
                'observaciones' => $datos['observaciones'] ?? null,
                'fecha_registro' => now()->toDateString(),
            ]);

            BitacoraService::registrar(
                BitacoraService::PROGRAMAR_CITA,
                $request->user()->id,
                'citas',
                $nuevaCita->id
            );

            return $nuevaCita;
        });

        return redirect()->route('agenda.index', ['fecha' => $datos['fecha']])
            ->with('exito', "Cita programada exitosamente para las {$inicio->format('H:i')}.");
    }

    /**
     * Muestra la ficha de detalle de una cita (CU11 y CU12).
     */
    public function show($id): View
    {
        $cita = Cita::with(['paciente', 'odontologo.especialidad', 'estado'])->findOrFail($id);
        $estadosDisponibles = EstadoCita::all();

        return view('citas.show', compact('cita', 'estadosDisponibles'));
    }

    /**
     * Reprograma una cita existente conservando el historial (CU11 pasos 3 a 5).
     */
    public function reprogramar(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'cita_id' => ['required', 'integer', 'exists:citas,id'],
            'nueva_fecha' => ['required', 'date', 'after_or_equal:today'],
            'nueva_hora_inicio' => ['required', 'date_format:H:i'],
            'motivo_reprogramacion' => ['required', 'string', 'max:250'],
        ], [
            'nueva_fecha.after_or_equal' => 'La nueva fecha no puede ser anterior a hoy.',
            'motivo_reprogramacion.required' => 'Debe indicar el motivo de la reprogramación.',
        ]);

        $citaOriginal = Cita::with('estado')->findOrFail($datos['cita_id']);

        if (! $citaOriginal->esProgramada()) {
            return redirect()->back()->withErrors([
                'operacion' => 'Solo se pueden reprogramar citas que estén en estado programada.',
            ]);
        }

        // Validación de hora si es hoy
        if ($datos['nueva_fecha'] === now()->toDateString() && $datos['nueva_hora_inicio'] < now()->format('H:i')) {
            return redirect()->back()->withErrors([
                'nueva_hora_inicio' => 'La nueva hora no puede ser anterior al momento actual.',
            ]);
        }

        // Cálculo de hora fin para la nueva fecha
        $inicio = Carbon::createFromFormat('H:i', $datos['nueva_hora_inicio']);
        $fin = (clone $inicio)->addMinutes($citaOriginal->duracion_minutos);
        $horaInicioStr = $inicio->format('H:i:s');
        $horaFinStr = $fin->format('H:i:s');

        $estadoProgramada = EstadoCita::where('nombre', EstadoCita::PROGRAMADA)->firstOrFail();
        $estadoCancelada = EstadoCita::where('nombre', EstadoCita::CANCELADA)->firstOrFail();

        // Verificación RN-07 en la nueva fecha y hora
        $conflicto = Cita::where('fecha', $datos['nueva_fecha'])
            ->where('estado_cita_id', $estadoProgramada->id)
            ->where('id', '!=', $citaOriginal->id)
            ->where(function ($query) use ($horaInicioStr, $horaFinStr) {
                $query->where('hora_inicio', '<', $horaFinStr)
                      ->where('hora_fin', '>', $horaInicioStr);
            })
            ->first();

        if ($conflicto) {
            return redirect()->back()->withErrors([
                'nueva_hora_inicio' => 'La nueva franja horaria solicitada ya se encuentra ocupada por otra cita del sillón.',
            ]);
        }

        DB::transaction(function () use ($citaOriginal, $datos, $horaInicioStr, $estadoProgramada, $estadoCancelada, $request) {
            // 1. Crea la nueva cita programada
            $nuevaCita = Cita::create([
                'paciente_id' => $citaOriginal->paciente_id,
                'odontologo_id' => $citaOriginal->odontologo_id,
                'fecha' => $datos['nueva_fecha'],
                'hora_inicio' => $horaInicioStr,
                'duracion_minutos' => $citaOriginal->duracion_minutos,
                'motivo' => $citaOriginal->motivo,
                'estado_cita_id' => $estadoProgramada->id,
                'observaciones' => "Reprogramada desde la Cita #{$citaOriginal->id}. Motivo: {$datos['motivo_reprogramacion']}",
                'fecha_registro' => now()->toDateString(),
            ]);

            // 2. Cambia la original a cancelada con historial (RF-70)
            $obsOriginal = trim(($citaOriginal->observaciones ?? '')." | Reprogramada a fecha {$datos['nueva_fecha']} {$datos['nueva_hora_inicio']}. Motivo: {$datos['motivo_reprogramacion']} (Nueva Cita #{$nuevaCita->id})");
            $citaOriginal->update([
                'estado_cita_id' => $estadoCancelada->id,
                'observaciones' => $obsOriginal,
            ]);

            BitacoraService::registrar(
                BitacoraService::REPROGRAMAR_CITA,
                $request->user()->id,
                'citas',
                $nuevaCita->id
            );
        });

        return redirect()->route('agenda.index', ['fecha' => $datos['nueva_fecha']])
            ->with('exito', 'Cita reprogramada exitosamente. El horario anterior ha sido liberado.');
    }

    /**
     * Cancela una cita y libera la franja horaria del sillón (CU11 paso 6).
     */
    public function cancelar(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'cita_id' => ['required', 'integer', 'exists:citas,id'],
            'motivo_cancelacion' => ['required', 'string', 'max:250'],
        ], [
            'motivo_cancelacion.required' => 'Debe indicar el motivo de la cancelación de la cita.',
        ]);

        $cita = Cita::with('estado')->findOrFail($datos['cita_id']);

        if (! $cita->esProgramada()) {
            return redirect()->back()->withErrors([
                'operacion' => 'Solo se pueden cancelar citas que se encuentren en estado programada.',
            ]);
        }

        $estadoCancelada = EstadoCita::where('nombre', EstadoCita::CANCELADA)->firstOrFail();

        DB::transaction(function () use ($cita, $datos, $estadoCancelada, $request) {
            $obs = trim(($cita->observaciones ?? '')." | Cancelada. Motivo: {$datos['motivo_cancelacion']}");
            $cita->update([
                'estado_cita_id' => $estadoCancelada->id,
                'observaciones' => $obs,
            ]);

            BitacoraService::registrar(
                BitacoraService::CANCELAR_CITA,
                $request->user()->id,
                'citas',
                $cita->id
            );
        });

        return redirect()->back()->with('exito', 'Cita cancelada correctamente. La franja horaria del sillón fue liberada.');
    }

    /**
     * Actualiza el estado final de una cita (CU12: atendida, no asistida, cancelada).
     */
    public function actualizarEstado(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'cita_id' => ['required', 'integer', 'exists:citas,id'],
            'estado_nombre' => ['required', Rule::in([EstadoCita::ATENDIDA, EstadoCita::NO_ASISTIDA, EstadoCita::CANCELADA])],
            'observaciones' => ['nullable', 'string', 'max:500'],
        ]);

        $cita = Cita::with('estado')->findOrFail($datos['cita_id']);

        if (! $cita->esProgramada()) {
            return redirect()->back()->withErrors([
                'operacion' => 'La cita ya tiene un estado final asignado y no puede modificarse.',
            ]);
        }

        // Regla: «Atendida» o «No asistida» solo pueden marcarse a partir de la fecha de la cita
        if (in_array($datos['estado_nombre'], [EstadoCita::ATENDIDA, EstadoCita::NO_ASISTIDA])) {
            if ($cita->fecha->isFuture()) {
                return redirect()->back()->withErrors([
                    'operacion' => 'No se puede marcar como atendida o no asistida una cita programada para una fecha futura.',
                ]);
            }
        }

        $nuevoEstado = EstadoCita::where('nombre', $datos['estado_nombre'])->firstOrFail();

        DB::transaction(function () use ($cita, $nuevoEstado, $datos, $request) {
            $cita->update([
                'estado_cita_id' => $nuevoEstado->id,
                'observaciones' => $datos['observaciones'] ? trim(($cita->observaciones ?? '')." | {$datos['observaciones']}") : $cita->observaciones,
            ]);

            BitacoraService::registrar(
                BitacoraService::ACTUALIZAR_ESTADO_CITA,
                $request->user()->id,
                'citas',
                $cita->id
            );
        });

        return redirect()->back()->with('exito', "Estado de la cita actualizado a «{$nuevoEstado->nombre}».");
    }

    /**
     * Calcula las franjas horarias libres más cercanas del consultorio para un día dado (RN-07 / A2.2).
     * Horario hábil estándar: 08:00 a 19:00.
     *
     * @return array<int, array{inicio: string, fin: string}>
     */
    private function calcularFranjasLibres(string $fecha, int $duracionMinutos, int $estadoProgramadaId): array
    {
        $citas = Cita::where('fecha', $fecha)
            ->where('estado_cita_id', $estadoProgramadaId)
            ->orderBy('hora_inicio')
            ->get();

        $franjas = [];
        $puntero = Carbon::createFromFormat('H:i', '08:00');
        $cierre = Carbon::createFromFormat('H:i', '19:00');

        foreach ($citas as $c) {
            $inicioCita = Carbon::createFromFormat('H:i', $c->horaInicioFormato());
            $finCita = Carbon::createFromFormat('H:i', $c->horaFinFormato());

            if ($puntero->lt($inicioCita)) {
                $minutosLibres = $puntero->diffInMinutes($inicioCita);
                if ($minutosLibres >= $duracionMinutos) {
                    $franjas[] = [
                        'inicio' => $puntero->format('H:i'),
                        'fin' => (clone $puntero)->addMinutes($duracionMinutos)->format('H:i'),
                    ];
                }
            }

            if ($finCita->gt($puntero)) {
                $puntero = clone $finCita;
            }
        }

        if ($puntero->lt($cierre)) {
            $minutosLibres = $puntero->diffInMinutes($cierre);
            if ($minutosLibres >= $duracionMinutos) {
                $franjas[] = [
                    'inicio' => $puntero->format('H:i'),
                    'fin' => (clone $puntero)->addMinutes($duracionMinutos)->format('H:i'),
                ];
            }
        }

        return array_slice($franjas, 0, 4);
    }
}
