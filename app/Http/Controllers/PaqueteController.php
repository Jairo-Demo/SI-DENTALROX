<?php

namespace App\Http\Controllers;

use App\Services\MenuService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Controlador para la visualización modular de cada Paquete del Sistema.
 * Muestra las opciones y accesos internos al hacer clic en un paquete.
 */
class PaqueteController extends Controller
{
    /**
     * Muestra la vista dedicada del paquete seleccionado.
     */
    public function show(string $codigo): View
    {
        // 1. Busca el paquete por código (P1, P2, P3, etc.) validando permisos del rol
        $paquete = MenuService::buscar($codigo, Auth::user());

        if (! $paquete) {
            abort(404, 'El paquete solicitado no existe o no tiene permisos para acceder a él.');
        }

        // 2. Retorna la vista dedicada del paquete con sus sub-módulos
        return view('paquetes.show', compact('paquete'));
    }
}
