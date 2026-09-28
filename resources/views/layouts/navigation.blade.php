@php
    $usuario = Auth::user();
    $esAdministrador = $usuario->rol === 'administrador';
    $paquetes = \App\Services\MenuService::paquetes($usuario);

    $paqueteActivo = fn ($paquete) => collect($paquete['opciones'])
        ->contains(fn ($opcion) => $opcion['activa'] && Route::has($opcion['ruta'] ?? '') && request()->routeIs($opcion['activa']));
@endphp

<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Menú de navegación principal -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                        <span class="font-semibold text-gray-800">{{ config('app.name', 'DentalRox') }}</span>
                    </a>
                </div>

                <!-- Menús desplegables de los paquetes -->
                <div class="hidden lg:flex lg:items-center lg:ms-8 lg:gap-1">
                    @foreach ($paquetes as $paquete)
                        <x-dropdown align="left" width="w-72">
                            <x-slot name="trigger">
                                <button class="inline-flex items-center px-3 py-2 rounded-md text-sm font-medium leading-4 text-gray-500 hover:text-gray-700 hover:bg-gray-50 focus:outline-none transition ease-in-out duration-150">
                                    <span class="me-1 text-xs font-semibold text-indigo-600">{{ $paquete['codigo'] }}</span>
                                    {{ $paquete['corto'] }}
                                    <svg class="ms-1 fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <!-- Nombre completo del paquete -->
                                <div class="px-4 py-2 text-xs font-semibold uppercase tracking-wider text-gray-500 border-b border-gray-100">
                                    {{ $paquete['codigo'] }} · {{ $paquete['nombre'] }}
                                </div>

                                @foreach ($paquete['opciones'] as $opcion)
                                    @if ($opcion['ruta'] && Route::has($opcion['ruta']))
                                        <x-dropdown-link :href="route($opcion['ruta'])">
                                            {{ $opcion['nombre'] }}
                                        </x-dropdown-link>
                                    @else
                                        {{-- Opción inhabilitada: en gris y sin enlace --}}
                                        <div class="flex items-center justify-between px-4 py-2 text-sm leading-5 text-gray-400 cursor-not-allowed">
                                            <span>{{ $opcion['nombre'] }}</span>
                                            <span class="rounded bg-gray-100 px-1.5 py-0.5 text-[10px] font-medium uppercase tracking-wide">Próximo ciclo</span>
                                        </div>
                                    @endif
                                @endforeach
                            </x-slot>
                        </x-dropdown>
                    @endforeach
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ $usuario->nombres }} {{ $usuario->apellidos }} ({{ ucfirst($usuario->rol) }})</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Cerrar Sesión') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center lg:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden lg:hidden">
        <div class="pt-2 pb-3 space-y-1">
            @foreach ($paquetes as $paquete)
                <div class="px-4 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                    {{ $paquete['codigo'] }} - {{ $paquete['corto'] }}
                </div>
                @foreach ($paquete['opciones'] as $opcion)
                    @if ($opcion['ruta'] && Route::has($opcion['ruta']))
                        <x-responsive-nav-link :href="route($opcion['ruta'])">
                            {{ $opcion['nombre'] }}
                        </x-responsive-nav-link>
                    @else
                        <div class="px-4 py-2 text-sm text-gray-400">
                            {{ $opcion['nombre'] }} (Próximo ciclo)
                        </div>
                    @endif
                @endforeach
            @endforeach
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ $usuario->nombres }} {{ $usuario->apellidos }}</div>
                <div class="font-medium text-sm text-gray-500">{{ $usuario->correo }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Cerrar Sesión') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
