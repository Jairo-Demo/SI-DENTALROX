@php
    $usuario = Auth::user();
    $esAdministrador = $usuario->rol === 'administrador';
    $paquetes = \App\Services\MenuService::paquetes($usuario);

    $paqueteActivo = fn ($paquete) => collect($paquete['opciones'])
        ->contains(fn ($opcion) => $opcion['activa'] && Route::has($opcion['ruta'] ?? '') && request()->routeIs($opcion['activa']));
@endphp

<nav x-data="{ open: false }" class="bg-white border-b border-gray-200 sticky top-0 z-50 shadow-sm">
    <!-- Barra Superior Principal Horizontal -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            
            <!-- Izquierda: Logo y Menú Horizontal de Paquetes -->
            <div class="flex items-center">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="font-bold text-xl text-sky-700 flex items-center gap-2">
                        <span class="text-2xl">🦷</span> DentalRox
                    </a>
                </div>

                <!-- Paquetes en disposición HORIZONTAL (Desktop) -->
                <div class="hidden lg:flex lg:items-center lg:ms-8 lg:gap-1">
                    @foreach ($paquetes as $paquete)
                        <x-dropdown align="left" width="w-72">
                            <x-slot name="trigger">
                                <button class="inline-flex items-center px-3 py-2 rounded-md text-sm font-medium leading-4 text-gray-700 hover:text-sky-700 hover:bg-sky-50 focus:outline-none transition ease-in-out duration-150">
                                    <span class="me-1.5 text-xs font-bold px-1.5 py-0.5 rounded bg-sky-100 text-sky-800">{{ $paquete['codigo'] }}</span>
                                    <span>{{ $paquete['corto'] }}</span>
                                    <svg class="ms-1 fill-current h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <!-- Título del Paquete -->
                                <div class="px-4 py-2 text-xs font-bold uppercase tracking-wider text-sky-800 bg-sky-50 border-b border-gray-100">
                                    {{ $paquete['codigo'] }} · {{ $paquete['nombre'] }}
                                </div>

                                <!-- Lista de Opciones del Paquete -->
                                @foreach ($paquete['opciones'] as $opcion)
                                    @if ($opcion['ruta'] && Route::has($opcion['ruta']))
                                        <x-dropdown-link :href="route($opcion['ruta'])">
                                            {{ $opcion['nombre'] }}
                                        </x-dropdown-link>
                                    @else
                                        <!-- Opción pendiente de implementar en siguientes ciclos -->
                                        <div class="flex items-center justify-between px-4 py-2 text-sm text-gray-400 cursor-not-allowed bg-gray-50/50">
                                            <span>{{ $opcion['nombre'] }}</span>
                                            <span class="rounded bg-gray-200 px-1.5 py-0.5 text-[10px] font-medium text-gray-600 uppercase">Próximo</span>
                                        </div>
                                    @endif
                                @endforeach
                            </x-slot>
                        </x-dropdown>
                    @endforeach
                </div>
            </div>

            <!-- Derecha: Perfil de Usuario y Cerrar Sesión -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-gray-200 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none transition">
                            <span class="inline-block w-2.5 h-2.5 rounded-full bg-emerald-500 me-2"></span>
                            <div>{{ $usuario->nombres }} {{ $usuario->apellidos }}</div>
                            <span class="ms-2 px-2 py-0.5 text-xs font-semibold rounded bg-sky-100 text-sky-800 uppercase">{{ $usuario->rol }}</span>
                            <svg class="ms-1 fill-current h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="px-4 py-2 text-xs text-gray-500 border-b border-gray-100">
                            Usuario: <strong>{{ $usuario->usuario }}</strong><br>
                            Matrícula: {{ $usuario->matricula_profesional }}
                        </div>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();" class="text-rose-600 font-medium">
                                {{ __('Cerrar Sesión') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Botón Menú Móvil (Hamburger) -->
            <div class="-me-2 flex items-center lg:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none transition">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

        </div>
    </div>

    <!-- Menú Desplegable en Móvil -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden lg:hidden bg-gray-50 border-t border-gray-200">
        <div class="pt-2 pb-3 space-y-1">
            @foreach ($paquetes as $paquete)
                <div class="px-4 py-2 text-xs font-bold text-sky-800 uppercase bg-sky-100/60">
                    {{ $paquete['codigo'] }} - {{ $paquete['nombre'] }}
                </div>
                @foreach ($paquete['opciones'] as $opcion)
                    @if ($opcion['ruta'] && Route::has($opcion['ruta']))
                        <x-responsive-nav-link :href="route($opcion['ruta'])">
                            {{ $opcion['nombre'] }}
                        </x-responsive-nav-link>
                    @else
                        <div class="px-4 py-2 text-sm text-gray-400 flex justify-between">
                            <span>{{ $opcion['nombre'] }}</span>
                            <span class="text-xs text-gray-400">Próximo</span>
                        </div>
                    @endif
                @endforeach
            @endforeach
        </div>

        <div class="pt-4 pb-3 border-t border-gray-200 px-4">
            <div class="font-medium text-base text-gray-800">{{ $usuario->nombres }} {{ $usuario->apellidos }}</div>
            <div class="font-medium text-sm text-gray-500 mb-2">{{ $usuario->correo }}</div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full text-left font-medium text-rose-600 py-1">
                    {{ __('Cerrar Sesión') }}
                </button>
            </form>
        </div>
    </div>
</nav>
