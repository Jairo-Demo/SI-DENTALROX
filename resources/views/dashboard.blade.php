@php
    $usuario = Auth::user();
    $paquetes = \App\Services\MenuService::paquetes($usuario);
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Panel
        </h2>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            
            @if (session('exito'))
                <div class="rounded-md bg-green-50 dark:bg-green-900/30 p-4 text-sm font-medium text-green-800 dark:text-green-200 border border-green-200 dark:border-green-800">
                    {{ session('exito') }}
                </div>
            @endif

            <!-- Mensaje de bienvenida estándar -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 dark:border-gray-700">
                <div class="p-5 sm:p-6 text-gray-900 dark:text-gray-100 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-lg text-gray-900 dark:text-gray-100">Hola, {{ $usuario->nombres }} {{ $usuario->apellidos }}</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-0.5">Elija una opción de los paquetes del sistema o navegue desde el menú.</p>
                    </div>
                    <span class="hidden sm:inline-flex text-xs font-semibold px-2.5 py-1 rounded-full bg-indigo-50 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300">
                        {{ ucfirst($usuario->rol) }}
                    </span>
                </div>
            </div>

            <!-- Una tarjeta por paquete (estructura adaptable a móvil y PC) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                @foreach ($paquetes as $paquete)
                    <div class="flex flex-col bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg overflow-hidden border border-gray-100 dark:border-gray-700 hover:border-indigo-300 dark:hover:border-indigo-600 transition">
                        <div class="p-5 sm:p-6 border-b border-gray-100 dark:border-gray-700">
                            <span class="inline-flex rounded bg-indigo-50 dark:bg-indigo-900/50 px-2 py-0.5 text-xs font-semibold text-indigo-700 dark:text-indigo-300">
                                {{ $paquete['codigo'] }}
                            </span>
                            <h3 class="mt-2 text-base sm:text-lg font-bold text-gray-900 dark:text-gray-100">{{ $paquete['nombre'] }}</h3>
                            <p class="mt-1 text-xs sm:text-sm text-gray-600 dark:text-gray-400 leading-relaxed">{{ $paquete['descripcion'] }}</p>
                        </div>

                        <ul class="p-2 flex-1 divide-y divide-gray-50 dark:divide-gray-700/50">
                            @foreach ($paquete['opciones'] as $opcion)
                                <li>
                                    @if ($opcion['ruta'] && Route::has($opcion['ruta']))
                                        <a href="{{ route($opcion['ruta']) }}"
                                           class="flex items-center justify-between rounded-md px-3 sm:px-4 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                            <span>{{ $opcion['nombre'] }}</span>
                                            <span aria-hidden="true" class="text-gray-400">&rarr;</span>
                                        </a>
                                    @else
                                        {{-- Opción inhabilitada: en gris y sin enlace --}}
                                        <div class="flex items-center justify-between rounded-md px-3 sm:px-4 py-2 text-sm text-gray-400 dark:text-gray-500 cursor-not-allowed" aria-disabled="true">
                                            <span>{{ $opcion['nombre'] }}</span>
                                            <span class="rounded bg-gray-100 dark:bg-gray-700 px-1.5 py-0.5 text-[10px] font-medium uppercase tracking-wide">Próximo ciclo</span>
                                        </div>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>

        </div>
    </div>
</x-app-layout>
