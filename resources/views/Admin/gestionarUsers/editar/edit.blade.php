{{-- resources/views/Admin/gestionarUsers/editar/edit.blade.php --}}

@extends('layouts.main')


<header>
    <link rel="stylesheet" href="{{ asset('css/editarUsuario.css') }}">
    {{-- Feather Icons para los íconos de los botones --}}
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
</header>


@section('content')
                    <div class="page-header">
                        <h1 class="page-title">Editar Usuario</h1>
                        <div>
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('admin.gestionar-usuarios.index') }}">Gestionar Usuarios</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Editar Usuario</li>
                            </ol>
                        </div>
                    </div>

                    <div class="edit-user-container">
                        <h1 class="page-title">Editar Usuario: {{ $user->persona->nombres ?? $user->name }}</h1>

                        {{-- Mensajes de sesión y validación Laravel --}}
                        @if (session('error'))
                            <div class="alert alert-error" role="alert">
                                <strong>¡Error!</strong>
                                <span>{{ session('error') }}</span>
                            </div>
                        @endif
                        @if (session('warning'))
                            <div class="alert alert-warning" role="alert">
                                <strong>¡Atención!</strong>
                                <span>{{ session('warning') }}</span>
                            </div>
                        @endif
                        @if ($errors->any())
                            <div class="alert alert-error" role="alert">
                                <strong>Por favor corrige los siguientes errores:</strong>
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        {{-- Alerta de validación JavaScript (oculta por defecto) --}}
                        <div id="js-validation-alert" class="alert alert-warning hidden" role="alert">
                            <strong>¡Atención!</strong>
                            <span id="js-validation-message"></span>
                        </div>

                        <form action="{{ route('admin.gestionar-usuarios.update', $user->id_usuario) }}" method="POST"
                              class="form-card" id="editUserForm" novalidate>
                            @csrf
                            @method('PUT')

                            <h2 class="form-section-title">Datos Personales</h2>
                            <div class="form-grid">
                                <div>
                                    <label for="nombres" class="form-label">Nombres:</label>
                                    <input type="text" name="nombres" id="nombres"
                                           value="{{ old('nombres', $user->persona->nombres ?? '') }}"
                                           class="form-input @error('nombres') is-invalid @enderror" required>
                                    @error('nombres') <p class="error-message">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label for="primer_apellido" class="form-label">Primer Apellido:</label>
                                    <input type="text" name="primer_apellido" id="primer_apellido"
                                           value="{{ old('primer_apellido', $user->persona->primer_apellido ?? '') }}"
                                           class="form-input @error('primer_apellido') is-invalid @enderror" required>
                                    @error('primer_apellido') <p class="error-message">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label for="segundo_apellido" class="form-label">Segundo Apellido (Opcional):</label>
                                    <input type="text" name="segundo_apellido" id="segundo_apellido"
                                           value="{{ old('segundo_apellido', $user->persona->segundo_apellido ?? '') }}"
                                           class="form-input @error('segundo_apellido') is-invalid @enderror">
                                    @error('segundo_apellido') <p class="error-message">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label for="fecha_nacimiento" class="form-label">Fecha de Nacimiento:</label>
                                    <input type="date" name="fecha_nacimiento" id="fecha_nacimiento"
                                           value="{{ old('fecha_nacimiento', $user->persona ? (\Carbon\Carbon::parse($user->persona->fecha_nacimiento)->format('Y-m-d')) : '') }}"
                                           class="form-input @error('fecha_nacimiento') is-invalid @enderror" required>
                                    @error('fecha_nacimiento') <p class="error-message">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label for="sexo" class="form-label">Sexo:</label>
                                    <select name="sexo" id="sexo"
                                            class="form-select @error('sexo') is-invalid @enderror" required>
                                        <option value="M" {{ old('sexo', $user->persona->sexo ?? '') == 'M' ? 'selected' : '' }}>Masculino</option>
                                        <option value="F" {{ old('sexo', $user->persona->sexo ?? '') == 'F' ? 'selected' : '' }}>Femenino</option>
                                        <option value="O" {{ old('sexo', $user->persona->sexo ?? '') == 'O' ? 'selected' : '' }}>Otro</option>
                                    </select>
                                    @error('sexo') <p class="error-message">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label for="estado_civil" class="form-label">Estado Civil:</label>
                                    <select name="estado_civil" id="estado_civil"
                                            class="form-select @error('estado_civil') is-invalid @enderror" required>
                                        <option value="casado" {{ old('estado_civil', $user->persona->estado_civil ?? '') == 'casado' ? 'selected' : '' }}>Casado/a</option>
                                        <option value="divorciado" {{ old('estado_civil', $user->persona->estado_civil ?? '') == 'divorciado' ? 'selected' : '' }}>Divorciado/a</option>
                                        <option value="soltero" {{ old('estado_civil', $user->persona->estado_civil ?? '') == 'soltero' ? 'selected' : '' }}>Soltero/a</option>
                                        <option value="otro" {{ old('estado_civil', $user->persona->estado_civil ?? '') == 'otro' ? 'selected' : '' }}>Otro</option>
                                    </select>
                                    @error('estado_civil') <p class="error-message">{{ $message }}</p> @enderror
                                </div>
                                <div class="col-span-full">
                                    <label for="domicilio" class="form-label">Domicilio:</label>
                                    <input type="text" name="domicilio" id="domicilio"
                                           value="{{ old('domicilio', $user->persona->domicilio ?? '') }}"
                                           class="form-input @error('domicilio') is-invalid @enderror" required>
                                    @error('domicilio') <p class="error-message">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label for="telefono" class="form-label">Teléfono:</label>
                                    <input type="tel" name="telefono" id="telefono"
                                           value="{{ old('telefono', $user->persona->telefono ?? '') }}"
                                           class="form-input @error('telefono') is-invalid @enderror" required>
                                    @error('telefono') <p class="error-message">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label for="zona_comunidad" class="form-label">Zona/Comunidad (Opcional):</label>
                                    <input type="text" name="zona_comunidad" id="zona_comunidad"
                                           value="{{ old('zona_comunidad', $user->persona->zona_comunidad ?? '') }}"
                                           class="form-input @error('zona_comunidad') is-invalid @enderror">
                                    @error('zona_comunidad') <p class="error-message">{{ $message }}</p> @enderror
                                </div>
                                
                                {{-- Campo de especialidad para rol Responsable (ID 2) --}}
                                <div id="campo_area_especialidad" style="display: none;">
                                    <label for="area_especialidad" class="form-label">Área de Especialidad (Salud):</label>
                                    <select name="area_especialidad" id="area_especialidad" class="form-select @error('area_especialidad') is-invalid @enderror">
                                        <option value="" disabled>Seleccione una especialidad...</option>
                                        <option value="Enfermeria" {{ old('area_especialidad', $user->persona->area_especialidad ?? '') == 'Enfermeria' ? 'selected' : '' }}>Enfermería</option>
                                        <option value="Fisioterapia-Kinesiologia" {{ old('area_especialidad', $user->persona->area_especialidad ?? '') == 'Fisioterapia-Kinesiologia' ? 'selected' : '' }}>Fisioterapia-Kinesiología</option>
                                        <option value="otro" {{ old('area_especialidad', $user->persona->area_especialidad ?? '') == 'otro' ? 'selected' : '' }}>Otro</option>
                                    </select>
                                    @error('area_especialidad') <p class="error-message">{{ $message }}</p> @enderror
                                </div>

                                {{-- Campo de especialidad para rol Legal (ID 3) --}}
                                <div id="campo_area_especialidad_legal" style="display: none;">
                                    <label for="area_especialidad_legal" class="form-label">Área de Especialidad (Legal):</label>
                                    <select name="area_especialidad_legal" id="area_especialidad_legal" class="form-select @error('area_especialidad_legal') is-invalid @enderror">
                                        <option value="" disabled>Seleccione una especialidad...</option>
                                        <option value="Asistente Social" {{ old('area_especialidad_legal', $user->persona->area_especialidad_legal ?? '') == 'Asistente Social' ? 'selected' : '' }}>Asistente Social</option>
                                        <option value="Psicologia" {{ old('area_especialidad_legal', $user->persona->area_especialidad_legal ?? '') == 'Psicologia' ? 'selected' : '' }}>Psicología</option>
                                        <option value="Derecho" {{ old('area_especialidad_legal', $user->persona->area_especialidad_legal ?? '') == 'Derecho' ? 'selected' : '' }}>Derecho</option>
                                    </select>
                                    @error('area_especialidad_legal') <p class="error-message">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            <hr class="divider">

                            <h2 class="form-section-title">Datos de Usuario</h2>
                            <div class="form-grid">
                                <div>
                                    <label for="ci_display" class="form-label">CI (No editable):</label>
                                    <input type="text" name="ci_display" id="ci_display"
                                           value="{{ $user->ci }}" class="form-input" readonly>
                                    <input type="hidden" name="ci" value="{{ $user->ci }}">
                                </div>
                                <div>
                                    <label for="id_rol" class="form-label">Rol:</label>
                                    <select name="id_rol" id="id_rol"
                                            class="form-select @error('id_rol') is-invalid @enderror"
                                            {{ $isAdultoMayorRole ? 'disabled' : '' }} required>
                                        @foreach ($roles as $rol)
                                            <option value="{{ $rol->id_rol }}"
                                                {{ old('id_rol', $user->id_rol) == $rol->id_rol ? 'selected' : '' }}>
                                                {{ $rol->nombre_rol }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if ($isAdultoMayorRole)
                                        <p class="error-message">El rol 'adulto_mayor' no puede ser modificado.</p>
                                        <input type="hidden" name="id_rol" value="{{ $user->id_rol }}">
                                    @endif
                                    @error('id_rol') <p class="error-message">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label for="password" class="form-label">Nueva Contraseña (Opcional):</label>
                                    <input type="password" name="password" id="password"
                                           class="form-input @error('password') is-invalid @enderror"
                                           autocomplete="new-password">
                                    @error('password') <p class="error-message">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label for="password_confirmation" class="form-label">Confirmar Nueva Contraseña:</label>
                                    <input type="password" name="password_confirmation" id="password_confirmation"
                                           class="form-input" autocomplete="new-password">
                                </div>
                            </div>

                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary">
                                    <i data-feather="save"></i> Actualizar Usuario
                                </button>
                                <a href="{{ route('admin.gestionar-usuarios.index') }}" class="btn btn-secondary">
                                    <i data-feather="x-circle"></i> Cancelar
                                </a>
                            </div>
                        </form>
                    </div>

   

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Inicializar Feather Icons
    if (typeof feather !== 'undefined') {
        feather.replace();
    }

    // --- LÓGICA PARA CAMPOS DE ESPECIALIDAD DINÁMICOS ---
    const rolSelect = document.getElementById('id_rol');
    const especialidadSaludContainer = document.getElementById('campo_area_especialidad');
    const especialidadLegalContainer = document.getElementById('campo_area_especialidad_legal');
    const especialidadSaludSelect = document.getElementById('area_especialidad');
    const especialidadLegalSelect = document.getElementById('area_especialidad_legal');

    function toggleEspecialidadFields() {
        const selectedRol = rolSelect.value;

        // Lógica para 'Responsable' (ID 2) - Campo de especialidad de salud
        if (selectedRol == '2') {
            especialidadSaludContainer.style.display = 'block';
            especialidadSaludSelect.disabled = false;
            especialidadSaludSelect.required = true;
        } else {
            especialidadSaludContainer.style.display = 'none';
            especialidadSaludSelect.disabled = true;
            especialidadSaludSelect.required = false;
            especialidadSaludSelect.value = ''; // Limpiar valor cuando se oculta
        }

        // Lógica para 'Legal' (ID 3) - Campo de especialidad legal
        if (selectedRol == '3') {
            especialidadLegalContainer.style.display = 'block';
            especialidadLegalSelect.disabled = false;
            especialidadLegalSelect.required = true;
        } else {
            especialidadLegalContainer.style.display = 'none';
            especialidadLegalSelect.disabled = true;
            especialidadLegalSelect.required = false;
            especialidadLegalSelect.value = ''; // Limpiar valor cuando se oculta
        }
    }

    if (rolSelect) {
        rolSelect.addEventListener('change', toggleEspecialidadFields);
        // Ejecutar al cargar para establecer el estado inicial
        toggleEspecialidadFields();
    }
    // --- FIN DE LÓGICA DE ESPECIALIDAD ---


    // --- LÓGICA DE VALIDACIÓN DEL FORMULARIO EXISTENTE ---
    const editUserForm = document.getElementById('editUserForm');
    const jsValidationAlert = document.getElementById('js-validation-alert');
    const jsValidationMessage = document.getElementById('js-validation-message');

    function showJsValidationAlert(message) {
        jsValidationMessage.textContent = message;
        jsValidationAlert.classList.remove('hidden');
        jsValidationAlert.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    function hideJsValidationAlert() {
        if (jsValidationAlert) {
            jsValidationAlert.classList.add('hidden');
            jsValidationMessage.textContent = '';
        }
    }

    hideJsValidationAlert();

    const formInputs = document.querySelectorAll('.form-input, .form-select');
    formInputs.forEach(input => {
        input.addEventListener('input', function() {
            this.classList.remove('is-invalid');
        });
    });

    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('password_confirmation');

    function validatePasswordMatch() {
        if (passwordInput.value !== confirmPasswordInput.value) {
            confirmPasswordInput.classList.add('is-invalid');
        } else {
            confirmPasswordInput.classList.remove('is-invalid');
        }
    }
    
    if (passwordInput && confirmPasswordInput) {
        passwordInput.addEventListener('input', validatePasswordMatch);
        confirmPasswordInput.addEventListener('input', validatePasswordMatch);
    }

    if (editUserForm) {
        editUserForm.addEventListener('submit', function (event) {
            let isValid = true;
            let firstInvalidField = null;
            let errorMessage = 'Por favor, complete todos los campos obligatorios.';
            
            editUserForm.querySelectorAll('.form-input, .form-select').forEach(field => {
                field.classList.remove('is-invalid');
            });

            const requiredFields = editUserForm.querySelectorAll('[required]');
            requiredFields.forEach(field => {
                // Solo validar si el campo es visible y no está deshabilitado
                if (!field.disabled && field.offsetParent !== null && field.value.trim() === '') {
                    isValid = false;
                    field.classList.add('is-invalid');
                    if (!firstInvalidField) {
                        firstInvalidField = field;
                    }
                }
            });

            if (passwordInput.value || confirmPasswordInput.value) {
                if (passwordInput.value !== confirmPasswordInput.value) {
                    isValid = false;
                    confirmPasswordInput.classList.add('is-invalid');
                    errorMessage = 'Las contraseñas no coinciden.';
                    if (!firstInvalidField) {
                        firstInvalidField = confirmPasswordInput;
                    }
                }
            }

            if (!isValid) {
                event.preventDefault();
                showJsValidationAlert(errorMessage);
                if (firstInvalidField) {
                    firstInvalidField.focus();
                    firstInvalidField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            } else {
                hideJsValidationAlert();
            }
        });
    }
});
</script>
@endpush