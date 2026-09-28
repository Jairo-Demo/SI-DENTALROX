<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Panel Principal') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Tarjeta de Bienvenida -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex items-center gap-4">
                    <div class="text-4xl">🦷</div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">
                            ¡Bienvenido(a), {{ Auth::user()->nombres }} {{ Auth::user()->apellidos }}!
                        </h3>
                        <p class="text-sm text-gray-600">
                            Rol: <span class="font-semibold text-sky-700 uppercase">{{ Auth::user()->rol }}</span> | 
                            Usuario: <span class="font-semibold text-gray-800">{{ Auth::user()->usuario }}</span> |
                            Matrícula: <span class="text-gray-700">{{ Auth::user()->matricula_profesional }}</span>
                        </p>
                    </div>
                </div>

                <hr class="my-6 border-gray-200">

                <div class="text-gray-700 text-sm">
                    <p class="mb-2">Has iniciado sesión correctamente en <strong>DentalRox</strong>.</p>
                    <p class="text-gray-500">Desde este panel podrás gestionar los módulos de Especialidades, Pacientes, Citas y Odontogramas.</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
