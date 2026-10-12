{{-- CU11 Reprogramar o cancelar cita y CU12 Actualizar estado de cita --}}
@php
    $claseCampo = 'mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm';
    $esHoyOAnterior = $cita->fecha->lte(now()->startOfDay());
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <div>
                <nav class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400 mb-1">
                    <a href="{{ route('agenda.index') }}" class="hover:underline">Agenda</a>
                    <span>/</span>
                    <span class="text-indigo-600 dark:text-indigo-400 font-semibold">Cita #{{ $cita->id }}</span>
                </nav>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Detalle de la Cita Médica #{{ $cita->id }}
                </h2>
            </div>
            <a href="{{ route('agenda.index', ['fecha' => $cita->fecha->toDateString()]) }}" class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">
                &larr; Volver a la Agenda del {{ $cita->fecha->format('d/m/Y') }}
            </a>
        </div>
    </x-slot>

    <div class="py-6 sm:py-12"
         x-data="{
            modalReprogramar: false,
            modalCancelar: false,
            modalEstado: false,
            nuevoEstado: 'atendida'
         }">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Mensajes de éxito -->
            @if (session('exito'))
                <div class="rounded-md bg-green-50 dark:bg-green-900/30 p-4 text-sm font-medium text-green-800 dark:text-green-200 border border-green-200 dark:border-green-800">
                    {{ session('exito') }}
                </div>
            @endif

            <!-- Mensajes de error -->
            @if ($errors->any())
                <div class="rounded-md bg-red-50 dark:bg-red-900/30 p-4 text-sm font-medium text-red-800 dark:text-red-200 border border-red-200 dark:border-red-800">
                    <ul class="space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Tarjeta Principal de la Cita -->
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                <!-- Encabezado con Estado -->
                <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <span class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Fecha agendada</span>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-0.5">
                            {{ ucfirst($cita->fecha->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY')) }}
                        </h3>
                        <p class="text-sm font-mono text-indigo-600 dark:text-indigo-400 mt-1 font-semibold">
                            ⏰ {{ $cita->horaInicioFormato() }} a {{ $cita->horaFinFormato() }} ({{ $cita->duracion_minutos }} minutos de sillón)
                        </p>
                    </div>

                    <div>
                        @if ($cita->esProgramada())
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-blue-100 dark:bg-blue-900/60 text-blue-800 dark:text-blue-200 border border-blue-300 dark:border-blue-700">
                                ⏳ Programada
                            </span>
                        @elseif ($cita->esAtendida())
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-green-100 dark:bg-green-900/60 text-green-800 dark:text-green-200 border border-green-300 dark:border-green-700">
                                ✓ Atendida
                            </span>
                        @elseif ($cita->esCancelada())
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 border border-gray-400 dark:border-gray-600">
                                ✕ Cancelada
                            </span>
                        @elseif ($cita->esNoAsistida())
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-amber-100 dark:bg-amber-900/60 text-amber-800 dark:text-amber-200 border border-amber-300 dark:border-amber-700">
                                ⚠ No asistió
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Detalles Clínicos -->
                <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-6 text-sm">
                    <!-- Datos del Paciente -->
                    <div class="p-4 rounded-lg bg-gray-50 dark:bg-gray-900/40 border border-gray-100 dark:border-gray-800">
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Paciente</dt>
                        <dd class="mt-2 text-base font-bold text-gray-900 dark:text-gray-100">
                            <a href="{{ route('pacientes.mostrar', $cita->paciente_id) }}" class="text-blue-600 dark:text-blue-400 hover:underline">
                                {{ $cita->paciente->apellidos }}, {{ $cita->paciente->nombres }}
                            </a>
                        </dd>
                        <p class="text-xs text-gray-600 dark:text-gray-300 mt-1">CI: {{ $cita->paciente->ci }} &bull; Edad: {{ $cita->paciente->edad() }} años</p>
                        <p class="text-xs text-gray-600 dark:text-gray-300 mt-0.5">Teléfono: {{ $cita->paciente->telefono ?? '—' }}</p>
                    </div>

                    <!-- Datos del Odontólogo -->
                    <div class="p-4 rounded-lg bg-gray-50 dark:bg-gray-900/40 border border-gray-100 dark:border-gray-800">
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Odontólogo Responsable</dt>
                        <dd class="mt-2 text-base font-bold text-gray-900 dark:text-gray-100">
                            Dr. {{ $cita->odontologo->apellidos }}, {{ $cita->odontologo->nombres }}
                        </dd>
                        <p class="text-xs text-gray-600 dark:text-gray-300 mt-1">
                            Especialidad: {{ $cita->odontologo->especialidad?->nombre ?? 'Odontología General' }}
                        </p>
                        <p class="text-xs text-gray-600 dark:text-gray-300 mt-0.5">Matrícula: {{ $cita->odontologo->matricula_profesional }}</p>
                    </div>

                    <!-- Motivo de la Cita -->
                    <div class="sm:col-span-2 p-4 rounded-lg bg-gray-50 dark:bg-gray-900/40 border border-gray-100 dark:border-gray-800">
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Motivo de la Cita</dt>
                        <dd class="mt-2 text-base font-semibold text-gray-900 dark:text-gray-100">{{ $cita->motivo }}</dd>
                    </div>

                    <!-- Observaciones e Historial -->
                    @if ($cita->observaciones)
                        <div class="sm:col-span-2 p-4 rounded-lg bg-gray-50 dark:bg-gray-900/40 border border-gray-100 dark:border-gray-800">
                            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Historial y Observaciones</dt>
                            <dd class="mt-2 text-sm text-gray-700 dark:text-gray-300 leading-relaxed font-mono">
                                {{ $cita->observaciones }}
                            </dd>
                        </div>
                    @endif
                </div>

                <!-- Barra de Acciones (Solo para citas programadas) -->
                @if ($cita->esProgramada())
                    <div class="p-6 bg-gray-50/50 dark:bg-gray-900/40 border-t border-gray-100 dark:border-gray-700 flex flex-wrap items-center gap-3">
                        <!-- Botón Actualizar Estado (CU12) -->
                        <x-primary-button type="button" @click="modalEstado = true">
                            ✓ Actualizar Estado (Atendida / No asistida)
                        </x-primary-button>

                        <!-- Botón Reprogramar (CU11) -->
                        <x-secondary-button type="button" @click="modalReprogramar = true">
                            🔄 Reprogramar Cita
                        </x-secondary-button>

                        <!-- Botón Cancelar (CU11) -->
                        <x-danger-button type="button" @click="modalCancelar = true">
                            ✕ Cancelar Cita
                        </x-danger-button>
                    </div>
                @endif
            </div>

            <!-- MODAL 1: Reprogramar Cita (CU11) -->
            <div x-show="modalReprogramar" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-black/60 flex items-center justify-center p-4">
                <div class="bg-white dark:bg-gray-800 rounded-xl max-w-lg w-full p-6 shadow-2xl border border-gray-100 dark:border-gray-700 space-y-4" @click.away="modalReprogramar = false">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">Reprogramar Cita Médica (CU11)</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        Al reprogramar, se verificará que la nueva franja esté libre (RN-07) y se guardará el registro histórico de la cita original.
                    </p>

                    <form method="POST" action="{{ route('citas.reprogramar') }}" class="space-y-4">
                        @csrf
                        <input type="hidden" name="cita_id" value="{{ $cita->id }}">

                        <div>
                            <x-input-label for="nueva_fecha" value="Nueva Fecha" />
                            <x-text-input id="nueva_fecha" name="nueva_fecha" type="date" class="mt-1 block w-full"
                                          min="{{ now()->toDateString() }}" value="{{ now()->toDateString() }}" required />
                        </div>

                        <div>
                            <x-input-label for="nueva_hora_inicio" value="Nueva Hora de Inicio" />
                            <x-text-input id="nueva_hora_inicio" name="nueva_hora_inicio" type="time" class="mt-1 block w-full"
                                          value="{{ $cita->horaInicioFormato() }}" required />
                            <span class="text-xs text-gray-500 dark:text-gray-400 mt-1 block">Conservará la misma duración de {{ $cita->duracion_minutos }} min.</span>
                        </div>

                        <div>
                            <x-input-label for="motivo_reprogramacion" value="Motivo del Cambio de Fecha/Hora" />
                            <textarea id="motivo_reprogramacion" name="motivo_reprogramacion" rows="3" class="{{ $claseCampo }}"
                                      required placeholder="Indique la razón informada por el paciente o el profesional..."></textarea>
                        </div>

                        <div class="flex justify-end gap-3 pt-3 border-t border-gray-100 dark:border-gray-700">
                            <x-secondary-button type="button" @click="modalReprogramar = false">Cerrar</x-secondary-button>
                            <x-primary-button>Confirmar Reprogramación</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- MODAL 2: Cancelar Cita (CU11) -->
            <div x-show="modalCancelar" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-black/60 flex items-center justify-center p-4">
                <div class="bg-white dark:bg-gray-800 rounded-xl max-w-lg w-full p-6 shadow-2xl border border-gray-100 dark:border-gray-700 space-y-4" @click.away="modalCancelar = false">
                    <h3 class="text-lg font-bold text-red-600 dark:text-red-400">Cancelar Cita Médica (CU11)</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        La cita cambiará su estado a «cancelada» y la franja del sillón quedará disponible inmediatamente para otros pacientes.
                    </p>

                    <form method="POST" action="{{ route('citas.cancelar') }}" class="space-y-4">
                        @csrf
                        <input type="hidden" name="cita_id" value="{{ $cita->id }}">

                        <div>
                            <x-input-label for="motivo_cancelacion" value="Motivo Obligatorio de Cancelación" />
                            <textarea id="motivo_cancelacion" name="motivo_cancelacion" rows="3" class="{{ $claseCampo }}"
                                      required placeholder="Ej: Paciente solicitó cancelar por viaje, urgencia personal, etc."></textarea>
                        </div>

                        <div class="flex justify-end gap-3 pt-3 border-t border-gray-100 dark:border-gray-700">
                            <x-secondary-button type="button" @click="modalCancelar = false">Volver</x-secondary-button>
                            <x-danger-button>Confirmar Cancelación</x-danger-button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- MODAL 3: Actualizar Estado (CU12) -->
            <div x-show="modalEstado" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-black/60 flex items-center justify-center p-4">
                <div class="bg-white dark:bg-gray-800 rounded-xl max-w-lg w-full p-6 shadow-2xl border border-gray-100 dark:border-gray-700 space-y-4" @click.away="modalEstado = false">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">Actualizar Estado de la Cita (CU12)</h3>

                    @if (!$esHoyOAnterior)
                        <div class="rounded-lg bg-amber-50 dark:bg-amber-950/50 p-3 border border-amber-200 dark:border-amber-800 text-xs text-amber-800 dark:text-amber-200">
                            ⚠️ <strong>Nota:</strong> Esta cita tiene fecha futura ({{ $cita->fecha->format('d/m/Y') }}). De acuerdo a las reglas de negocio, los estados «Atendida» y «No asistida» solo pueden registrarse a partir del día de la cita.
                        </div>
                    @endif

                    <form method="POST" action="{{ route('citas.actualizarEstado') }}" class="space-y-4">
                        @csrf
                        <input type="hidden" name="cita_id" value="{{ $cita->id }}">

                        <div>
                            <x-input-label for="estado_nombre" value="Seleccione el Resultado" />
                            <select id="estado_nombre" name="estado_nombre" class="{{ $claseCampo }}" x-model="nuevoEstado" required>
                                <option value="atendida" @disabled(!$esHoyOAnterior)>✓ Atendida (Paciente asistió y fue atendido)</option>
                                <option value="no asistida" @disabled(!$esHoyOAnterior)>⚠ No asistió (Paciente faltó a la cita)</option>
                                <option value="cancelada">✕ Cancelada (Anular cita)</option>
                            </select>
                        </div>

                        <div>
                            <x-input-label for="observaciones_estado" value="Observaciones / Resumen Clínico (Opcional)" />
                            <textarea id="observaciones_estado" name="observaciones" rows="3" class="{{ $claseCampo }}"
                                      placeholder="Detalles breves de la atención realizada o motivo de la falta..."></textarea>
                        </div>

                        <div class="flex justify-end gap-3 pt-3 border-t border-gray-100 dark:border-gray-700">
                            <x-secondary-button type="button" @click="modalEstado = false">Cerrar</x-secondary-button>
                            <x-primary-button>Guardar Estado</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
