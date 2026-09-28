<?php

namespace Database\Seeders;

use App\Models\Especialidad;
use Illuminate\Database\Seeder;

class EspecialidadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $especialidades = [
            [
                'nombre' => 'Odontología General',
                'descripcion' => 'Atención primaria, prevención, diagnóstico y tratamiento general.',
            ],
            [
                'nombre' => 'Ortodoncia',
                'descripcion' => 'Corrección de anomalías de posición, forma y alineación dental.',
            ],
            [
                'nombre' => 'Endodoncia',
                'descripcion' => 'Tratamiento del tejido pulpar y conductos radiculares.',
            ],
            [
                'nombre' => 'Periodoncia',
                'descripcion' => 'Tratamiento de enfermedades de las encías y estructuras de soporte.',
            ],
            [
                'nombre' => 'Cirugía Maxilofacial',
                'descripcion' => 'Extracciones complejas y cirugías de la estructura bucal y facial.',
            ],
            [
                'nombre' => 'Implantología',
                'descripcion' => 'Colocación y rehabilitación de implantes dentales osteointegrados.',
            ],
            [
                'nombre' => 'Odontopediatría',
                'descripcion' => 'Atención odontológica integral especializada en niños y adolescentes.',
            ],
        ];

        foreach ($especialidades as $especialidad) {
            Especialidad::firstOrCreate(
                ['nombre' => $especialidad['nombre']],
                ['descripcion' => $especialidad['descripcion']]
            );
        }
    }
}
