<?php

namespace App\Http\Controllers;

use App\Models\Especialidad;
use App\Models\Tratamiento;
use App\Services\BitacoraService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Controlador para la Gestión del Catálogo de Especialidades Odontológicas.
 * Implementa el CRUD completo con verbos GET y POST (ID oculto en formularios).
 */
class EspecialidadController extends Controller
{
    /**
     * Muestra el listado de especialidades con la cantidad de usuarios vinculados.
     */
    public function index(): View
    {
        $especialidades = Especialidad::withCount('usuarios')
            ->orderBy('nombre')
            ->get();

        return view('especialidades.index', compact('especialidades'));
    }

    /**
     * Muestra la ficha detallada de una especialidad y sus odontólogos asociados.
     */
    public function show($id): View
    {
        $especialidad = Especialidad::withCount('usuarios')
            ->with(['usuarios' => fn ($query) => $query->orderBy('apellidos')->orderBy('nombres')])
            ->findOrFail($id);

        return view('especialidades.show', compact('especialidad'));
    }

    /**
     * Guarda una nueva especialidad en la base de datos (Ruta POST).
     */
    public function store(Request $request): RedirectResponse
    {
        // Validación de datos
        $datosValidados = $request->validate($this->reglas(), $this->mensajes());

        DB::transaction(function () use ($datosValidados, $request) {
            $especialidad = Especialidad::create($datosValidados);

            // Registro en bitácora de auditoría
            BitacoraService::registrar(
                BitacoraService::CREAR_ESPECIALIDAD,
                $request->user()->id,
                'especialidades',
                $especialidad->id
            );
        });

        return redirect()->back()->with('exito', 'Especialidad registrada correctamente.');
    }

    /**
     * Modifica una especialidad existente (Ruta POST con ID oculto).
     */
    public function update(Request $request): RedirectResponse
    {
        // 1. Valida el ID oculto
        $request->validate($this->reglaId(), $this->mensajes());

        // 2. Valida el nombre asegurando que sea único (ignorando el ID actual)
        $id = (int) $request->input('id');
        $datosValidados = $request->validate($this->reglas($id), $this->mensajes());

        $especialidad = Especialidad::findOrFail($id);

        DB::transaction(function () use ($especialidad, $datosValidados, $request) {
            $especialidad->update($datosValidados);

            BitacoraService::registrar(
                BitacoraService::MODIFICAR_ESPECIALIDAD,
                $request->user()->id,
                'especialidades',
                $especialidad->id
            );
        });

        return redirect()->back()->with('exito', 'Especialidad actualizada correctamente.');
    }

    /**
     * Elimina una especialidad si no tiene profesionales ni tratamientos vinculados.
     */
    public function delete(Request $request): RedirectResponse
    {
        $request->validate($this->reglaId(), $this->mensajes());

        $especialidad = Especialidad::findOrFail($request->input('id'));

        // Verificación de integridad referencial antes de borrar
        $tieneUsuarios = $especialidad->usuarios()->exists();
        $tieneTratamientos = Tratamiento::where('especialidad_id', $especialidad->id)->exists();

        if ($tieneUsuarios || $tieneTratamientos) {
            return redirect()->back()->withErrors([
                'operacion' => 'No se puede eliminar la especialidad porque tiene odontólogos o tratamientos asociados.',
            ]);
        }

        DB::transaction(function () use ($especialidad, $request) {
            $idEliminado = $especialidad->id;
            $especialidad->delete();

            BitacoraService::registrar(
                BitacoraService::ELIMINAR_ESPECIALIDAD,
                $request->user()->id,
                'especialidades',
                $idEliminado
            );
        });

        return redirect()->route('especialidades.listar')->with('exito', 'Especialidad eliminada correctamente.');
    }

    /**
     * Regla de validación para el ID oculto.
     */
    private function reglaId(): array
    {
        return [
            'id' => ['required', 'integer', 'exists:especialidades,id'],
        ];
    }

    /**
     * Reglas de validación para los campos del formulario.
     */
    private function reglas(?int $idIgnorar = null): array
    {
        return [
            'nombre' => ['required', 'string', 'max:40', Rule::unique('especialidades', 'nombre')->ignore($idIgnorar)],
            'descripcion' => ['nullable', 'string'],
        ];
    }

    /**
     * Mensajes de validación en español.
     */
    private function mensajes(): array
    {
        return [
            'id.required' => 'No se indicó la especialidad.',
            'id.exists' => 'La especialidad indicada no existe en el sistema.',
            'nombre.required' => 'El campo Nombre es obligatorio.',
            'nombre.max' => 'El nombre no puede superar los 40 caracteres.',
            'nombre.unique' => 'Ya existe una especialidad registrada con este nombre.',
            'descripcion.string' => 'La descripción debe ser un texto.',
        ];
    }
}
