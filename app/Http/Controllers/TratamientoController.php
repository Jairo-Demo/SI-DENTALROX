<?php

namespace App\Http\Controllers;

use App\Models\Especialidad;
use App\Models\Tratamiento;
use App\Services\BitacoraService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * CU15 Gestionar catálogo de tratamientos.
 * Permite al administrador registrar, modificar, consultar y dar de baja lógica
 * los tratamientos ofrecidos por el consultorio (RNF-06: sin eliminación física).
 */
class TratamientoController extends Controller
{
    private const CAMPOS_DATOS = [
        'nombre',
        'descripcion',
        'especialidad_id',
        'duracion_estimada_minutos',
        'numero_sesiones_previstas',
        'requiere_sesiones',
    ];

    /**
     * Lista los tratamientos del catálogo con buscador y filtros (CU15 pasos 1 y 2).
     */
    public function index(Request $request): View
    {
        $request->validate([
            'buscar' => ['nullable', 'string', 'max:100'],
            'especialidad_id' => ['nullable', 'integer', 'exists:especialidades,id'],
            'estado' => ['nullable', Rule::in(['disponible', 'no disponible', 'todos'])],
        ]);

        $buscar = $request->input('buscar');
        $especialidadId = $request->input('especialidad_id');
        $estado = $request->input('estado', 'todos');

        $tratamientos = Tratamiento::with('especialidad')
            ->when($buscar, function ($query, $buscar) {
                $query->where(function ($q) use ($buscar) {
                    $q->where('nombre', 'like', "%{$buscar}%")
                        ->orWhere('descripcion', 'like', "%{$buscar}%");
                });
            })
            ->when($especialidadId, function ($query, $especialidadId) {
                $query->where('especialidad_id', $especialidadId);
            })
            ->when($estado && $estado !== 'todos', function ($query) use ($estado) {
                $query->where('estado', $estado);
            })
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

        $especialidades = Especialidad::orderBy('nombre')->get();

        return view('tratamientos.index', compact('tratamientos', 'especialidades', 'buscar', 'especialidadId', 'estado'));
    }

    /**
     * Muestra la ficha detallada de un tratamiento (CU15 consultar).
     */
    public function show($id): View
    {
        $tratamiento = Tratamiento::with('especialidad')->findOrFail($id);

        return view('tratamientos.show', compact('tratamiento'));
    }

    /**
     * Registra un nuevo tratamiento en el catálogo (CU15 pasos 4 a 6).
     */
    public function store(Request $request): RedirectResponse
    {
        $datos = $request->validate($this->reglasDatos(), $this->mensajes());

        DB::transaction(function () use ($datos, $request) {
            $atributos = Arr::only($datos, self::CAMPOS_DATOS);
            $atributos['requiere_sesiones'] = $request->boolean('requiere_sesiones');
            $atributos['estado'] = 'disponible';

            $tratamiento = Tratamiento::create($atributos);

            BitacoraService::registrar(
                BitacoraService::CREAR_TRATAMIENTO,
                $request->user()->id,
                'tratamientos',
                $tratamiento->id
            );
        });

        return redirect()->route('tratamientos.listar')->with('exito', 'Tratamiento registrado correctamente en el catálogo.');
    }

    /**
     * Modifica los datos de un tratamiento existente (CU15 modificar).
     */
    public function update(Request $request): RedirectResponse
    {
        $request->validate($this->reglaId(), $this->mensajes());
        $id = (int) $request->input('id');

        $datos = $request->validate($this->reglasDatos($id), $this->mensajes());

        $tratamiento = Tratamiento::findOrFail($id);

        DB::transaction(function () use ($tratamiento, $datos, $request) {
            $atributos = Arr::only($datos, self::CAMPOS_DATOS);
            $atributos['requiere_sesiones'] = $request->boolean('requiere_sesiones');

            $tratamiento->update($atributos);

            BitacoraService::registrar(
                BitacoraService::MODIFICAR_TRATAMIENTO,
                $request->user()->id,
                'tratamientos',
                $tratamiento->id
            );
        });

        return redirect()->route('tratamientos.listar')->with('exito', 'Tratamiento actualizado correctamente.');
    }

    /**
     * Da de baja lógica un tratamiento cambiando su estado a 'no disponible' (CU15 paso 7 - RNF-06).
     */
    public function delete(Request $request): RedirectResponse
    {
        $request->validate($this->reglaId(), $this->mensajes());

        $tratamiento = Tratamiento::findOrFail($request->input('id'));

        if ($tratamiento->estado === 'no disponible') {
            return redirect()->back()->withErrors(['operacion' => 'El tratamiento ya se encuentra dado de baja.']);
        }

        DB::transaction(function () use ($tratamiento, $request) {
            $tratamiento->update(['estado' => 'no disponible']);

            BitacoraService::registrar(
                BitacoraService::DESACTIVAR_TRATAMIENTO,
                $request->user()->id,
                'tratamientos',
                $tratamiento->id
            );
        });

        return redirect()->route('tratamientos.listar')->with('exito', 'Tratamiento dado de baja. Ya no se ofrecerá al programar citas.');
    }

    /**
     * Habilita nuevamente un tratamiento previamente dado de baja.
     */
    public function activar(Request $request): RedirectResponse
    {
        $request->validate($this->reglaId(), $this->mensajes());

        $tratamiento = Tratamiento::findOrFail($request->input('id'));

        if ($tratamiento->estado === 'disponible') {
            return redirect()->back()->withErrors(['operacion' => 'El tratamiento ya se encuentra activo y disponible.']);
        }

        DB::transaction(function () use ($tratamiento, $request) {
            $tratamiento->update(['estado' => 'disponible']);

            BitacoraService::registrar(
                BitacoraService::ACTIVAR_TRATAMIENTO,
                $request->user()->id,
                'tratamientos',
                $tratamiento->id
            );
        });

        return redirect()->route('tratamientos.listar')->with('exito', 'Tratamiento habilitado exitosamente para programación de citas.');
    }

    /**
     * Regla de validación del ID recibido en formulario POST.
     */
    private function reglaId(): array
    {
        return ['id' => ['required', 'integer', 'exists:tratamientos,id']];
    }

    /**
     * Reglas de validación para los datos del tratamiento.
     */
    private function reglasDatos(?int $idIgnorar = null): array
    {
        return [
            'nombre' => [
                'required',
                'string',
                'max:60',
                Rule::unique('tratamientos', 'nombre')->ignore($idIgnorar),
            ],
            'descripcion' => ['nullable', 'string', 'max:500'],
            'especialidad_id' => ['required', 'integer', 'exists:especialidades,id'],
            'duracion_estimada_minutos' => ['required', 'integer', 'min:1', 'max:480'],
            'numero_sesiones_previstas' => ['required', 'integer', 'min:1', 'max:50'],
            'requiere_sesiones' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Mensajes descriptivos en español para las validaciones.
     */
    private function mensajes(): array
    {
        return [
            'id.required' => 'No se indicó el tratamiento.',
            'id.exists' => 'El tratamiento indicado no existe.',
            'nombre.required' => 'El campo Nombre del tratamiento es obligatorio.',
            'nombre.max' => 'El nombre no puede superar los 60 caracteres.',
            'nombre.unique' => 'Ya existe un tratamiento con este nombre en el catálogo.',
            'descripcion.max' => 'La descripción no puede superar los 500 caracteres.',
            'especialidad_id.required' => 'Debe seleccionar una especialidad para el tratamiento.',
            'especialidad_id.exists' => 'La especialidad seleccionada no es válida.',
            'duracion_estimada_minutos.required' => 'Debe indicar la duración estimada en minutos.',
            'duracion_estimada_minutos.min' => 'La duración estimada debe ser mayor a 0 minutos.',
            'duracion_estimada_minutos.max' => 'La duración estimada no puede superar los 480 minutos (8 horas).',
            'numero_sesiones_previstas.required' => 'Debe indicar el número de sesiones previstas.',
            'numero_sesiones_previstas.min' => 'Las sesiones previstas deben ser al menos 1.',
        ];
    }
}
