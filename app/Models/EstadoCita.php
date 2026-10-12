<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EstadoCita extends Model
{
    /**
     * Nombres estándar de los estados de una cita (RF-71).
     */
    public const PROGRAMADA = 'programada';
    public const ATENDIDA = 'atendida';
    public const CANCELADA = 'cancelada';
    public const NO_ASISTIDA = 'no asistida';

    /**
     * Nombre de la tabla.
     */
    protected $table = 'estados_cita';

    /**
     * La tabla no tiene columnas created_at / updated_at.
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    /**
     * Un estado tiene muchas citas asociadas.
     */
    public function citas(): HasMany
    {
        return $this->hasMany(Cita::class, 'estado_cita_id');
    }
}
