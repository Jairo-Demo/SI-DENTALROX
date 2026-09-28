<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - DentalRox</title>
    
    <!-- Enlace al archivo CSS externo mediante el helper asset() -->
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>
<body>

    <!-- Contenedor centralizado del formulario -->
    <main class="login-container">
        
        <!-- Encabezado con identidad del consultorio -->
        <header class="login-header">
            <div class="logo-badge">🦷</div>
            <h2>DentalRox</h2>
            <p>Sistema de Gestión Odontológica</p>
        </header>

        <!-- Mensajes de Error de Validación / Credenciales -->
        @if ($errors->any())
            <div class="alert alert-error" role="alert">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <!-- Mensajes Flash de Éxito (ej. al cerrar sesión) -->
        @if (session('exito'))
            <div class="alert alert-success" role="alert">
                <p>{{ session('exito') }}</p>
            </div>
        @endif

        <!-- Formulario de Autenticación (Envío por método POST) -->
        <form method="POST" action="{{ route('login') }}" class="login-form">
            @csrf

            <!-- Campo: Nombre de Usuario -->
            <div class="form-group">
                <label for="usuario">Usuario</label>
                <input type="text" 
                       id="usuario" 
                       name="usuario" 
                       value="{{ old('usuario') }}" 
                       placeholder="Ej. admin o odontologo1" 
                       required 
                       autofocus 
                       autocomplete="username">
            </div>

            <!-- Campo: Contraseña -->
            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" 
                       id="password" 
                       name="password" 
                       placeholder="Ingrese su contraseña" 
                       required 
                       autocomplete="current-password">
            </div>

            <!-- Botón de acción principal -->
            <button type="submit" class="btn-submit">
                Ingresar al Sistema
            </button>
            
            <p class="help-text">¿Problemas de acceso? Comuníquese con la administración.</p>
        </form>

    </main>

</body>
</html>
