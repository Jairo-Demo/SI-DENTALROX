<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperación de Contraseña - DentalRox</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f3f4f6;
            margin: 0;
            padding: 20px;
            color: #1f2937;
        }
        .container {
            max-width: 580px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        .header {
            background-color: #4f46e5;
            color: #ffffff;
            padding: 24px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            letter-spacing: 0.5px;
        }
        .header p {
            margin: 6px 0 0;
            font-size: 13px;
            color: #e0e7ff;
        }
        .content {
            padding: 30px;
            line-height: 1.6;
        }
        .user-greeting {
            font-size: 16px;
            font-weight: 600;
            color: #111827;
            margin-bottom: 12px;
        }
        .password-box {
            background-color: #f8fafc;
            border: 2px dashed #cbd5e1;
            border-radius: 8px;
            padding: 18px;
            text-align: center;
            margin: 24px 0;
        }
        .password-label {
            font-size: 12px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
            margin-bottom: 6px;
        }
        .password-value {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: 22px;
            font-weight: bold;
            color: #4f46e5;
            letter-spacing: 2px;
            user-select: all;
        }
        .badge-warning {
            background-color: #fffbeb;
            border-left: 4px solid #f59e0b;
            padding: 12px 16px;
            font-size: 13px;
            color: #92400e;
            border-radius: 4px;
            margin: 20px 0;
        }
        .btn-container {
            text-align: center;
            margin: 30px 0 10px;
        }
        .btn {
            background-color: #4f46e5;
            color: #ffffff !important;
            display: inline-block;
            padding: 12px 28px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            border-radius: 6px;
        }
        .footer {
            background-color: #f8fafc;
            border-top: 1px solid #e2e8f0;
            padding: 18px;
            text-align: center;
            font-size: 12px;
            color: #94a3b8;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>DentalRox</h1>
            <p>Sistema Integral de Gestión Odontológica</p>
        </div>
        <div class="content">
            <p class="user-greeting">Hola, {{ $usuario->nombres }} {{ $usuario->apellidos }}:</p>
            <p>
                Hemos recibido una solicitud para restablecer la contraseña de su cuenta de usuario (<strong>{{ $usuario->usuario }}</strong>).
            </p>
            <p>
                A continuación se le proporciona una <strong>contraseña temporal de acceso</strong> generada de forma segura:
            </p>

            <div class="password-box">
                <div class="password-label">Contraseña Temporal</div>
                <div class="password-value">{{ $contrasenaTemporal }}</div>
            </div>

            <div class="badge-warning">
                <strong>🔒 Política de Seguridad (RN-01):</strong><br>
                Esta contraseña temporal caduca al ser utilizada. En cuanto inicie sesión con ella, el sistema le exigirá automáticamente establecer su nueva contraseña personal y definitiva antes de navegar.
            </div>

            <div class="btn-container">
                <a href="{{ route('login') }}" class="btn">Ingresar al Sistema</a>
            </div>

            <p style="font-size: 12px; color: #64748b; margin-top: 25px;">
                Si usted no solicitó este cambio, comuníquese de inmediato con el Administrador del sistema para asegurar su cuenta.
            </p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} DentalRox. Todos los derechos reservados.
        </div>
    </div>
</body>
</html>
