{{-- CU13 Consultar agenda del sillón único (RF-72) --}}
@php
    $claseCampo = 'block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm text-sm';
    $fechaStr = $fechaSeleccionada->toDateString();
    $diaAnterior = (clone $fechaSeleccionada)->subDay()->toDateString();
    $diaSiguiente = (clone $fechaSeleccionada)->addDay()->toDateString();
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <nav class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400 mb-1">
                    <a href="{{ route('paquetes.mostrar', 'p3') }}" class="hover:underline">Paquete P3 (Pacientes y Agenda)</a>
                    <span>/</span>
                    <span class="text-indigo-600 dark:text-indigo-400 font-semibold">Agenda Clínica</span>
                </nav>
                <h2 class="font-bold text-xl sm:text-2xl text-gray-900 dark:text-gray-100 leading-tight flex items-center gap-2">
                    <span>📅</span>
                    <span>Agenda del Sillón Único</span>
                </h2>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('citas.crear', ['fecha' => $fechaStr]) }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-semibold shadow-sm transition">
                    <span>+ Nueva Cita</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6 sm:py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Mensajes flash -->
            @if (session('exito'))
                <div class="rounded-md bg-green-50 dark:bg-green-900/30 p-4 text-sm font-medium text-green-800 dark:text-green-200 border border-green-200 dark:border-green-800">
                    {{ session('exito') }}
                </div>
            @endif

            <!-- Barra Superior de Navegación Temporal y Filtros -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-4 sm:p-5 space-y-4">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                    
                    <!-- Navegación de Fecha (Día Anterior, Hoy, Día Siguiente) -->
                    <div class="flex flex-wrap items-center gap-2">
                        <a href="{{ route('agenda.index', array_merge(request()->query(), ['fecha' => $diaAnterior])) }}"
                           class="px-3 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-lg text-sm font-medium transition">
                            &larr; Anterior
                        </a>
                        <a href="{{ route('agenda.index', array_merge(request()->query(), ['fecha' => now()->toDateString()])) }}"
                           class="px-3 py-2 bg-indigo-50 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-300 hover:bg-indigo-100 dark:hover:bg-indigo-900/60 rounded-lg text-sm font-bold transition">
                            Hoy
                        </a>
                        <a href="{{ route('agenda.index', array_merge(request()->query(), ['fecha' => $diaSiguiente])) }}"
                           class="px-3 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-lg text-sm font-medium transition">
                            Siguiente &rarr;
                        </a>

                        <form method="GET" action="{{ route('agenda.index') }}" class="inline-flex items-center ms-1">
                            <input type="hidden" name="vista" value="{{ $vista }}">
                            @if ($odontologoId) <input type="hidden" name="odontologo_id" value="{{ $odontologoId }}"> @endif
                            @if ($estadoId) <input type="hidden" name="estado_id" value="{{ $estadoId }}"> @endif
                            <input type="date" name="fecha" value="{{ $fechaStr }}" onchange="this.form.submit()"
                                   class="text-xs sm:text-sm border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 rounded-lg py-1.5 focus:ring-indigo-500">
                        </form>
                    </div>

                    <!-- Selector de Vista (Diaria vs Semanal) -->
                    <div class="flex items-center rounded-lg bg-gray-100 dark:bg-gray-700/60 p-1 self-start sm:self-auto">
                        <a href="{{ route('agenda.index', array_merge(request()->query(), ['vista' => 'diaria'])) }}"
                           class="px-3.5 py-1.5 rounded-md text-xs font-semibold transition {{ $vista === 'diaria' ? 'bg-white dark:bg-gray-800 text-indigo-600 dark:text-indigo-400 shadow-sm' : 'text-gray-600 dark:text-gray-300 hover:text-gray-900' }}">
                            Vista Diaria
                        </a>
                        <a href="{{ route('agenda.index', array_merge(request()->query(), ['vista' => 'semanal'])) }}"
                           class="px-3.5 py-1.5 rounded-md text-xs font-semibold transition {{ $vista === 'semanal' ? 'bg-white dark:bg-gray-800 text-indigo-600 dark:text-indigo-400 shadow-sm' : 'text-gray-600 dark:text-gray-300 hover:text-gray-900' }}">
                            Vista Semanal
                        </a>
                    </div>
                </div>

                <!-- Filtros Secundarios (Odontólogo y Estado) -->
                <form method="GET" action="{{ route('agenda.index') }}" class="pt-3 border-t border-gray-100 dark:border-gray-700 flex flex-wrap items-center gap-3">
                    <input type="hidden" name="fecha" value="{{ $fechaStr }}">
                    <input type="hidden" name="vista" value="{{ $vista }}">

                    <div class="flex-1 min-w-[200px]">
                        <select name="odontologo_id" onchange="this.form.submit()" class="{{ $claseCampo }} text-xs">
                            <option value="">Todos los odontólogos</option>
                            @foreach ($odontologos as $od)
                                <option value="{{ $od->id }}" @selected((string)$odontologoId === (string)$od->id)>
                                    Dr. {{ $od->apellidos }}, {{ $od->nombres }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex-1 min-w-[180px]">
                        <select name="estado_id" onchange="this.form.submit()" class="{{ $claseCampo }} text-xs">
                            <option value="">Todos los estados</option>
                            @foreach ($estados as $est)
                                <option value="{{ $est->id }}" @selected((string)$estadoId === (string)$est->id)>
                                    {{ ucfirst($est->nombre) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    @if ($odontologoId || $estadoId)
                        <a href="{{ route('agenda.index', ['fecha' => $fechaStr, 'vista' => $vista]) }}" class="text-xs text-gray-500 hover:underline">
                            Limpiar filtros
                        </a>
                    @endif
                </form>
            </div>

            <!-- Encabezado de la Fecha Activa -->
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-gray-100">
                        @if ($vista === 'diaria')
                            {{ ucfirst($fechaSeleccionada->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY')) }}
                        @else
                            Semana del {{ $inicioSemana->format('d/m') }} al {{ $finSemana->format('d/m/Y') }}
                        @endif
                    </h3>
                    <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                        Consultorio Odontológico DentalRox &bull; Sillón Dental Único (RN-07)
                    </p>
                </div>
            </div>

            <!-- VISTA 1: AGENDA DIARIA POR HORAS -->
            @if ($vista === 'diaria')
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 divide-y divide-gray-100 dark:divide-gray-700 overflow-hidden">
                    @foreach ($bloquesHorarios as $bloque)
                        <div class="p-4 sm:p-5 flex flex-col sm:flex-row items-start sm:items-center gap-4 hover:bg-gray-50/50 dark:hover:bg-gray-700/20 transition">
                            <!-- Columna Hora -->
                            <div class="sm:w-28 shrink-0 flex items-center sm:flex-col sm:items-start gap-2">
                                <span class="font-mono text-base sm:text-lg font-bold text-gray-900 dark:text-gray-100">
                                    {{ $bloque['hora'] }}
                                </span>
                                <span class="text-xs text-gray-400 dark:text-gray-500 font-mono">
                                    a {{ $bloque['hora_fin_bloque'] }}
                                </span>
                            </div>

                            <!-- Columna Contenido del Bloque -->
                            <div class="flex-1 w-full">
                                @if ($bloque['citas']->isNotEmpty())
                                    <div class="space-y-3">
                                        @foreach ($bloque['citas'] as $cita)
                                            <div class="p-3.5 sm:p-4 rounded-xl border flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-sm transition
                                                {{ $cita->esProgramada() ? 'bg-blue-50/80 dark:bg-blue-950/40 border-blue-200 dark:border-blue-800/80' : '' }}
                                                {{ $cita->esAtendida() ? 'bg-green-50/80 dark:bg-green-950/40 border-green-200 dark:border-green-800/80' : '' }}
                                                {{ $cita->esCancelada() ? 'bg-gray-100 dark:bg-gray-700/60 border-gray-200 dark:border-gray-600' : '' }}
                                                {{ $cita->esNoAsistida() ? 'bg-amber-50/80 dark:bg-amber-950/40 border-amber-200 dark:border-amber-800/80' : '' }}
                                            ">
                                                <div>
                                                    <div class="flex flex-wrap items-center gap-2">
                                                        <span class="font-mono text-xs font-bold text-indigo-700 dark:text-indigo-300 bg-white dark:bg-gray-800 px-2 py-0.5 rounded shadow-xs">
                                                            ⏰ {{ $cita->horaInicioFormato() }} - {{ $cita->horaFinFormato() }} ({{ $cita->duracion_minutos }} min)
                                                        </span>

                                                        @if ($cita->esProgramada())
                                                            <span class="inline-flex rounded-full bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 px-2 py-0.5 text-[11px] font-bold uppercase">
                                                                Programada
                                                            </span>
                                                        @elseif ($cita->esAtendida())
                                                            <span class="inline-flex rounded-full bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 px-2 py-0.5 text-[11px] font-bold uppercase">
                                                                Atendida
                                                            </span>
                                                        @elseif ($cita->esCancelada())
                                                            <span class="inline-flex rounded-full bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-200 px-2 py-0.5 text-[11px] font-bold uppercase">
                                                                Cancelada
                                                            </span>
                                                        @elseif ($cita->esNoAsistida())
                                                            <span class="inline-flex rounded-full bg-amber-100 dark:bg-amber-900 text-amber-800 dark:text-amber-200 px-2 py-0.5 text-[11px] font-bold uppercase">
                                                                No asistió
                                                            </span>
                                                        @endif
                                                    </div>

                                                    <h4 class="font-bold text-base text-gray-900 dark:text-gray-100 mt-1.5">
                                                        {{ $cita->paciente->apellidos }}, {{ $cita->paciente->nombres }}
                                                    </h4>
                                                    <p class="text-xs text-gray-600 dark:text-gray-300 mt-0.5">
                                                        Motivo: <strong class="font-semibold">{{ $cita->motivo }}</strong> &bull; Atiende: Dr. {{ $cita->odontologo->apellidos }}
                                                    </p>
                                                </div>

                                                <div class="shrink-0 flex items-center gap-2">
                                                    <a href="{{ route('citas.mostrar', $cita->id) }}"
                                                       class="w-full sm:w-auto text-center px-3 py-1.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-xs font-semibold text-gray-800 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 transition shadow-xs">
                                                        Ver detalle / Gestionar &rarr;
                                                    </a>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <!-- Franja horaria libre del sillón -->
                                    <div class="flex items-center justify-between py-1">
                                        <div class="flex items-center gap-2 text-xs text-gray-400 dark:text-gray-500">
                                            <span>🪑 Sillón libre</span>
                                        </div>
                                        <a href="{{ route('citas.crear', ['fecha' => $fechaStr, 'hora' => $bloque['hora']]) }}"
                                           class="inline-flex items-center gap-1 text-xs text-indigo-600 dark:text-indigo-400 font-semibold hover:underline">
                                            <span>+ Agendar a las {{ $bloque['hora'] }}</span>
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- VISTA 2: AGENDA SEMANAL COMPLETA -->
            @if ($vista === 'semanal')
                <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm bg-white dark:bg-gray-800">
                    <div class="min-w-[900px] grid grid-cols-6 divide-x divide-gray-100 dark:divide-gray-700">
                        @foreach ($diasSemana as $dia)
                            @php
                                $citasDelDiaSemana = $citas->get($dia['fecha'], collect());
                            @endphp
                            <div class="min-h-[450px] flex flex-col {{ $dia['es_hoy'] ? 'bg-indigo-50/20 dark:bg-indigo-950/20' : '' }}">
                                <!-- Cabecera del día -->
                                <div class="p-3 border-b border-gray-100 dark:border-gray-700 text-center {{ $dia['es_hoy'] ? 'bg-indigo-50 dark:bg-indigo-900/40 text-indigo-800 dark:text-indigo-200 font-bold' : 'bg-gray-50 dark:bg-gray-800/80 text-gray-700 dark:text-gray-300' }}">
                                    <div class="text-xs uppercase tracking-wider font-semibold">{{ $dia['dia_nombre'] }}</div>
                                    <div class="text-base font-bold mt-0.5">{{ $dia['dia_numero'] }}</div>
                                </div>

                                <!-- Citas del día -->
                                <div class="p-2.5 flex-1 space-y-2">
                                    @forelse ($citasDelDiaSemana as $cita)
                                        <a href="{{ route('citas.mostrar', $cita->id) }}"
                                           class="block p-2 rounded-lg border text-xs transition hover:shadow-md
                                            {{ $cita->esProgramada() ? 'bg-blue-50 dark:bg-blue-950/60 border-blue-200 dark:border-blue-800 text-blue-900 dark:text-blue-100' : '' }}
                                            {{ $cita->esAtendida() ? 'bg-green-50 dark:bg-green-950/60 border-green-200 dark:border-green-800 text-green-900 dark:text-green-100' : '' }}
                                            {{ $cita->esCancelada() ? 'bg-gray-100 dark:bg-gray-700 border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-300 opacity-60' : '' }}
                                            {{ $cita->esNoAsistida() ? 'bg-amber-50 dark:bg-amber-950/60 border-amber-200 dark:border-amber-800 text-amber-900 dark:text-amber-100' : '' }}
                                           ">
                                            <div class="font-mono font-bold">{{ $cita->horaInicioFormato() }} - {{ $cita->horaFinFormato() }}</div>
                                            <div class="font-semibold truncate mt-0.5">{{ $cita->paciente->apellidos }}, {{ $cita->paciente->nombres }}</div>
                                            <div class="text-[10px] text-gray-500 dark:text-gray-400 truncate">{{ $cita->motivo }}</div>
                                        </a>
                                    @empty
                                        <div class="text-center py-8 text-gray-400 dark:text-gray-500 text-xs">
                                            Sin citas
                                        </div>
                                    @endforelse
                                </div>

                                <!-- Botón agendar en este día -->
                                <div class="p-2 border-t border-gray-100 dark:border-gray-700/60 text-center">
                                    <a href="{{ route('citas.crear', ['fecha' => $dia['fecha']]) }}"
                                       class="text-[11px] text-indigo-600 dark:text-indigo-400 font-semibold hover:underline block">
                                        + Agendar cita
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
