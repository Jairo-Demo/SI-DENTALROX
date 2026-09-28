@php
    $volvioFormulario = old('formulario') === 'datos';

    $formularioInicial = [
        'id' => $volvioFormulario ? old('id') : null,
        'nombre' => $volvioFormulario ? old('nombre', '') : '',
        'descripcion' => $volvioFormulario ? old('descripcion', '') : '',
    ];
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Catálogo de Especialidades Odontológicas') }}
            </h2>
            <a href="{{ route('paquetes.mostrar', 'p2') }}" class="text-xs font-semibold text-indigo-600 hover:underline">
                &larr; Volver al Paquete P2
            </a>
        </div>
    </x-slot>

    <div class="py-12"
         x-data="{
            abierto: {{ Js::from($volvioFormulario) }},
            form: {{ Js::from($formularioInicial) }},
            nuevo() {
                this.form = { id: null, nombre: '', descripcion: '' };
                this.mostrarFormulario();
            },
            editar(especialidad) {
                this.form = { ...especialidad, descripcion: especialidad.descripcion ?? '' };
                this.mostrarFormulario();
            },
            mostrarFormulario() {
                this.abierto = true;
                this.$nextTick(() => this.$refs.formulario.scrollIntoView({ behavior: 'smooth' }));
            },
         }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Alerta de Éxito -->
            @if (session('exito'))
                <div class="rounded-md bg-green-50 p-4 text-sm font-medium text-green-800 border border-green-200">
                    {{ session('exito') }}
                </div>
            @endif

            <!-- Alerta de Errores de Operación -->
            @if ($errors->any() && ! $volvioFormulario)
                <div class="rounded-md bg-red-50 p-4 text-sm font-medium text-red-800 border border-red-200">
                    <ul class="space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Tarjeta Principal: Tabla de Especialidades -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 dark:border-gray-700">
                <div class="p-6 text-gray-900 dark:text-gray-100 space-y-4">
                    
                    <div class="flex justify-between items-center">
                        <p class="text-sm text-gray-600 dark:text-gray-300 font-medium">Listado de especialidades registradas para asignación de odontólogos.</p>
                        <x-primary-button type="button" @click="nuevo()">
                            + Nueva Especialidad
                        </x-primary-button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                            <thead>
                                <tr class="text-left text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-200 bg-gray-50 dark:bg-gray-700/50">
                                    <th class="px-4 py-3">Nombre</th>
                                    <th class="px-4 py-3">Descripción</th>
                                    <th class="px-4 py-3 text-center">Odontólogos</th>
                                    <th class="px-4 py-3 text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @forelse ($especialidades as $especialidad)
                                    <tr class="align-top hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition text-gray-900 dark:text-gray-100">
                                        <td class="px-4 py-3">
                                            <a href="{{ route('especialidades.mostrar', $especialidad->id) }}" class="font-semibold text-blue-600 dark:text-blue-400 hover:underline">
                                                {{ $especialidad->nombre }}
                                            </a>
                                        </td>
                                        <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $especialidad->descripcion ?? '—' }}</td>
                                        <td class="px-4 py-3 text-center font-medium">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 border border-gray-300 dark:border-gray-600">
                                                {{ $especialidad->usuarios_count }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <div class="flex items-center justify-center gap-2">
                                                <x-secondary-button @click="editar({{ Js::from($especialidad->only(['id', 'nombre', 'descripcion'])) }})">
                                                    Modificar
                                                </x-secondary-button>

                                                <!-- Formulario Eliminar (POST con ID oculto) -->
                                                <form method="POST" action="{{ route('especialidades.eliminar') }}"
                                                      onsubmit="return confirm({{ Js::from('¿Desea eliminar la especialidad '.$especialidad->nombre.'?') }});">
                                                    @csrf
                                                    <input type="hidden" name="id" value="{{ $especialidad->id }}">
                                                    <x-danger-button>Eliminar</x-danger-button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-8 text-center text-gray-400 dark:text-gray-500">
                                            No hay especialidades registradas en el catálogo.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Formulario Desplegable de Registro / Modificación -->
            <div x-ref="formulario" x-show="abierto" x-cloak class="p-6 sm:p-8 bg-white shadow sm:rounded-lg border border-gray-100">
                <section class="max-w-xl">
                    <header>
                        <h2 class="text-lg font-bold text-gray-900"
                            x-text="form.id ? 'Modificar Especialidad' : 'Registrar Nueva Especialidad'"></h2>
                        <p class="text-xs text-gray-500 mt-1">Complete los datos de la especialidad clínica.</p>
                    </header>

                    <form method="POST" :action="form.id ? {{ Js::from(route('especialidades.modificar')) }} : {{ Js::from(route('especialidades.guardar')) }}" class="mt-6 space-y-5">
                        @csrf
                        <input type="hidden" name="formulario" value="datos">
                        <!-- El ID viaja oculto solo al modificar -->
                        <input type="hidden" name="id" :value="form.id" :disabled="! form.id">

                        <div>
                            <x-input-label for="nombre" value="Nombre de la Especialidad *" />
                            <x-text-input id="nombre" name="nombre" type="text" class="mt-1 block w-full" x-model="form.nombre" maxlength="40" placeholder="Ej. Endodoncia" required />
                            @if ($volvioFormulario) <x-input-error :messages="$errors->get('nombre')" class="mt-2" /> @endif
                        </div>

                        <div>
                            <x-input-label for="descripcion" value="Descripción (opcional)" />
                            <textarea id="descripcion" name="descripcion" rows="3" x-model="form.descripcion" placeholder="Breve descripción de los procedimientos que abarca..."
                                      class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm"></textarea>
                            @if ($volvioFormulario) <x-input-error :messages="$errors->get('descripcion')" class="mt-2" /> @endif
                        </div>

                        <div class="flex items-center gap-3 pt-2">
                            <x-primary-button x-text="form.id ? 'Guardar Cambios' : 'Registrar Especialidad'">Guardar</x-primary-button>
                            <x-secondary-button @click="abierto = false">Cancelar</x-secondary-button>
                        </div>
                    </form>
                </section>
            </div>

        </div>
    </div>
</x-app-layout>
