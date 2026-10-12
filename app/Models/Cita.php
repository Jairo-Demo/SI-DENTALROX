<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cita extends Model
{
    /**
     * Nombre de la tabla.
     */
    protected $table = 'citas';

    /**
     * La tabla no tiene columnas created_at / updated_at.
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * hora_fin se excluye: es una columna generada por la base de datos.
     *
     * @var list<string>
     */
    protected $fillable = [
        'paciente_id',
        'odontologo_id',
        'tratamiento_aplicado_id',
        'fecha',
        'hora_inicio',
        'duracion_minutos',
        'motivo',
        'estado_cita_id',
        'observaciones',
        'fecha_registro',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'fecha' => 'date',
            'fecha_registro' => 'date',
            'duracion_minutos' => 'integer',
        ];
    }

    /**
     * Paciente asignado a la cita médica.
     */
    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class, 'paciente_id');
    }

    /**
     * Odontólogo responsable que atenderá la cita.
     */
    public function odontologo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'odontologo_id');
    }

    /**
     * Estado actual de la cita (programada, atendida, cancelada, no asistida).
     */
    public function estado(): BelongsTo
    {
        return $this->belongsTo(EstadoCita::class, 'estado_cita_id');
    }

    /**
     * Tratamiento aplicado (Ciclo 4, opcional/nullable en Ciclo 2).
     */
    public function tratamientoAplicado(): BelongsTo
    {
        return $this->belongsTo(TratamientoAplicado::class, 'tratamiento_aplicado_id');
    }

    /**
     * Scope para citas que ocupan activamente la agenda del sillón (RN-07).
     */
    public function scopeProgramadas(Builder $query): Builder
    {
        return $query->whereHas('estado', fn ($q) => $q->where('nombre', EstadoCita::PROGRAMADA));
    }

    /**
     * Retorna la hora de inicio formateada HH:mm.
     */
    public function horaInicioFormato(): string
    {
        return substr((string) $this->hora_inicio, 0, 5);
    }

    /**
     * Retorna la hora de fin formateada HH:mm.
     */
    public function horaFinFormato(): string
    {
        return substr((string) $this->hora_fin, 0, 5);
    }

    /**
     * Verifica si la cita se encuentra en estado programada.
     */
    public function esProgramada(): bool
    {
        return $this->estado?->nombre === EstadoCita::PROGRAMADA;
    }

    /**
     * Verifica si la cita se encuentra en estado atendida.
     */
    public function esAtendida(): bool
    {
        return $this->estado?->nombre === EstadoCita::ATENDIDA;
    }

    /**
     * Verifica si la cita se encuentra en estado cancelada.
     */
    public function esCancelada(): bool
    {
        return $this->estado?->nombre === EstadoCita::CANCELADA;
    }

    /**
     * Verifica si la cita se encuentra en estado no asistida.
     */
    public function esNoAsistida(): bool
    {
        return $this->estado?->nombre === EstadoCita::NO_ASISTIDA;
    }
}
