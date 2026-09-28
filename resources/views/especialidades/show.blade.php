<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Detalle de Especialidad: {{ $especialidad->nombre }}
            </h2>
            <a href="{{ route('especialidades.listar') }}" class="text-xs font-semibold text-indigo-600 hover:underline">
                &larr; Volver a Especialidades
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Datos de la Especialidad -->
            <div class="p-6 bg-white shadow sm:rounded-lg border border-gray-100">
                <dl class="grid grid-cols-1 sm:grid-cols-3 gap-6 text-sm">
                    <div>
                        <dt class="text-xs font-bold uppercase tracking-wider text-gray-400">Nombre</dt>
                        <dd class="mt-1 text-base font-semibold text-gray-900">{{ $especialidad->nombre }}</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-xs font-bold uppercase tracking-wider text-gray-400">Descripción</dt>
                        <dd class="mt-1 text-sm text-gray-700 leading-relaxed">{{ $especialidad->descripcion ?? 'Sin descripción registrada.' }}</dd>
                    </div>
                </dl>
            </div>

            <!-- Profesionales Vinculados -->
            <div class="p-6 bg-white shadow sm:rounded-lg border border-gray-100">
                <h3 class="text-base font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <span>Odontólogos con esta Especialidad</span>
                    <span class="text-xs px-2.5 py-0.5 rounded-full bg-indigo-50 text-indigo-700 font-bold">
                        {{ $especialidad->usuarios_count }}
                    </span>
                </h3>

                @if ($especialidad->usuarios->isEmpty())
                    <p class="text-sm text-gray-400 py-4">No hay odontólogos asignados a esta especialidad actualmente.</p>
                @else
                    <ul class="divide-y divide-gray-100 text-sm">
                        @foreach ($especialidad->usuarios as $profesional)
                            <li class="py-3 flex justify-between items-center">
                                <div>
                                    <span class="font-semibold text-gray-900">{{ $profesional->apellidos }}, {{ $profesional->nombres }}</span>
                                    <span class="text-xs text-gray-400 ms-2">Matrícula: {{ $profesional->matricula_profesional }}</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="text-xs font-medium text-gray-500">{{ $profesional->correo }}</span>
                                    <span class="px-2 py-0.5 text-xs font-bold rounded-full {{ $profesional->estado === 'activo' ? 'bg-emerald-50 text-emerald-700' : 'bg-gray-100 text-gray-600' }}">
                                        {{ ucfirst($profesional->estado) }}
                                    </span>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            <div>
                <a href="{{ route('especialidades.listar') }}" 
                   class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 transition">
                    &larr; Volver al Listado
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
