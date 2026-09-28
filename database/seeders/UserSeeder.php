<?php

namespace Database\Seeders;

use App\Models\Especialidad;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $espGeneral = Especialidad::where('nombre', 'Odontología General')->first()?->id ?? 1;
        $espOrtodoncia = Especialidad::where('nombre', 'Ortodoncia')->first()?->id ?? 1;

        // Usuario Administrador principal
        User::firstOrCreate(
            ['usuario' => 'admin'],
            [
                'nombres' => 'Administrador',
                'apellidos' => 'Sistema',
                'ci' => '1234567',
                'matricula_profesional' => 'MAT-ADM-001',
                'especialidad_id' => $espGeneral,
                'correo' => 'admin@dentalrox.com',
                'password' => Hash::make('Admin123*'),
                'telefono' => '70012345',
                'rol' => 'administrador',
                'estado' => 'activo',
                'intentos_fallidos' => 0,
                'bloqueado_hasta' => null,
                'debe_cambiar_contrasena' => false,
            ]
        );

        // Usuario Odontólogo de prueba
        User::firstOrCreate(
            ['usuario' => 'odontologo1'],
            [
                'nombres' => 'Carlos',
                'apellidos' => 'Mendoza Vargas',
                'ci' => '7654321',
                'matricula_profesional' => 'MAT-ODO-102',
                'especialidad_id' => $espOrtodoncia,
                'correo' => 'carlos.mendoza@dentalrox.com',
                'password' => Hash::make('Odonto123*'),
                'telefono' => '71198765',
                'rol' => 'odontologo',
                'estado' => 'activo',
                'intentos_fallidos' => 0,
                'bloqueado_hasta' => null,
                'debe_cambiar_contrasena' => false,
            ]
        );
    }
}
