{{-- CU10 Programar cita (Odontólogo / Administrador) --}}
@php
    $claseCampo = 'mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm';
    $conflicto = session('error_conflicto');
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <div>
                <nav class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400 mb-1">
                    <a href="{{ route('agenda.index') }}" class="hover:underline">Agenda del Sillón</a>
                    <span>/</span>
                    <span class="text-indigo-600 dark:text-indigo-400 font-semibold">Programar Cita</span>
                </nav>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Programar Cita Odontológica (CU10)
                </h2>
            </div>
            <a href="{{ route('agenda.index') }}" class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">
                &larr; Volver a la Agenda
            </a>
        </div>
    </x-slot>

    <div class="py-6 sm:py-12"
         x-data="{
            tratamientosMap: {{ Js::from($tratamientos->keyBy('id')->map(fn ($t) => ['duracion' => $t->duracion_estimada_minutos, 'nombre' => $t->nombre])) }},
            tratamientoId: '{{ old('tratamiento_id', '') }}',
            duracion: {{ old('duracion_minutos', 30) }},
            horaInicio: '{{ old('hora_inicio', $horaInicial) }}',
            motivo: '{{ old('motivo', '') }}',
            horaFin: '--:--',

            actualizarTratamiento() {
                if (this.tratamientoId && this.tratamientosMap[this.tratamientoId]) {
                    this.duracion = this.tratamientosMap[this.tratamientoId].duracion;
                    if (!this.motivo) {
                        this.motivo = this.tratamientosMap[this.tratamientoId].nombre;
                    }
                }
                this.calcularFin();
            },

            calcularFin() {
                if (!this.horaInicio) return;
                const partes = this.horaInicio.split(':');
                if (partes.length < 2) return;
                let h = parseInt(partes[0]);
                let m = parseInt(partes[1]);
                let totalMin = (h * 60) + m + parseInt(this.duracion || 0);
                let finH = Math.floor(totalMin / 60) % 24;
                let finM = totalMin % 60;
                this.horaFin = String(finH).padStart(2, '0') + ':' + String(finM).padStart(2, '0');
            },

            usarFranja(inicio) {
                this.horaInicio = inicio;
                this.calcularFin();
            },

            init() {
                this.calcularFin();
            }
         }">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Alerta de Conflicto de Horario (RN-07 / Flujo alternativo A2) -->
            @if ($conflicto)
                <div class="rounded-xl border-2 border-red-400 dark:border-red-600 bg-red-50 dark:bg-red-950/50 p-5 shadow-sm space-y-3">
                    <div class="flex items-start gap-3">
                        <span class="text-2xl">⚠️</span>
                        <div>
                            <h3 class="text-base font-bold text-red-900 dark:text-red-200">
                                Horario no disponible — Conflicto de Sillón Único (RN-07)
                            </h3>
                            <p class="text-sm text-red-800 dark:text-red-300 mt-1">
                                El consultorio solo cuenta con un sillón dental. La franja solicitada se cruza con la cita programada del paciente:
                                <strong class="font-semibold">{{ $conflicto['paciente'] }}</strong> 
                                (Dr. {{ $conflicto['odontologo'] }}) de 
                                <span class="font-mono bg-red-200 dark:bg-red-800 px-1.5 py-0.5 rounded text-red-900 dark:text-red-100 font-bold">
                                    {{ $conflicto['hora_inicio'] }} a {{ $conflicto['hora_fin'] }}
                                </span>.
                            </p>
                        </div>
                    </div>

                    @if (!empty($conflicto['franjas_libres']))
                        <div class="pt-3 border-t border-red-200 dark:border-red-800/60">
                            <p class="text-xs font-bold uppercase tracking-wider text-red-900 dark:text-red-200 mb-2">
                                💡 Franjas libres más cercanas disponibles en esta misma fecha:
                            </p>
                            <div class="flex flex-wrap gap-2">
                                @foreach ($conflicto['franjas_libres'] as $franja)
                                    <button type="button" @click="usarFranja('{{ $franja['inicio'] }}')"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white dark:bg-gray-800 border border-red-300 dark:border-red-700 text-xs font-semibold text-gray-800 dark:text-gray-200 hover:bg-red-100 dark:hover:bg-red-900/40 transition shadow-sm">
                                        <span>⏰ {{ $franja['inicio'] }} - {{ $franja['fin'] }}</span>
                                        <span class="text-[10px] text-indigo-600 dark:text-indigo-400 font-bold">(Seleccionar)</span>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Formulario Principal -->
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-xl p-5 sm:p-8 border border-gray-100 dark:border-gray-700">
                <form method="POST" action="{{ route('citas.guardar') }}" class="space-y-6">
                    @csrf

                    <!-- Sección 1: Paciente y Odontólogo -->
                    <div class="border-b border-gray-100 dark:border-gray-700 pb-6">
                        <h3 class="text-base font-bold text-gray-900 dark:text-gray-100 mb-4 flex items-center gap-2">
                            <span>1. Paciente y Profesional Responsable</span>
                        </h3>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <!-- Paciente -->
                            <div>
                                <x-input-label for="paciente_id" value="Paciente" />
                                <select id="paciente_id" name="paciente_id" class="{{ $claseCampo }}" required>
                                    <option value="">Seleccione al paciente...</option>
                                    @foreach ($pacientes as $paciente)
                                        <option value="{{ $paciente->id }}" @selected((string)old('paciente_id', $pacientePreseleccionado?->id) === (string)$paciente->id)>
                                            {{ $paciente->apellidos }}, {{ $paciente->nombres }} (CI: {{ $paciente->ci }})
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('paciente_id')" class="mt-2" />
                            </div>

                            <!-- Odontólogo -->
                            <div>
                                <x-input-label for="odontologo_id" value="Odontólogo Responsable" />
                                <select id="odontologo_id" name="odontologo_id" class="{{ $claseCampo }}" required>
                                    <option value="">Seleccione al odontólogo...</option>
                                    @foreach ($odontologos as $odonto)
                                        <option value="{{ $odonto->id }}" @selected((string)old('odontologo_id', auth()->id()) === (string)$odonto->id)>
                                            Dr. {{ $odonto->apellidos }}, {{ $odonto->nombres }} ({{ $odonto->especialidad?->nombre ?? ucfirst($odonto->rol) }})
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('odontologo_id')" class="mt-2" />
                            </div>
                        </div>
                    </div>

                    <!-- Sección 2: Tratamiento y Horario (RN-07, RN-08, RN-09) -->
                    <div class="border-b border-gray-100 dark:border-gray-700 pb-6">
                        <h3 class="text-base font-bold text-gray-900 dark:text-gray-100 mb-4 flex items-center gap-2">
                            <span>2. Tratamiento y Programación de Horario (Sillón Único)</span>
                        </h3>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                            <!-- Tratamiento del catálogo -->
                            <div class="sm:col-span-3">
                                <x-input-label for="tratamiento_id" value="Tratamiento Solicitado (Catálogo)" />
                                <select id="tratamiento_id" name="tratamiento_id" class="{{ $claseCampo }}"
                                        x-model="tratamientoId" @change="actualizarTratamiento()" required>
                                    <option value="">Seleccione el tratamiento clínico...</option>
                                    @foreach ($tratamientos as $t)
                                        <option value="{{ $t->id }}">
                                            {{ $t->nombre }} — {{ $t->especialidad->nombre }} (Estimado: {{ $t->duracion_estimada_minutos }} min)
                                        </option>
                                    @endforeach
                                </select>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    Al elegir un tratamiento se propondrá automáticamente su duración en minutos (RN-08).
                                </p>
                                <x-input-error :messages="$errors->get('tratamiento_id')" class="mt-2" />
                            </div>

                            <!-- Fecha -->
                            <div>
                                <x-input-label for="fecha" value="Fecha de la Cita" />
                                <x-text-input id="fecha" name="fecha" type="date" class="mt-1 block w-full"
                                              :value="old('fecha', $fechaInicial)" min="{{ now()->toDateString() }}" required />
                                <x-input-error :messages="$errors->get('fecha')" class="mt-2" />
                            </div>

                            <!-- Hora de Inicio -->
                            <div>
                                <x-input-label for="hora_inicio" value="Hora de Inicio" />
                                <x-text-input id="hora_inicio" name="hora_inicio" type="time" class="mt-1 block w-full"
                                              x-model="horaInicio" @input="calcularFin()" required />
                                <x-input-error :messages="$errors->get('hora_inicio')" class="mt-2" />
                            </div>

                            <!-- Duración en Minutos (RN-08) -->
                            <div>
                                <x-input-label for="duracion_minutos" value="Duración (Minutos)" />
                                <x-text-input id="duracion_minutos" name="duracion_minutos" type="number" min="5" max="360" step="5"
                                              class="mt-1 block w-full" x-model="duracion" @input="calcularFin()" required />
                                <x-input-error :messages="$errors->get('duracion_minutos')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Tarjeta visual de cálculo de finalización (RN-09) -->
                        <div class="mt-4 p-4 rounded-xl bg-indigo-50 dark:bg-indigo-950/40 border border-indigo-200 dark:border-indigo-800 flex flex-col sm:flex-row justify-between items-center gap-3">
                            <div class="text-xs text-indigo-900 dark:text-indigo-200">
                                <strong>RN-09 (Hora de fin calculada):</strong> La cita bloqueará el sillón dental desde las <span class="font-bold underline" x-text="horaInicio"></span> hasta las <span class="font-bold underline" x-text="horaFin"></span>.
                            </div>
                            <div class="text-sm font-bold text-indigo-700 dark:text-indigo-300">
                                Término previsto: <span class="text-base font-mono bg-white dark:bg-gray-800 px-2 py-1 rounded shadow-sm" x-text="horaFin"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Sección 3: Motivo y Observaciones -->
                    <div>
                        <h3 class="text-base font-bold text-gray-900 dark:text-gray-100 mb-4 flex items-center gap-2">
                            <span>3. Motivo y Notas Clínicas</span>
                        </h3>

                        <div class="space-y-4">
                            <div>
                                <x-input-label for="motivo" value="Motivo Principal de la Consulta" />
                                <x-text-input id="motivo" name="motivo" type="text" class="mt-1 block w-full"
                                              x-model="motivo" maxlength="150" required placeholder="Ej: Control de ortodoncia, dolor agudo molar, limpieza..." />
                                <x-input-error :messages="$errors->get('motivo')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="observaciones" value="Observaciones Previas (Opcional)" />
                                <textarea id="observaciones" name="observaciones" rows="2" class="{{ $claseCampo }}"
                                          maxlength="500" placeholder="Alergias mencionadas, recomendaciones pre-operatorias o notas adicionales...">{{ old('observaciones') }}</textarea>
                                <x-input-error :messages="$errors->get('observaciones')" class="mt-2" />
                            </div>
                        </div>
                    </div>

                    <!-- Botones de Acción -->
                    <div class="flex flex-wrap items-center gap-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                        <x-primary-button class="px-6 py-2.5 text-sm">
                            📅 Confirmar y Programar Cita
                        </x-primary-button>
                        <a href="{{ route('agenda.index') }}" class="px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 transition">
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
