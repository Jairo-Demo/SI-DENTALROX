{{-- CU08 Consultar paciente: ficha con la edad calculada (no se guarda). --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Paciente: {{ $paciente->nombreCompleto() }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('exito'))
                <div class="rounded-md bg-green-50 dark:bg-green-900/30 p-4 text-sm font-medium text-green-800 dark:text-green-200">
                    {{ session('exito') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="rounded-md bg-red-50 dark:bg-red-900/30 p-4 text-sm font-medium text-red-800 dark:text-red-200">
                    <ul class="space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Datos personales --}}
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="flex items-start justify-between gap-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Datos del paciente</h3>
                    @if ($paciente->estado === 'activo')
                        <span class="inline-flex rounded-full bg-green-100 dark:bg-green-900/40 px-2 py-0.5 text-xs font-medium text-green-800 dark:text-green-200">Activo</span>
                    @else
                        <span class="inline-flex rounded-full bg-gray-200 dark:bg-gray-700 px-2 py-0.5 text-xs font-medium text-gray-700 dark:text-gray-300">Inactivo</span>
                    @endif
                </div>

                <dl class="mt-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-4 text-sm">
                    <div>
                        <dt class="text-gray-500 dark:text-gray-400">CI</dt>
                        <dd class="font-medium text-gray-900 dark:text-gray-100">{{ $paciente->ci }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 dark:text-gray-400">Nombres</dt>
                        <dd class="font-medium text-gray-900 dark:text-gray-100">{{ $paciente->nombres }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 dark:text-gray-400">Apellidos</dt>
                        <dd class="font-medium text-gray-900 dark:text-gray-100">{{ $paciente->apellidos }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 dark:text-gray-400">Fecha de nacimiento</dt>
                        <dd class="font-medium text-gray-900 dark:text-gray-100">{{ $paciente->fecha_nacimiento->format('d/m/Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 dark:text-gray-400">Edad</dt>
                        <dd class="font-medium text-gray-900 dark:text-gray-100">{{ $paciente->edad() }} años</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 dark:text-gray-400">Sexo</dt>
                        <dd class="font-medium text-gray-900 dark:text-gray-100">{{ $paciente->sexo === 'F' ? 'Femenino' : 'Masculino' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 dark:text-gray-400">Teléfono</dt>
                        <dd class="font-medium text-gray-900 dark:text-gray-100">{{ $paciente->telefono ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 dark:text-gray-400">Correo</dt>
                        <dd class="font-medium text-gray-900 dark:text-gray-100">{{ $paciente->correo ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 dark:text-gray-400">Dirección</dt>
                        <dd class="font-medium text-gray-900 dark:text-gray-100">{{ $paciente->direccion ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 dark:text-gray-400">Fecha de registro</dt>
                        <dd class="font-medium text-gray-900 dark:text-gray-100">{{ $paciente->fecha_registro->format('d/m/Y') }}</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-gray-500 dark:text-gray-400">Observaciones</dt>
                        <dd class="font-medium text-gray-900 dark:text-gray-100 whitespace-pre-line">{{ $paciente->observaciones ?? '—' }}</dd>
                    </div>
                </dl>
            </div>

            {{-- Historia clínica (se abre al registrar al paciente) --}}
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Historia clínica</h3>

                @if ($paciente->historiaClinica)
                    <dl class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-x-6 gap-y-4 text-sm">
                        <div>
                            <dt class="text-gray-500 dark:text-gray-400">N.º de historia</dt>
                            <dd class="font-medium text-gray-900 dark:text-gray-100">{{ $paciente->historiaClinica->id }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500 dark:text-gray-400">Fecha de apertura</dt>
                            <dd class="font-medium text-gray-900 dark:text-gray-100">{{ $paciente->historiaClinica->fecha_apertura->format('d/m/Y') }}</dd>
                        </div>
                    </dl>
                    <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">
                        Los antecedentes, el odontograma y los tratamientos se registrarán en el paquete de Atención Clínica.
                    </p>
                @else
                    <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">El paciente no tiene historia clínica.</p>
                @endif
            </div>

            {{-- Historial de citas en el sillón dental --}}
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Citas en el sillón dental</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Historial y próximas citas programadas para este paciente.</p>
                    </div>
                    @if ($paciente->estado === 'activo')
                        <a href="{{ route('citas.crear', ['paciente_id' => $paciente->id]) }}"
                           class="inline-flex items-center justify-center px-3 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-md shadow-sm transition">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Programar cita
                        </a>
                    @endif
                </div>

                @if ($paciente->citas->isNotEmpty())
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                            <thead>
                                <tr class="text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    <th class="py-2.5 px-3">Fecha</th>
                                    <th class="py-2.5 px-3">Horario</th>
                                    <th class="py-2.5 px-3">Odontólogo</th>
                                    <th class="py-2.5 px-3">Motivo / Tratamiento</th>
                                    <th class="py-2.5 px-3">Estado</th>
                                    <th class="py-2.5 px-3 text-right">Acción</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach ($paciente->citas as $cita)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-750">
                                        <td class="py-2.5 px-3 font-medium text-gray-900 dark:text-gray-100 whitespace-nowrap">
                                            {{ $cita->fecha->format('d/m/Y') }}
                                        </td>
                                        <td class="py-2.5 px-3 text-gray-600 dark:text-gray-300 whitespace-nowrap font-mono text-xs">
                                            {{ $cita->horaInicioFormato() }} - {{ $cita->horaFinFormato() }}
                                            <span class="text-gray-400">({{ $cita->duracion_minutos }}m)</span>
                                        </td>
                                        <td class="py-2.5 px-3 text-gray-700 dark:text-gray-300">
                                            {{ $cita->odontologo?->nombreCompleto() ?? 'Dr(a). '.$cita->odontologo?->nombres }}
                                        </td>
                                        <td class="py-2.5 px-3 text-gray-600 dark:text-gray-400 max-w-xs truncate">
                                            {{ $cita->motivo }}
                                        </td>
                                        <td class="py-2.5 px-3 whitespace-nowrap">
                                            @php
                                                $badgeClasses = match($cita->estado_cita_id) {
                                                    'programada' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300',
                                                    'atendida' => 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300',
                                                    'cancelada' => 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300',
                                                    'no_asistida' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300',
                                                    default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'
                                                };
                                            @endphp
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $badgeClasses }}">
                                                {{ $cita->estado?->nombre ?? ucfirst($cita->estado_cita_id) }}
                                            </span>
                                        </td>
                                        <td class="py-2.5 px-3 text-right whitespace-nowrap">
                                            <a href="{{ route('citas.show', ['id' => $cita->id]) }}"
                                               class="text-indigo-600 dark:text-indigo-400 hover:underline font-medium text-xs">
                                                Ver detalle &rarr;
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-6 border-2 border-dashed border-gray-200 dark:border-gray-700 rounded-lg">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Este paciente no tiene citas registradas aún.</p>
                        @if ($paciente->estado === 'activo')
                            <a href="{{ route('citas.crear', ['paciente_id' => $paciente->id]) }}"
                               class="mt-2 inline-flex items-center text-xs font-medium text-indigo-600 dark:text-indigo-400 hover:underline">
                                Programar la primera cita &rarr;
                            </a>
                        @endif
                    </div>
                @endif
            </div>

            <div class="flex flex-wrap items-center gap-4">
                <a href="{{ route('pacientes.listar') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700">
                    Volver a pacientes
                </a>

                @if ($paciente->estado === 'activo')
                    <a href="{{ route('citas.crear', ['paciente_id' => $paciente->id]) }}"
                       class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Programar cita
                    </a>
                @endif

                {{-- CU08: inhabilitar / habilitar desde la ficha (el id viaja oculto) --}}
                @if ($paciente->estado === 'activo')
                    <form method="POST" action="{{ route('pacientes.eliminar') }}"
                          onsubmit="return confirm({{ Js::from('¿Inhabilitar al paciente '.$paciente->nombreCompleto().'? Su historia clínica se conserva.') }});">
                        @csrf
                        <input type="hidden" name="id" value="{{ $paciente->id }}">
                        <x-danger-button>Inhabilitar</x-danger-button>
                    </form>
                @else
                    <form method="POST" action="{{ route('pacientes.habilitar') }}">
                        @csrf
                        <input type="hidden" name="id" value="{{ $paciente->id }}">
                        <x-secondary-button type="submit">Habilitar</x-secondary-button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
