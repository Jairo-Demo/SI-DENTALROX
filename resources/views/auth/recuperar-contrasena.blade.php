<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña - DentalRox</title>
    
    <!-- Enlace al CSS externo usando el helper asset() -->
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>
<body>

    <div class="login-container">
        <div class="login-header">
            <h2>DentalRox</h2>
            <p>Recuperar Acceso</p>
        </div>

        <!-- Manejo de errores -->
        @if ($errors->any())
            <div class="alert alert-error">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <!-- Mensaje de éxito -->
        @if (session('exito'))
            <div class="alert alert-success">
                {{ session('exito') }}
            </div>
        @endif

        <p style="font-size: 0.9rem; color: #4b5563; margin-bottom: 1.5rem; text-align: center; line-height: 1.4;">
            Ingrese su <strong>nombre de usuario</strong> o <strong>correo electrónico</strong> registrado. Le enviaremos una contraseña temporal de acceso a su bandeja de entrada.
        </p>

        <!-- Formulario de recuperación -->
        <form method="POST" action="{{ route('password.email') }}" class="login-form">
            @csrf

            <div class="form-group">
                <label for="usuario">Usuario o Correo Electrónico</label>
                <input type="text" 
                       id="usuario" 
                       name="usuario" 
                       value="{{ old('usuario') }}" 
                       placeholder="Ej: admin o doctor@dentalrox.com" 
                       required 
                       autofocus>
            </div>

            <button type="submit" class="btn-submit">Enviar Contraseña a mi Correo</button>
            
            <a href="{{ route('login') }}" class="back-link">
                &larr; Volver al inicio de sesión
            </a>
        </form>
    </div>

</body>
</html>
