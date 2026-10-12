<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\EstadoCita;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * CU13 Consultar agenda del sillón único (RF-72).
 * Muestra la ocupación horaria diaria y semanal con franjas ocupadas y disponibles.
 */
class AgendaController extends Controller
{
    /**
     * Visualización diaria o semanal de la agenda de citas.
     */
    public function index(Request $request): View
    {
        $request->validate([
            'fecha' => ['nullable', 'date'],
            'vista' => ['nullable', 'in:diaria,semanal'],
            'odontologo_id' => ['nullable', 'integer', 'exists:users,id'],
            'estado_id' => ['nullable', 'integer', 'exists:estados_cita,id'],
        ]);

        $fechaSeleccionada = $request->input('fecha')
            ? Carbon::parse($request->input('fecha'))
            : now()->startOfDay();

        $vista = $request->input('vista', 'diaria');
        $odontologoId = $request->input('odontologo_id');
        $estadoId = $request->input('estado_id');

        $odontologos = User::where('estado', 'activo')
            ->whereIn('rol', ['odontologo', 'administrador'])
            ->orderBy('apellidos')
            ->get();

        $estados = EstadoCita::all();

        if ($vista === 'semanal') {
            // Semana de Lunes a Sábado
            $inicioSemana = (clone $fechaSeleccionada)->startOfWeek(Carbon::MONDAY);
            $finSemana = (clone $inicioSemana)->addDays(5); // Lunes a Sábado

            $diasSemana = [];
            for ($i = 0; $i < 6; $i++) {
                $dia = (clone $inicioSemana)->addDays($i);
                $diasSemana[] = [
                    'fecha' => $dia->toDateString(),
                    'dia_nombre' => ucfirst($dia->locale('es')->isoFormat('dddd')),
                    'dia_numero' => $dia->format('d/m'),
                    'es_hoy' => $dia->isToday(),
                ];
            }

            $citas = Cita::whereBetween('fecha', [$inicioSemana->toDateString(), $finSemana->toDateString()])
                ->when($odontologoId, fn ($q) => $q->where('odontologo_id', $odontologoId))
                ->when($estadoId, fn ($q) => $q->where('estado_cita_id', $estadoId))
                ->with(['paciente', 'odontologo', 'estado'])
                ->orderBy('hora_inicio')
                ->get()
                ->groupBy(fn ($cita) => $cita->fecha->toDateString());

            return view('agenda.index', compact(
                'vista',
                'fechaSeleccionada',
                'inicioSemana',
                'finSemana',
                'diasSemana',
                'citas',
                'odontologos',
                'estados',
                'odontologoId',
                'estadoId'
            ));
        }

        // Vista Diaria
        $citasDelDia = Cita::where('fecha', $fechaSeleccionada->toDateString())
            ->when($odontologoId, fn ($q) => $q->where('odontologo_id', $odontologoId))
            ->when($estadoId, fn ($q) => $q->where('estado_cita_id', $estadoId))
            ->with(['paciente', 'odontologo.especialidad', 'estado'])
            ->orderBy('hora_inicio')
            ->get();

        // Generación de bloques horarios de 08:00 a 19:00
        $bloquesHorarios = [];
        $horaActual = Carbon::createFromFormat('H:i', '08:00');
        $horaFinDia = Carbon::createFromFormat('H:i', '19:00');

        while ($horaActual->lt($horaFinDia)) {
            $horaStr = $horaActual->format('H:i');
            $siguienteHora = (clone $horaActual)->addMinutes(60)->format('H:i');

            // Citas que caen o inician en esta hora
            $citasEnBloque = $citasDelDia->filter(function ($cita) use ($horaStr, $siguienteHora) {
                return ($cita->horaInicioFormato() >= $horaStr && $cita->horaInicioFormato() < $siguienteHora);
            });

            $bloquesHorarios[] = [
                'hora' => $horaStr,
                'hora_fin_bloque' => $siguienteHora,
                'citas' => $citasEnBloque,
            ];

            $horaActual->addMinutes(60);
        }

        return view('agenda.index', compact(
            'vista',
            'fechaSeleccionada',
            'citasDelDia',
            'bloquesHorarios',
            'odontologos',
            'estados',
            'odontologoId',
            'estadoId'
        ));
    }
}
