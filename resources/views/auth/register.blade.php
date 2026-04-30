<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario</title>
    <style>
        body {
            font-family: sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #f0f0f0;
            margin: 0; /* Asegurar que no haya márgenes por defecto */
            padding: 20px 0; /* Añadir padding vertical para scroll en pantallas pequeñas */
        }
        .register-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 450px; /* Un poco más de ancho para los campos */
            width: 90%;
        }
        .register-container h1 {
            margin-bottom: 20px;
            color: #333;
        }
        .register-container input[type="text"],
        .register-container input[type="email"],
        .register-container input[type="password"],
        .register-container input[type="date"], /* Estilo para input date */
        .register-container select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 16px; /* Mejorar legibilidad */
        }
        .register-container button {
            width: 100%;
            padding: 12px; /* Botón un poco más grande */
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        .register-container button:hover {
            background-color: #218838;
        }
        .error-message-container { /* Contenedor para errores */
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
            text-align: left;
        }
        .error-message-container ul {
            margin: 0;
            padding-left: 20px;
        }
        .login-link {
            margin-top: 20px; /* Más espacio */
            font-size: 14px;
        }
        .hidden {
            display: none;
        }
        .form-group { /* Agrupar label e input si fuera necesario, aquí solo para inputs */
            margin-bottom: 15px;
        }
        .form-group label { /* Estilo para labels si se añaden explícitamente */
            display: block;
            margin-bottom: 5px;
            text-align: left;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <h1>Registro de Usuario</h1>

        @if ($errors->any())
            <div class="error-message-container">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST">
            @csrf

            <div class="form-group">
                <input type="text" name="name" placeholder="Nombre(s)" value="{{ old('name') }}" required autofocus>
            </div>
            <div class="form-group">
                <input type="text" name="last_name" placeholder="Apellidos" value="{{ old('last_name') }}" required>
            </div>
            <div class="form-group">
                <input type="email" name="email" placeholder="Correo electrónico" value="{{ old('email') }}" required>
            </div>
            <div class="form-group">
                <input type="password" name="password" placeholder="Contraseña" required>
                <small style="display: block; text-align: left; font-size: 0.8em; color: #666; margin-top: -10px; margin-bottom: 10px;">Mínimo 8 caracteres, incluyendo mayúsculas, minúsculas, números y símbolos.</small>
            </div>
            <div class="form-group">
                <input type="password" name="password_confirmation" placeholder="Confirmar Contraseña" required>
            </div>

            <div class="form-group">
                <select name="role" id="role" required>
                    <option value="">Selecciona tu rol</option>
                    {{-- CAMBIO: value y texto de 'adulto_mayor' a 'paciente' --}}
                    <option value="paciente" {{ old('role') == 'paciente' ? 'selected' : '' }}>Paciente</option>
                    <option value="responsable" {{ old('role') == 'responsable' ? 'selected' : '' }}>Responsable (Doctor, Fisioterapeuta, Familiar, etc.)</option>
                </select>
            </div>

            {{-- CAMBIO: id del div y la condición de la clase para 'paciente' --}}
            <div id="paciente-fields" class="{{ old('role') == 'paciente' ? '' : 'hidden' }}">
                <div class="form-group">
                    <label for="pac_fecha_nacimiento" style="display: block; text-align: left; font-weight: normal; margin-bottom: 5px;">Fecha de Nacimiento:</label>
                    {{-- CAMBIO: id del input para consistencia (opcional, pero bueno para claridad) --}}
                    <input type="date" name="fecha_nacimiento" id="pac_fecha_nacimiento" placeholder="Fecha de Nacimiento" value="{{ old('fecha_nacimiento') }}">
                </div>
                <div class="form-group">
                     {{-- CAMBIO: id del input para consistencia --}}
                    <input type="text" name="ci" id="pac_ci" placeholder="CI (Cédula de Identidad)" value="{{ old('ci') }}">
                </div>
                <div class="form-group">
                     {{-- CAMBIO: id del input para consistencia --}}
                    <input type="text" name="telefono" id="pac_telefono" placeholder="Teléfono" value="{{ old('telefono') }}">
                </div>
                <div class="form-group">
                     {{-- CAMBIO: id del input para consistencia --}}
                    <input type="text" name="direccion" id="pac_direccion" placeholder="Dirección" value="{{ old('direccion') }}">
                </div>
            </div>

            {{-- Campos adicionales para Responsable --}}
            {{-- CAMBIO: Condición de la clase para 'responsable' para ser consistente con la lógica de 'paciente' --}}
            <div id="responsable-fields" class="{{ old('role') == 'responsable' ? '' : 'hidden' }}">
                <div class="form-group">
                    <input type="text" name="profesion" id="res_profesion" placeholder="Profesión/Ocupación" value="{{ old('profesion') }}">
                </div>
                <div class="form-group">
                    <input type="text" name="ci" id="res_ci" placeholder="CI (Cédula de Identidad del Responsable)" value="{{ old('ci') }}">
                </div>
                <div class="form-group">
                    <input type="text" name="telefono" id="res_telefono" placeholder="Teléfono del Responsable" value="{{ old('telefono') }}">
                </div>
                <div class="form-group">
                    <input type="text" name="direccion" id="res_direccion" placeholder="Dirección del Responsable" value="{{ old('direccion') }}">
                </div>
            </div>

            <button type="submit">Registrar</button>
        </form>

        <p class="login-link">¿Ya tienes cuenta? <a href="{{ route('login') }}">Inicia sesión aquí</a></p>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const roleSelect = document.getElementById('role');
            const pacienteFieldsContainer = document.getElementById('paciente-fields');
            const responsableFieldsContainer = document.getElementById('responsable-fields');

            const pacienteInputs = pacienteFieldsContainer.querySelectorAll('input, select'); // Incluye selects si los tuvieras
            const responsableInputs = responsableFieldsContainer.querySelectorAll('input, select');

            function toggleFields() {
                const selectedRole = roleSelect.value;

                // Ocultar, deshabilitar y quitar 'required' por defecto para Paciente
                pacienteFieldsContainer.classList.add('hidden');
                pacienteInputs.forEach(input => {
                    input.removeAttribute('required');
                    input.disabled = true; // *** AÑADIR ESTO ***
                });

                // Ocultar, deshabilitar y quitar 'required' por defecto para Responsable
                responsableFieldsContainer.classList.add('hidden');
                responsableInputs.forEach(input => {
                    input.removeAttribute('required');
                    input.disabled = true; // *** AÑADIR ESTO ***
                });

                // Limpiar valores siempre es una buena práctica al cambiar de rol
                if (selectedRole !== 'paciente') {
                    pacienteInputs.forEach(input => input.value = '');
                }
                if (selectedRole !== 'responsable') {
                    responsableInputs.forEach(input => input.value = '');
                }

                if (selectedRole === 'paciente') {
                    pacienteFieldsContainer.classList.remove('hidden');
                    pacienteInputs.forEach(input => {
                        // Solo añadir 'required' a los que deben serlo según tu backend
                        if (input.name === 'fecha_nacimiento' || input.name === 'ci' || input.name === 'telefono' || input.name === 'direccion') {
                            input.setAttribute('required', 'required');
                        }
                        input.disabled = false; // *** AÑADIR ESTO ***
                    });
                } else if (selectedRole === 'responsable') {
                    responsableFieldsContainer.classList.remove('hidden');
                    responsableInputs.forEach(input => {
                        // Solo añadir 'required' a los que deben serlo según tu backend
                        if (input.name === 'profesion' || input.name === 'ci' || input.name === 'telefono' || input.name === 'direccion') {
                            input.setAttribute('required', 'required');
                        }
                        input.disabled = false; // *** AÑADIR ESTO ***
                    });
                }
            }

            roleSelect.addEventListener('change', toggleFields);
            toggleFields(); // Ejecutar al cargar para el caso de old('role') y errores de validación
        });
    </script>
</body>
</html>