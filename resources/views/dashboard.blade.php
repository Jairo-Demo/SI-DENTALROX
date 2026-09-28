@php
    $usuario = Auth::user();
    $paquetes = \App\Services\MenuService::paquetes($usuario);
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
            <div>
                <h2 class="font-extrabold text-2xl text-gray-900 leading-tight">
                    {{ __('Panel de Control y Módulos') }}
                </h2>
                <p class="text-xs text-gray-500 mt-0.5">Seleccione un paquete para acceder a sus funciones y submódulos.</p>
            </div>
            <span class="text-xs font-bold px-3 py-1.5 rounded-full bg-sky-100 text-sky-800 uppercase tracking-wide border border-sky-200">
                Rol: {{ $usuario->rol }}
            </span>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            <!-- Banner de Bienvenida -->
            <div class="bg-gradient-to-r from-sky-800 via-sky-700 to-cyan-700 text-white overflow-hidden shadow-md sm:rounded-2xl p-6 md:p-8">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div class="flex items-center gap-5">
                        <div class="w-16 h-16 bg-white/15 rounded-2xl flex items-center justify-center text-3xl shadow-inner border border-white/20">
                            🦷
                        </div>
                        <div>
                            <span class="text-xs font-semibold px-2.5 py-0.5 rounded-md bg-white/20 text-white uppercase tracking-wider">
                                Consultorio Odontológico
                            </span>
                            <h3 class="text-2xl font-bold mt-1">
                                ¡Bienvenido(a), {{ $usuario->nombres }} {{ $usuario->apellidos }}!
                            </h3>
                            <p class="text-sky-100 text-sm mt-1">
                                Sistema DentalRox · Panel de Gestión Central
                            </p>
                        </div>
                    </div>
                    <div class="bg-white/10 px-4 py-3 rounded-xl border border-white/10 text-xs backdrop-blur-sm self-start md:self-auto space-y-1">
                        <div>Usuario: <strong>{{ $usuario->usuario }}</strong></div>
                        <div>Matrícula: <strong>{{ $usuario->matricula_profesional }}</strong></div>
                    </div>
                </div>
            </div>

            <!-- Grilla Horizontal de los 5 Paquetes Principales (Clic para entrar a cada uno) -->
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <span>📦</span> Paquetes del Sistema
                    </h3>
                    <span class="text-xs text-gray-500 font-medium">Haga clic en una tarjeta para ver sus opciones</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($paquetes as $paquete)
                        <a href="{{ route('paquetes.mostrar', strtolower($paquete['codigo'])) }}" 
                           class="group flex flex-col justify-between bg-white border border-gray-200 rounded-2xl p-6 shadow-sm hover:shadow-xl hover:border-sky-500 hover:-translate-y-1 transition duration-200 cursor-pointer">
                            
                            <!-- Parte Superior de la Tarjeta -->
                            <div>
                                <div class="flex items-center justify-between mb-4">
                                    <!-- Badge Código -->
                                    <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-extrabold bg-sky-50 text-sky-700 border border-sky-200 group-hover:bg-sky-600 group-hover:text-white transition">
                                        {{ $paquete['codigo'] }}
                                    </span>
                                    <!-- Icono -->
                                    <span class="text-2xl">{{ $paquete['icono'] ?? '📁' }}</span>
                                </div>

                                <h4 class="font-bold text-gray-900 text-lg group-hover:text-sky-700 transition leading-snug">
                                    {{ $paquete['nombre'] }}
                                </h4>
                                
                                <p class="text-xs text-gray-500 mt-2 line-clamp-3 leading-relaxed">
                                    {{ $paquete['descripcion'] }}
                                </p>
                            </div>

                            <!-- Parte Inferior con Contador y Botón -->
                            <div class="pt-6 mt-4 border-t border-gray-100 flex items-center justify-between">
                                <span class="text-xs font-semibold text-gray-400">
                                    {{ count($paquete['opciones']) }} {{ count($paquete['opciones']) === 1 ? 'módulo' : 'módulos' }}
                                </span>

                                <span class="inline-flex items-center gap-1 text-xs font-bold text-sky-600 group-hover:text-sky-800 group-hover:translate-x-1 transition duration-200">
                                    <span>Abrir Paquete</span>
                                    <span>&rarr;</span>
                                </span>
                            </div>

                        </a>
                    @endforeach
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
