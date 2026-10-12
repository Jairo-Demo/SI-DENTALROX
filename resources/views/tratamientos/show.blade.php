<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <div>
                <nav class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400 mb-1">
                    <a href="{{ route('paquetes.mostrar', 'p2') }}" class="hover:underline">Paquete P2</a>
                    <span>/</span>
                    <a href="{{ route('tratamientos.listar') }}" class="hover:underline">Tratamientos</a>
                    <span>/</span>
                    <span class="text-indigo-600 dark:text-indigo-400 font-semibold">Detalle</span>
                </nav>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Ficha Clínica: {{ $tratamiento->nombre }}
                </h2>
            </div>
            <a href="{{ route('tratamientos.listar') }}" class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">
                &larr; Volver al Catálogo
            </a>
        </div>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 dark:border-gray-700">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 pb-6 border-b border-gray-100 dark:border-gray-700">
                        <div>
                            <span class="inline-flex rounded-full bg-indigo-50 dark:bg-indigo-900/40 px-2.5 py-0.5 text-xs font-semibold text-indigo-700 dark:text-indigo-300">
                                {{ $tratamiento->especialidad->nombre }}
                            </span>
                            <h3 class="text-2xl font-bold mt-2 text-gray-900 dark:text-gray-100">{{ $tratamiento->nombre }}</h3>
                        </div>
                        <div>
                            @if ($tratamiento->esDisponible())
                                <span class="inline-flex rounded-full bg-green-100 dark:bg-green-900/60 px-3 py-1 text-xs font-semibold text-green-800 dark:text-green-200 border border-green-300 dark:border-green-700">
                                    Disponible para citas
                                </span>
                            @else
                                <span class="inline-flex rounded-full bg-gray-200 dark:bg-gray-700 px-3 py-1 text-xs font-semibold text-gray-800 dark:text-gray-200 border border-gray-400 dark:border-gray-600">
                                    Dado de baja
                                </span>
                            @endif
                        </div>
                    </div>

                    <dl class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 pt-6 text-sm">
                        <div class="p-4 rounded-lg bg-gray-50 dark:bg-gray-900/40 border border-gray-100 dark:border-gray-800">
                            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Duración Estimada</dt>
                            <dd class="mt-1 text-xl font-bold text-gray-900 dark:text-gray-100">⏱ {{ $tratamiento->duracion_estimada_minutos }} minutos</dd>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Bloque que ocupa en el sillón dental único.</p>
                        </div>

                        <div class="p-4 rounded-lg bg-gray-50 dark:bg-gray-900/40 border border-gray-100 dark:border-gray-800">
                            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Sesiones Previstas</dt>
                            <dd class="mt-1 text-xl font-bold text-gray-900 dark:text-gray-100">{{ $tratamiento->numero_sesiones_previstas }} sesión(es)</dd>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                {{ $tratamiento->requiere_sesiones ? 'Requiere citas de seguimiento' : 'Tratamiento de sesión única' }}
                            </p>
                        </div>

                        <div class="p-4 rounded-lg bg-gray-50 dark:bg-gray-900/40 border border-gray-100 dark:border-gray-800">
                            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Especialidad Clínica</dt>
                            <dd class="mt-1 text-base font-semibold text-gray-900 dark:text-gray-100">{{ $tratamiento->especialidad->nombre }}</dd>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Ramas y especialistas capacitados.</p>
                        </div>

                        <div class="sm:col-span-2 lg:col-span-3 p-4 rounded-lg bg-gray-50 dark:bg-gray-900/40 border border-gray-100 dark:border-gray-800">
                            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Descripción del Procedimiento</dt>
                            <dd class="mt-2 text-sm text-gray-700 dark:text-gray-300 leading-relaxed">
                                {{ $tratamiento->descripcion ?? 'Sin descripción clínica registrada.' }}
                            </dd>
                        </div>
                    </dl>

                    <div class="mt-8 pt-6 border-t border-gray-100 dark:border-gray-700 flex flex-wrap gap-4">
                        <a href="{{ route('citas.crear') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-md text-sm font-medium shadow-sm transition">
                            📅 Programar cita con este tratamiento
                        </a>
                        <a href="{{ route('tratamientos.listar') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 rounded-md text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-600 transition">
                            Volver a la lista
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
