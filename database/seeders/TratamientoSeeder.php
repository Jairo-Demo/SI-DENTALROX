<?php

namespace Database\Seeders;

use App\Models\Especialidad;
use App\Models\Tratamiento;
use Illuminate\Database\Seeder;

class TratamientoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $general = Especialidad::where('nombre', 'like', '%General%')->first();
        $ortodoncia = Especialidad::where('nombre', 'like', '%Ortodoncia%')->first();
        $endodoncia = Especialidad::where('nombre', 'like', '%Endodoncia%')->first();
        $cirugia = Especialidad::where('nombre', 'like', '%Cirug%')->first();
        $odontopediatria = Especialidad::where('nombre', 'like', '%Pediatr%')->first();

        // Si no existen especialidades, toma las primeras disponibles
        $defectoId = Especialidad::first()?->id ?? 1;

        $tratamientos = [
            [
                'nombre' => 'Limpieza Dental y Profilaxis',
                'descripcion' => 'Eliminación de placa bacteriana, cálculo dental y pulido coronario general.',
                'especialidad_id' => $general?->id ?? $defectoId,
                'duracion_estimada_minutos' => 45,
                'numero_sesiones_previstas' => 1,
                'requiere_sesiones' => false,
                'estado' => 'disponible',
            ],
            [
                'nombre' => 'Evaluación y Diagnóstico de Ortodoncia',
                'descripcion' => 'Estudio clínico preliminar, toma de impresiones y propuesta de plan ortodóncico.',
                'especialidad_id' => $ortodoncia?->id ?? $defectoId,
                'duracion_estimada_minutos' => 30,
                'numero_sesiones_previstas' => 1,
                'requiere_sesiones' => false,
                'estado' => 'disponible',
            ],
            [
                'nombre' => 'Instalación de Brackets Metálicos',
                'descripcion' => 'Cementado de aparatología fija superior e inferior con colocación de arcos iniciales.',
                'especialidad_id' => $ortodoncia?->id ?? $defectoId,
                'duracion_estimada_minutos' => 90,
                'numero_sesiones_previstas' => 1,
                'requiere_sesiones' => true,
                'estado' => 'disponible',
            ],
            [
                'nombre' => 'Endodoncia Unirradicular',
                'descripcion' => 'Tratamiento de conducto en pieza dentaria anterior o premolar.',
                'especialidad_id' => $endodoncia?->id ?? $defectoId,
                'duracion_estimada_minutos' => 60,
                'numero_sesiones_previstas' => 2,
                'requiere_sesiones' => true,
                'estado' => 'disponible',
            ],
            [
                'nombre' => 'Extracción Dental Simple',
                'descripcion' => 'Avulsión quirúrgica simple de pieza dentaria con anestesia local.',
                'especialidad_id' => $cirugia?->id ?? $defectoId,
                'duracion_estimada_minutos' => 45,
                'numero_sesiones_previstas' => 1,
                'requiere_sesiones' => false,
                'estado' => 'disponible',
            ],
            [
                'nombre' => 'Blanqueamiento Dental en Consultorio',
                'descripcion' => 'Aplicación de gel blanqueador fotoactivado por sesiones clínicas.',
                'especialidad_id' => $general?->id ?? $defectoId,
                'duracion_estimada_minutos' => 60,
                'numero_sesiones_previstas' => 2,
                'requiere_sesiones' => true,
                'estado' => 'disponible',
            ],
            [
                'nombre' => 'Consulta y Fluorización Pediátrica',
                'descripcion' => 'Revisión preventiva infantil y aplicación tópica de flúor de alta concentración.',
                'especialidad_id' => $odontopediatria?->id ?? $defectoId,
                'duracion_estimada_minutos' => 30,
                'numero_sesiones_previstas' => 1,
                'requiere_sesiones' => false,
                'estado' => 'disponible',
            ],
        ];

        foreach ($tratamientos as $t) {
            Tratamiento::updateOrCreate(
                ['nombre' => $t['nombre']],
                $t
            );
        }
    }
}
