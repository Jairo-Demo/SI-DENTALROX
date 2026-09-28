<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - DentalRox</title>
    
    <!-- Enlace al CSS externo usando el helper asset() -->
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>
<body>

    <div class="login-container">
        <div class="login-header">
            <h2>DentalRox</h2>
            <p>Inicio de Sesión</p>
        </div>

        <!-- Manejo de errores genéricos -->
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

        <!-- Formulario semántico -->
        <form method="POST" action="{{ route('login') }}" class="login-form">
            @csrf

            <div class="form-group">
                <label for="usuario">Usuario</label>
                <input type="text" 
                       id="usuario" 
                       name="usuario" 
                       value="{{ old('usuario') }}" 
                       placeholder="Ingrese su usuario" 
                       required 
                       autofocus>
            </div>

            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" 
                       id="password" 
                       name="password" 
                       placeholder="Ingrese su contraseña" 
                       required>
            </div>

            <button type="submit" class="btn-submit">Ingresar al Sistema</button>
            
            <p class="help-text">Si olvidó su contraseña, comuníquese con el Administrador.</p>
        </form>
    </div>

</body>
</html>
