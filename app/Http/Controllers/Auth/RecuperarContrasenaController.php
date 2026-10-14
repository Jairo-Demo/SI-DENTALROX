<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\RecuperarContrasenaMail;
use App\Models\User;
use App\Services\BitacoraService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class RecuperarContrasenaController extends Controller
{
    /**
     * Muestra el formulario para solicitar la recuperación de contraseña.
     */
    public function create(): View
    {
        return view('auth.recuperar-contrasena');
    }

    /**
     * Procesa la solicitud: valida el usuario/correo, genera la contraseña temporal (RN-01),
     * actualiza la cuenta, registra en bitácora y envía el correo electrónico.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'usuario' => ['required', 'string', 'max:100'],
        ], [
            'usuario.required' => 'Debe ingresar su nombre de usuario o correo electrónico.',
        ]);

        $identificador = trim($request->input('usuario'));

        // Permite buscar por nombre de usuario O por correo electrónico
        $usuario = User::where('usuario', $identificador)
            ->orWhere('correo', $identificador)
            ->first();

        if (! $usuario) {
            return back()->withInput()->withErrors([
                'usuario' => 'No se encontró ninguna cuenta registrada con ese usuario o correo.',
            ]);
        }

        if ($usuario->estado === 'inactivo') {
            return back()->withInput()->withErrors([
                'usuario' => 'La cuenta se encuentra inhabilitada. Comuníquese directamente con el Administrador.',
            ]);
        }

        if (empty($usuario->correo)) {
            return back()->withInput()->withErrors([
                'usuario' => 'La cuenta no tiene un correo electrónico asociado. Comuníquese con el Administrador.',
            ]);
        }

        // Generar contraseña temporal segura que cumple RN-01
        $temporal = $this->generarContrasenaTemporal();

        DB::transaction(function () use ($usuario, $temporal) {
            $usuario->password = Hash::make($temporal);
            $usuario->intentos_fallidos = 0;
            $usuario->bloqueado_hasta = null;
            $usuario->debe_cambiar_contrasena = true;
            $usuario->save();

            // Auditoría en bitácora
            BitacoraService::registrar(
                BitacoraService::RECUPERACION_CONTRASENA,
                $usuario->id,
                'users',
                $usuario->id
            );
        });

        // Intentar envío de correo
        $correoOfuscado = $this->ofuscarCorreo($usuario->correo);
        $correoEnviado = false;

        try {
            Mail::to($usuario->correo)->send(new RecuperarContrasenaMail($usuario, $temporal));
            $correoEnviado = true;
        } catch (\Throwable $e) {
            Log::warning('No se pudo enviar correo de recuperación a '.$usuario->correo.': '.$e->getMessage());
        }

        if ($correoEnviado) {
            return redirect()->route('login')->with(
                'exito',
                "Se ha enviado una contraseña temporal a su correo registrado ({$correoOfuscado}). Revise su bandeja de entrada para ingresar."
            );
        }

        // En caso de que el entorno no tenga servidor SMTP configurado, se entrega mensaje amigable con la clave para no dejarlo bloqueado
        return redirect()->route('login')->with(
            'exito',
            "Acceso restablecido para {$usuario->usuario}. Contraseña temporal generada: {$temporal} (Al iniciar sesión, el sistema le pedirá crear su contraseña definitiva)."
        );
    }

    /**
     * Genera una contraseña temporal de 12 caracteres que cumple RN-01.
     * Mínimo 8 caracteres, al menos una mayúscula, minúscula, número y carácter especial.
     */
    private function generarContrasenaTemporal(): string
    {
        $minusculas = 'abcdefghijkmnpqrstuvwxyz';
        $mayusculas = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
        $numeros = '23456789';
        $especiales = '!@#$%&*?';
        $todos = $minusculas.$mayusculas.$numeros.$especiales;

        do {
            $caracteres = [
                $this->caracterAlAzar($minusculas),
                $this->caracterAlAzar($mayusculas),
                $this->caracterAlAzar($numeros),
                $this->caracterAlAzar($especiales),
            ];

            for ($i = 4; $i < 12; $i++) {
                $caracteres[] = $this->caracterAlAzar($todos);
            }

            shuffle($caracteres);
            $candidata = implode('', $caracteres);
        } while (! preg_match(User::REGEX_CONTRASENA, $candidata));

        return $candidata;
    }

    private function caracterAlAzar(string $alfabeto): string
    {
        return $alfabeto[random_int(0, strlen($alfabeto) - 1)];
    }

    /**
     * Ofusca el correo para no mostrarlo completo por privacidad (ej. adm***@dentalrox.com).
     */
    private function ofuscarCorreo(string $correo): string
    {
        $partes = explode('@', $correo);
        if (count($partes) !== 2) {
            return $correo;
        }

        $usuario = $partes[0];
        $dominio = $partes[1];
        $visible = substr($usuario, 0, min(3, strlen($usuario)));

        return $visible.'***@'.$dominio;
    }
}
