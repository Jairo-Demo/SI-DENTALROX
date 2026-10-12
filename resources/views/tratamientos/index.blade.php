{{-- CU15 Gestionar catálogo de tratamientos (Solo Administrador) --}}
@php
    $volvioFormulario = old('formulario') === 'datos';

    $formularioInicial = [
        'id' => $volvioFormulario ? old('id') : null,
        'nombre' => $volvioFormulario ? old('nombre', '') : '',
        'descripcion' => $volvioFormulario ? old('descripcion', '') : '',
        'especialidad_id' => $volvioFormulario ? old('especialidad_id', '') : '',
        'duracion_estimada_minutos' => $volvioFormulario ? old('duracion_estimada_minutos', 30) : 30,
        'numero_sesiones_previstas' => $volvioFormulario ? old('numero_sesiones_previstas', 1) : 1,
        'requiere_sesiones' => $volvioFormulario ? (bool) old('requiere_sesiones') : false,
    ];

    $claseCampo = 'mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm';
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <div>
                <nav class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400 mb-1">
                    <a href="{{ route('paquetes.mostrar', 'p2') }}" class="hover:underline">Paquete P2 (Catálogos Clínicos)</a>
                    <span>/</span>
                    <span class="text-indigo-600 dark:text-indigo-400 font-semibold">Tratamientos</span>
                </nav>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Catálogo de Tratamientos Odontológicos
                </h2>
            </div>
            <a href="{{ route('paquetes.mostrar', 'p2') }}" class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">
                &larr; Volver al Paquete P2
            </a>
        </div>
    </x-slot>

    <div class="py-6 sm:py-12"
         x-data="{
            abierto: {{ Js::from($volvioFormulario) }},
            form: {{ Js::from($formularioInicial) }},
            nuevo() {
                this.form = { id: null, nombre: '', descripcion: '', especialidad_id: '',
                              duracion_estimada_minutos: 30, numero_sesiones_previstas: 1, requiere_sesiones: false };
                this.mostrarFormulario();
            },
            editar(t) {
                this.form = {
                    id: t.id,
                    nombre: t.nombre,
                    descripcion: t.descripcion ?? '',
                    especialidad_id: String(t.especialidad_id),
                    duracion_estimada_minutos: t.duracion_estimada_minutos,
                    numero_sesiones_previstas: t.numero_sesiones_previstas,
                    requiere_sesiones: Boolean(t.requiere_sesiones)
                };
                this.mostrarFormulario();
            },
            mostrarFormulario() {
                this.abierto = true;
                this.$nextTick(() => this.$refs.formulario.scrollIntoView({ behavior: 'smooth' }));
            },
         }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Mensajes de éxito -->
            @if (session('exito'))
                <div class="rounded-md bg-green-50 dark:bg-green-900/30 p-4 text-sm font-medium text-green-800 dark:text-green-200 border border-green-200 dark:border-green-800">
                    {{ session('exito') }}
                </div>
            @endif

            <!-- Errores de operaciones -->
            @if ($errors->any() && ! $volvioFormulario)
                <div class="rounded-md bg-red-50 dark:bg-red-900/30 p-4 text-sm font-medium text-red-800 dark:text-red-200 border border-red-200 dark:border-red-800">
                    <ul class="space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Panel de Búsqueda y Filtros -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 dark:border-gray-700">
                <div class="p-4 sm:p-6 text-gray-900 dark:text-gray-100 space-y-4">
                    <form method="GET" action="{{ route('tratamientos.listar') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
                        <div>
                            <x-input-label for="buscar" value="Buscar tratamiento" />
                            <x-text-input id="buscar" name="buscar" type="text" class="mt-1 block w-full" :value="$buscar" placeholder="Por nombre o descripción..." />
                        </div>

                        <div>
                            <x-input-label for="especialidad_id_filtro" value="Especialidad" />
                            <select id="especialidad_id_filtro" name="especialidad_id" class="{{ $claseCampo }}">
                                <option value="">Todas las especialidades</option>
                                @foreach ($especialidades as $esp)
                                    <option value="{{ $esp->id }}" @selected((string)$especialidadId === (string)$esp->id)>
                                        {{ $esp->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <x-input-label for="estado_filtro" value="Estado" />
                            <select id="estado_filtro" name="estado" class="{{ $claseCampo }}">
                                <option value="todos" @selected($estado === 'todos')>Todos los estados</option>
                                <option value="disponible" @selected($estado === 'disponible')>Solo disponibles</option>
                                <option value="no disponible" @selected($estado === 'no disponible')>Dados de baja</option>
                            </select>
                        </div>

                        <div class="flex items-center gap-2">
                            <x-primary-button class="w-full sm:w-auto justify-center">Filtrar</x-primary-button>
                            @if ($buscar || $especialidadId || $estado !== 'todos')
                                <a href="{{ route('tratamientos.listar') }}" class="text-sm text-gray-600 dark:text-gray-400 underline">Limpiar</a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tabla de Tratamientos -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 dark:border-gray-700">
                <div class="p-4 sm:p-6 text-gray-900 dark:text-gray-100 space-y-4">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                        <p class="text-sm text-gray-600 dark:text-gray-400 font-medium">
                            {{ $tratamientos->total() }} tratamiento(s) registrado(s) en el catálogo.
                        </p>
                        <x-primary-button type="button" @click="nuevo()" class="self-start sm:self-auto">
                            + Nuevo Tratamiento
                        </x-primary-button>
                    </div>

                    <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                            <thead>
                                <tr class="text-left text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-200 bg-gray-50 dark:bg-gray-700/50">
                                    <th class="px-4 py-3 whitespace-nowrap">Tratamiento</th>
                                    <th class="px-4 py-3 whitespace-nowrap">Especialidad</th>
                                    <th class="px-4 py-3 text-center whitespace-nowrap">Duración est.</th>
                                    <th class="px-4 py-3 text-center whitespace-nowrap">Sesiones</th>
                                    <th class="px-4 py-3 text-center whitespace-nowrap">Estado</th>
                                    <th class="px-4 py-3 text-center whitespace-nowrap">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @forelse ($tratamientos as $t)
                                    <tr class="align-top hover:bg-gray-50 dark:hover:bg-gray-700/30 transition text-gray-900 dark:text-gray-100">
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <a href="{{ route('tratamientos.mostrar', $t->id) }}" class="font-semibold text-blue-600 dark:text-blue-400 hover:underline">
                                                {{ $t->nombre }}
                                            </a>
                                            @if ($t->descripcion)
                                                <div class="text-xs text-gray-500 dark:text-gray-400 max-w-xs truncate">{{ $t->descripcion }}</div>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-gray-700 dark:text-gray-300">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-50 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-300">
                                                {{ $t->especialidad->nombre }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-center whitespace-nowrap font-medium text-gray-800 dark:text-gray-200">
                                            ⏱ {{ $t->duracion_estimada_minutos }} min
                                        </td>
                                        <td class="px-4 py-3 text-center whitespace-nowrap">
                                            <span class="font-medium text-gray-800 dark:text-gray-200">{{ $t->numero_sesiones_previstas }}</span>
                                            @if ($t->requiere_sesiones)
                                                <span class="block text-[10px] text-amber-600 dark:text-amber-400 font-semibold uppercase">Múltiples</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-center whitespace-nowrap">
                                            @if ($t->esDisponible())
                                                <span class="inline-flex rounded-full bg-green-100 dark:bg-green-900/60 px-2.5 py-0.5 text-xs font-semibold text-green-800 dark:text-green-200 border border-green-300 dark:border-green-700">
                                                    Disponible
                                                </span>
                                            @else
                                                <span class="inline-flex rounded-full bg-gray-200 dark:bg-gray-700 px-2.5 py-0.5 text-xs font-semibold text-gray-800 dark:text-gray-200 border border-gray-400 dark:border-gray-600">
                                                    No disponible
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-center whitespace-nowrap">
                                            <div class="flex items-center justify-center gap-2">
                                                <!-- Modificar -->
                                                <x-secondary-button @click="editar({{ Js::from($t->only(['id', 'nombre', 'descripcion', 'especialidad_id', 'duracion_estimada_minutos', 'numero_sesiones_previstas', 'requiere_sesiones'])) }})">
                                                    Modificar
                                                </x-secondary-button>

                                                <!-- Baja lógica o reactivación (RNF-06) -->
                                                @if ($t->esDisponible())
                                                    <form method="POST" action="{{ route('tratamientos.eliminar') }}"
                                                          onsubmit="return confirm({{ Js::from('¿Dar de baja el tratamiento «'.$t->nombre.'»? Ya no se ofrecerá al programar citas.') }});">
                                                        @csrf
                                                        <input type="hidden" name="id" value="{{ $t->id }}">
                                                        <x-danger-button>Dar de baja</x-danger-button>
                                                    </form>
                                                @else
                                                    <form method="POST" action="{{ route('tratamientos.habilitar') }}">
                                                        @csrf
                                                        <input type="hidden" name="id" value="{{ $t->id }}">
                                                        <x-secondary-button type="submit">Habilitar</x-secondary-button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                            No se encontraron tratamientos con los filtros seleccionados.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{ $tratamientos->links() }}
                </div>
            </div>

            <!-- Formulario Reactivo de Registro / Modificación (CU15 pasos 4 y 5) -->
            <div x-ref="formulario" x-show="abierto" x-cloak class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg border border-gray-100 dark:border-gray-700">
                <section>
                    <header class="flex justify-between items-start">
                        <div>
                            <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100"
                                x-text="form.id ? 'Modificar Tratamiento' : 'Registrar Nuevo Tratamiento'"></h2>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                Ingrese las especificaciones clínicas, la especialidad y la duración estimada para la agenda.
                            </p>
                        </div>
                        <button type="button" @click="abierto = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 text-sm font-semibold">
                            ✕ Cerrar
                        </button>
                    </header>

                    @if ($volvioFormulario && $errors->any())
                        <div class="mt-4 rounded-md bg-red-50 dark:bg-red-900/30 p-4 text-sm font-medium text-red-800 dark:text-red-200">
                            Por favor corrija los campos observados a continuación.
                        </div>
                    @endif

                    <form method="POST" :action="form.id ? {{ Js::from(route('tratamientos.modificar')) }} : {{ Js::from(route('tratamientos.guardar')) }}" class="mt-6 space-y-6">
                        @csrf
                        <input type="hidden" name="formulario" value="datos">
                        <input type="hidden" name="id" :value="form.id" :disabled="! form.id">

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <!-- Nombre -->
                            <div class="sm:col-span-2">
                                <x-input-label for="nombre" value="Nombre del Tratamiento" />
                                <x-text-input id="nombre" name="nombre" type="text" class="mt-1 block w-full" x-model="form.nombre" maxlength="60" required placeholder="Ej: Calza dental en resina fotocurable" />
                                @if ($volvioFormulario) <x-input-error :messages="$errors->get('nombre')" class="mt-2" /> @endif
                            </div>

                            <!-- Especialidad -->
                            <div>
                                <x-input-label for="especialidad_id" value="Especialidad Odontológica" />
                                <select id="especialidad_id" name="especialidad_id" class="{{ $claseCampo }}" x-model="form.especialidad_id" required>
                                    <option value="">Seleccione una especialidad...</option>
                                    @foreach ($especialidades as $esp)
                                        <option value="{{ $esp->id }}">{{ $esp->nombre }}</option>
                                    @endforeach
                                </select>
                                @if ($volvioFormulario) <x-input-error :messages="$errors->get('especialidad_id')" class="mt-2" /> @endif
                            </div>

                            <!-- Duración estimada en minutos (RN-08) -->
                            <div>
                                <x-input-label for="duracion_estimada_minutos" value="Duración Estimada (Minutos para la agenda)" />
                                <x-text-input id="duracion_estimada_minutos" name="duracion_estimada_minutos" type="number" min="5" max="480" step="5" class="mt-1 block w-full" x-model="form.duracion_estimada_minutos" required />
                                <span class="text-xs text-gray-500 dark:text-gray-400 mt-1 block">Esta duración se propondrá automáticamente al agendar citas.</span>
                                @if ($volvioFormulario) <x-input-error :messages="$errors->get('duracion_estimada_minutos')" class="mt-2" /> @endif
                            </div>

                            <!-- Sesiones previstas -->
                            <div>
                                <x-input-label for="numero_sesiones_previstas" value="Número de Sesiones Previstas" />
                                <x-text-input id="numero_sesiones_previstas" name="numero_sesiones_previstas" type="number" min="1" max="50" class="mt-1 block w-full" x-model="form.numero_sesiones_previstas" required />
                                @if ($volvioFormulario) <x-input-error :messages="$errors->get('numero_sesiones_previstas')" class="mt-2" /> @endif
                            </div>

                            <!-- Requiere varias sesiones -->
                            <div class="flex items-center pt-6">
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="requiere_sesiones" value="1" x-model="form.requiere_sesiones" class="rounded border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600">
                                    <span class="ms-2 text-sm text-gray-700 dark:text-gray-300 font-medium">Tratamiento de múltiples sesiones clínicas</span>
                                </label>
                            </div>

                            <!-- Descripción -->
                            <div class="sm:col-span-2">
                                <x-input-label for="descripcion" value="Descripción / Protocolo Clínico (Opcional)" />
                                <textarea id="descripcion" name="descripcion" rows="3" class="{{ $claseCampo }}" x-model="form.descripcion" maxlength="500" placeholder="Detalles de indicaciones clínicas, materiales necesarios o notas adicionales..."></textarea>
                                @if ($volvioFormulario) <x-input-error :messages="$errors->get('descripcion')" class="mt-2" /> @endif
                            </div>
                        </div>

                        <div class="flex items-center gap-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                            <x-primary-button>
                                <span x-text="form.id ? 'Guardar Cambios' : 'Registrar Tratamiento'"></span>
                            </x-primary-button>
                            <x-secondary-button type="button" @click="abierto = false">
                                Cancelar
                            </x-secondary-button>
                        </div>
                    </form>
                </section>
            </div>

        </div>
    </div>
</x-app-layout>
