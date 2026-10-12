<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <!-- Migas de pan (Breadcrumbs) -->
                <nav class="flex items-center gap-2 text-xs text-gray-500 mb-1">
                    <a href="{{ route('dashboard') }}" class="hover:text-sky-600 transition">Panel Principal</a>
                    <span>/</span>
                    <span class="text-sky-700 font-semibold">{{ $paquete['codigo'] }}</span>
                </nav>
                <h2 class="font-bold text-2xl text-gray-900 leading-tight flex items-center gap-2">
                    <span>{{ $paquete['icono'] ?? '📦' }}</span>
                    <span>{{ $paquete['codigo'] }}: {{ $paquete['nombre'] }}</span>
                </h2>
            </div>

            <!-- Botón Volver -->
            <a href="{{ route('dashboard') }}" 
               class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition shadow-sm">
                <span>&larr;</span> Volver al Panel
            </a>
        </div>
    </x-slot>

    <div class="py-6 sm:py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Banner Descriptivo del Paquete -->
            <div class="bg-white dark:bg-gray-800 border border-sky-100 dark:border-gray-700 p-5 sm:p-6 rounded-xl shadow-sm">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-xl bg-sky-50 dark:bg-sky-950/60 text-sky-700 dark:text-sky-300 flex items-center justify-center text-2xl shrink-0">
                        {{ $paquete['icono'] ?? '📦' }}
                    </div>
                    <div>
                        <span class="text-xs font-bold px-2.5 py-0.5 rounded-full bg-sky-100 dark:bg-sky-900/60 text-sky-800 dark:text-sky-300 uppercase tracking-wide">
                            Módulo de {{ $paquete['corto'] }}
                        </span>
                        <p class="text-gray-600 dark:text-gray-300 text-sm mt-2 leading-relaxed">
                            {{ $paquete['descripcion'] }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Cuadrícula de Módulos y Acciones del Paquete -->
            <div>
                <h3 class="text-base font-bold text-gray-800 dark:text-gray-200 mb-4 flex items-center gap-2">
                    <span>Sub-Módulos y Procesos Disponibles</span>
                    <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300">
                        {{ count($paquete['opciones']) }} opciones
                    </span>
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                    @foreach ($paquete['opciones'] as $opcion)
                        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-5 sm:p-6 shadow-sm flex flex-col justify-between hover:border-sky-300 dark:hover:border-sky-500 hover:shadow-md transition duration-200">
                            <div>
                                <div class="flex items-center justify-between mb-3">
                                    <div class="w-10 h-10 rounded-lg bg-sky-50 dark:bg-sky-950/60 text-sky-600 dark:text-sky-300 flex items-center justify-center font-bold text-sm">
                                        {{ substr($opcion['nombre'], 0, 2) }}
                                    </div>
                                    @if ($opcion['solo_admin'] ?? false)
                                        <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded bg-amber-50 dark:bg-amber-900/50 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-700">
                                            Admin
                                        </span>
                                    @endif
                                </div>

                                <h4 class="font-bold text-gray-900 dark:text-gray-100 text-base sm:text-lg mb-1">{{ $opcion['nombre'] }}</h4>
                                <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mb-6">
                                    {{ $opcion['detalle'] ?? 'Gestión del módulo y operaciones del sistema.' }}
                                </p>
                            </div>

                            <!-- Botón de Acción -->
                            <div>
                                @if ($opcion['ruta'] && Route::has($opcion['ruta']))
                                    <a href="{{ route($opcion['ruta']) }}" 
                                       class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-sky-600 hover:bg-sky-700 text-white font-semibold text-sm rounded-lg shadow-sm transition">
                                        <span>Abrir Módulo</span>
                                        <span>&rarr;</span>
                                    </a>
                                @else
                                    <div class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-400 dark:text-gray-500 font-medium text-sm rounded-lg cursor-not-allowed">
                                        <span>Próximo Ciclo</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
