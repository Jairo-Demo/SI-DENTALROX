<?php

namespace Database\Seeders;

use App\Models\EstadoCita;
use Illuminate\Database\Seeder;

class EstadoCitaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $estados = [
            [
                'nombre' => EstadoCita::PROGRAMADA,
                'descripcion' => 'Cita médica agendada que ocupa la franja horaria del sillón dental único.',
            ],
            [
                'nombre' => EstadoCita::ATENDIDA,
                'descripcion' => 'Cita en la que el paciente asistió y fue atendido por el profesional.',
            ],
            [
                'nombre' => EstadoCita::CANCELADA,
                'descripcion' => 'Cita anulada o reprogramada, liberando la franja horaria correspondiente.',
            ],
            [
                'nombre' => EstadoCita::NO_ASISTIDA,
                'descripcion' => 'Cita en la que el paciente no se presentó en la fecha y hora pactadas.',
            ],
        ];

        foreach ($estados as $estado) {
            EstadoCita::updateOrCreate(
                ['nombre' => $estado['nombre']],
                ['descripcion' => $estado['descripcion']]
            );
        }
    }
}
