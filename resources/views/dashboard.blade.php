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

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            @if (session('exito'))
                <div class="rounded-md bg-green-50 p-4 text-sm font-medium text-green-800">
                    {{ session('exito') }}
                </div>
            @endif

            <!-- Mensaje de bienvenida estándar -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    Hola, {{ $usuario->nombres }} {{ $usuario->apellidos }}. Elija una opción de los paquetes del sistema.
                </div>
            </div>

            <!-- Una tarjeta por paquete (estructura estándar de referencia) -->
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                @foreach ($paquetes as $paquete)
                    <div class="flex flex-col bg-white shadow-sm sm:rounded-lg overflow-hidden border border-gray-100">
                        <div class="p-6 border-b border-gray-100">
                            <span class="inline-flex rounded bg-indigo-50 px-2 py-0.5 text-xs font-semibold text-indigo-700">
                                {{ $paquete['codigo'] }}
                            </span>
                            <h3 class="mt-2 text-lg font-medium text-gray-900">{{ $paquete['nombre'] }}</h3>
                            <p class="mt-1 text-sm text-gray-600">{{ $paquete['descripcion'] }}</p>
                        </div>

                        <ul class="p-2 flex-1">
                            @foreach ($paquete['opciones'] as $opcion)
                                <li>
                                    @if ($opcion['ruta'] && Route::has($opcion['ruta']))
                                        <a href="{{ route($opcion['ruta']) }}"
                                           class="flex items-center justify-between rounded-md px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 transition">
                                            <span>{{ $opcion['nombre'] }}</span>
                                            <span aria-hidden="true" class="text-gray-400">&rarr;</span>
                                        </a>
                                    @else
                                        {{-- Opción inhabilitada: en gris y sin enlace --}}
                                        <div class="flex items-center justify-between rounded-md px-4 py-2 text-sm text-gray-400 cursor-not-allowed" aria-disabled="true">
                                            <span>{{ $opcion['nombre'] }}</span>
                                            <span class="rounded bg-gray-100 px-1.5 py-0.5 text-[10px] font-medium uppercase tracking-wide">Próximo ciclo</span>
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
