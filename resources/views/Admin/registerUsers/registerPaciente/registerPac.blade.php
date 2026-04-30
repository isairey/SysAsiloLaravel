<!-- {{-- ========================================================================= --}}
{{-- === PASO 1: SE EXTIENDE EL LAYOUT PRINCIPAL EN LUGAR DE INCLUIR HEADERS === --}}
{{-- ========================================================================= --}} -->
@extends('layouts.main')

<!-- {{-- ========================================================================= --}}
{{-- === PASO 2: TODO EL CONTENIDO DE LA PÁGINA VA DENTRO DE @section('content') === --}}
{{-- ========================================================================= --}} -->
@section('content')

<div class="main-container container-fluid">
    <div class="page-header">
        <h1 class="page-title">Registrar Adulto Mayor</h1>
        <div>
            <ol class="breadcrumb">
                {{-- Mejora: La ruta al dashboard es dinámica según el rol del usuario logueado --}}
                @php
                    $user = auth()->user();
                    $rol = $user->role_name ?? 'admin'; // Usando el accessor del modelo User
                    $dashboardRoute = route('login'); // Fallback
                    if (in_array($rol, ['admin', 'legal', 'asistente-social'])) {
                        $dashboardRouteName = $rol . '.dashboard';
                        if (Route::has($dashboardRouteName)) {
                            $dashboardRoute = route($dashboardRouteName);
                        }
                    }
                @endphp
                <li class="breadcrumb-item"><a href="{{ $dashboardRoute }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Registrar Adulto Mayor</li>
            </ol>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xl-12">
            <div class="card overflow-hidden">
                <div class="card-header bg-success text-white">
                    <h3 class="card-title text-white">Formulario de Registro de Adulto Mayor</h3>
                </div>
                <div class="card-body">
                    {{-- Manejo de errores de validación --}}
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- La ruta del formulario corregida se mantiene --}}
                    <form action="{{ route('gestionar-adultomayor.store') }}" method="POST" id="registerAdultoMayorForm" novalidate>
                        @csrf

                        {{-- Navegación de Pestañas (Sin cambios) --}}
                        <ul class="nav nav-tabs" id="adultMayorTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="datos-personales-tab" data-bs-toggle="tab" data-bs-target="#datosPersonales" type="button" role="tab" aria-controls="datosPersonales" aria-selected="true">
                                    1. Datos Personales
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="datos-adulto-mayor-tab" data-bs-toggle="tab" data-bs-target="#datosAdultoMayor" type="button" role="tab" aria-controls="datosAdultoMayor" aria-selected="false">
                                    2. Información Específica
                                </button>
                            </li>
                        </ul>

                        {{-- Contenido de las Pestañas --}}
                        <div class="tab-content mt-3" id="adultMayorTabContent">
                            {{-- Pestaña 1: Datos Personales --}}
                            <div class="tab-pane fade show active" id="datosPersonales" role="tabpanel" aria-labelledby="datos-personales-tab">
                                <h5 class="mb-3 text-success">Información Personal</h5>
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="nombres" class="form-label">Nombres <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="nombres" name="nombres" value="{{ old('nombres') }}" required>
                                        <div class="invalid-feedback">Por favor, ingrese los nombres.</div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="primer_apellido" class="form-label">Primer Apellido <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="primer_apellido" name="primer_apellido" value="{{ old('primer_apellido') }}" required>
                                        <div class="invalid-feedback">Por favor, ingrese el primer apellido.</div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="segundo_apellido" class="form-label">Segundo Apellido</label>
                                        <input type="text" class="form-control" id="segundo_apellido" name="segundo_apellido" value="{{ old('segundo_apellido') }}">
                                    </div>
                                </div>
                                <div class="row">
                                    {{-- ========================================================================= --}}
                                    {{-- === INICIO: CAMBIO EN CAMPO CI === --}}
                                    {{-- ========================================================================= --}}
                                    <div class="col-md-4 mb-3">
                                        <label for="ci" class="form-label">CI (Cédula de Identidad) <span class="text-danger">*</span></label>
                                        {{-- Se elimina el atributo pattern para permitir letras y guiones --}}
                                        <input type="text" class="form-control" id="ci" name="ci" value="{{ old('ci') }}" required>
                                        <div class="invalid-feedback" id="ci_error_message">Por favor, ingrese el CI.</div>
                                    </div>
                                    {{-- ========================================================================= --}}
                                    {{-- === FIN: CAMBIO EN CAMPO CI === --}}
                                    {{-- ========================================================================= --}}
                                    <div class="col-md-4 mb-3">
                                        <label for="fecha_nacimiento" class="form-label">Fecha de Nacimiento <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento" value="{{ old('fecha_nacimiento') }}" required>
                                        <div class="invalid-feedback">Por favor, ingrese la fecha de nacimiento.</div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="sexo" class="form-label">Sexo <span class="text-danger">*</span></label>
                                        <select class="form-select" id="sexo" name="sexo" required>
                                            <option value="" disabled {{ old('sexo') ? '' : 'selected' }}>Seleccione...</option>
                                            <option value="F" {{ old('sexo') == 'F' ? 'selected' : '' }}>Femenino</option>
                                            <option value="M" {{ old('sexo') == 'M' ? 'selected' : '' }}>Masculino</option>
                                            <option value="O" {{ old('sexo') == 'O' ? 'selected' : '' }}>Otro</option>
                                        </select>
                                        <div class="invalid-feedback">Por favor, seleccione el sexo.</div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="estado_civil" class="form-label">Estado Civil <span class="text-danger">*</span></label>
                                        <select class="form-select" id="estado_civil" name="estado_civil" required>
                                            <option value="" disabled {{ old('estado_civil') ? '' : 'selected' }}>Seleccione...</option>
                                            <option value="casado" {{ old('estado_civil') == 'casado' ? 'selected' : '' }}>Casado(a)</option>
                                            <option value="divorciado" {{ old('estado_civil') == 'divorciado' ? 'selected' : '' }}>Divorciado(a)</option>
                                            <option value="soltero" {{ old('estado_civil') == 'soltero' ? 'selected' : '' }}>Soltero(a)</option>
                                            <option value="otro" {{ old('estado_civil') == 'otro' ? 'selected' : '' }}>Otro</option>
                                        </select>
                                        <div class="invalid-feedback">Por favor, seleccione el estado civil.</div>
                                    </div>
                                    <div class="col-md-8 mb-3">
                                        <label for="domicilio" class="form-label">Domicilio <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="domicilio" name="domicilio" value="{{ old('domicilio') }}" required>
                                        <div class="invalid-feedback">Por favor, ingrese el domicilio.</div>
                                    </div>
                                </div>
                                <div class="row">
                                    {{-- ========================================================================= --}}
                                    {{-- === INICIO: CAMBIO EN CAMPO TELEFONO === --}}
                                    {{-- ========================================================================= --}}
                                    <div class="col-md-4 mb-3">
                                        <label for="telefono" class="form-label">Teléfono/Celular <span class="text-danger">*</span></label>
                                        {{-- Se elimina el atributo pattern para consistencia --}}
                                        <input type="text" class="form-control" id="telefono" name="telefono" value="{{ old('telefono') }}" required>
                                        <div class="invalid-feedback">Por favor, ingrese el teléfono.</div>
                                    </div>
                                    {{-- ========================================================================= --}}
                                    {{-- === FIN: CAMBIO EN CAMPO TELEFONO === --}}
                                    {{-- ========================================================================= --}}
                                    <div class="col-md-4 mb-3">
                                        <label for="zona_comunidad" class="form-label">Zona/Comunidad</label>
                                        <input type="text" class="form-control" id="zona_comunidad" name="zona_comunidad" value="{{ old('zona_comunidad') }}">
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end mt-3">
                                    <button type="button" class="btn btn-success" id="nextButton">
                                        Siguiente <i class="fe fe-arrow-right"></i>
                                    </button>
                                </div>
                            </div>

                            {{-- Pestaña 2: Datos Específicos del Adulto Mayor --}}
                            <div class="tab-pane fade" id="datosAdultoMayor" role="tabpanel" aria-labelledby="datos-adulto-mayor-tab">
                                <h5 class="mb-3 text-success">Información Específica del Adulto Mayor</h5>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="discapacidad" class="form-label">Discapacidades o Condiciones Especiales</label>
                                        <textarea class="form-control" id="discapacidad" name="discapacidad" rows="4" placeholder="Describe cualquier discapacidad, condición médica especial o necesidades específicas...">{{ old('discapacidad') }}</textarea>
                                        <small class="form-text text-muted">Campo opcional. Información importante para brindar atención personalizada.</small>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="vive_con" class="form-label">¿Con quién vive?</label>
                                        <textarea class="form-control" id="vive_con" name="vive_con" rows="4" placeholder="Ejemplo: Vive solo, con esposo/a, con hijos, en casa de adulto mayor, etc.">{{ old('vive_con') }}</textarea>
                                        <small class="form-text text-muted">Esta información ayuda a entender su situación social y de apoyo.</small>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="migrante" class="form-label">Situación Migratoria</label>
                                        <select class="form-select" id="migrante" name="migrante">
                                            <option value="0" @if(old('migrante', '0') == '0') selected @endif>No es migrante</option>
                                            <option value="1" @if(old('migrante') == '1') selected @endif>Es migrante</option>
                                        </select>
                                        <small class="form-text text-muted">Indica si la persona migró de otra región o país.</small>
                                    </div>
                                    
                                    <!-- Campo condicional para el origen del migrante -->
                                    <div class="col-md-6 mb-3" id="origen_migracion_wrapper" style="display: none;">
                                        <label for="origen_migracion" class="form-label">Lugar de Origen (Migración) <span class="text-danger" id="origen_migracion_asterisk" style="display: none;">*</span></label>
                                        <input type="text" class="form-control" id="origen_migracion" name="origen_migracion" value="{{ old('origen_migracion') }}" placeholder="País, Departamento o Ciudad">
                                        <div class="invalid-feedback">Por favor, ingrese el lugar de origen.</div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="nro_caso" class="form-label">Número de Caso</label>
                                        <input type="text" class="form-control" id="nro_caso" name="nro_caso" value="{{ old('nro_caso') }}" placeholder="Ej: AM-2024-001">
                                        <small class="form-text text-muted">Número único de identificación del caso (opcional).</small>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="fecha" class="form-label">Fecha de Registro <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="fecha" name="fecha" value="{{ old('fecha', date('Y-m-d')) }}" required>
                                        <div class="invalid-feedback">Por favor, ingrese la fecha de registro.</div>
                                        <small class="form-text text-muted">Fecha en que se registra al adulto mayor en el sistema.</small>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between mt-4">
                                    <button type="button" class="btn btn-outline-success" id="prevButton">
                                        <i class="fe fe-arrow-left"></i> Anterior
                                    </button>
                                    <div>
                                        <button type="submit" class="btn btn-success" id="submitButton">
                                            <i class="fe fe-check-circle"></i> Registrar Adulto Mayor
                                        </button>
                                        <a href="{{ url()->previous() }}" class="btn btn-danger ms-2">
                                            <i class="fe fe-x"></i> Cancelar
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

<style>
    /* Estilos generales para asegurar visibilidad de validación */
    .form-control.is-invalid, .form-select.is-invalid, .form-check-input.is-invalid {
        border-color: #dc3545 !important; /* Rojo para inválido */
    }
    .form-control.is-valid, .form-select.is-valid {
        border-color: #198754 !important; /* Verde para válido */
    }
    .invalid-feedback {
        display: block !important; /* Asegurar que el mensaje de feedback se muestre */
        width: 100%;
        margin-top: .25rem;
        font-size: .875em;
        color: #dc3545;
    }
    .form-check-input.is-invalid ~ .form-check-label {
        color: #dc3545 !important;
    }
    .nav-tabs .nav-link {
        border: 1px solid #ddd;
        border-bottom-color: transparent;
        border-radius: .25rem .25rem 0 0;
        margin-right: 2px;
        color: #495057;
    }
    .nav-tabs .nav-link.active {
        color: #28a745;
        background-color: #fff;
        border-color: #dee2e6 #dee2e6 #fff;
        font-weight: bold;
    }
    .nav-tabs .nav-link:hover {
        color: #28a745;
        border-color: #e9ecef #e9ecef #dee2e6;
    }
    .tab-content {
        border: 1px solid #dee2e6;
        border-top: none;
        padding: 20px;
        border-radius: 0 0 .25rem .25rem;
        background-color: #fafafa;
    }
    .form-label {
        font-weight: 600;
        color: #495057;
    }
    .text-success {
        color: #28a745 !important;
    }
    .btn-success {
        background-color: #28a745;
        border-color: #28a745;
    }
    .btn-outline-success {
        color: #28a745;
        border-color: #28a745;
    }
    .btn-outline-success:hover {
        background-color: #28a745;
        border-color: #28a745;
        color: white;
    }
    .card-header.bg-success {
        background-color: #28a745 !important;
    }
</style>

<!-- Bootstrap JS (necesario para que bootstrap.Tab exista) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- SweetAlert2 para alertas -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM completamente cargado y Bootstrap ya disponible.');

    // --- Selección de Elementos DOM ---
    const form = document.getElementById('registerAdultoMayorForm');
    const nextButton = document.getElementById('nextButton');
    const prevButton = document.getElementById('prevButton');
    const datosPersonalesTabEl = document.getElementById('datos-personales-tab');
    const datosAdultoMayorTabEl = document.getElementById('datos-adulto-mayor-tab');
    
    // --- Lógica para campo condicional de migración ---
    const migranteSelect = document.getElementById('migrante');
    const origenMigracionWrapper = document.getElementById('origen_migracion_wrapper');
    const origenMigracionInput = document.getElementById('origen_migracion');
    const origenMigracionAsterisk = document.getElementById('origen_migracion_asterisk');

    function toggleOrigenMigracion() {
        if (migranteSelect && origenMigracionWrapper && origenMigracionInput && origenMigracionAsterisk) {
            if (migranteSelect.value === '1') { // '1' para "Es migrante"
                origenMigracionWrapper.style.display = 'block';
                origenMigracionInput.required = true;
                origenMigracionAsterisk.style.display = 'inline';
            } else {
                origenMigracionWrapper.style.display = 'none';
                origenMigracionInput.required = false;
                origenMigracionInput.value = ''; // Limpiar valor al ocultar
                origenMigracionInput.classList.remove('is-invalid', 'is-valid');
                origenMigracionAsterisk.style.display = 'none';
            }
        }
    }

    if (migranteSelect) {
        migranteSelect.addEventListener('change', toggleOrigenMigracion);
        toggleOrigenMigracion();
    }

    // --- Instanciación de pestañas (Bootstrap.Tab) ---
    let bsTabDatosPersonales = null;
    let bsTabDatosAdultoMayor = null;
    if (datosPersonalesTabEl && typeof bootstrap !== 'undefined' && bootstrap.Tab) {
        bsTabDatosPersonales = new bootstrap.Tab(datosPersonalesTabEl);
    }
    if (datosAdultoMayorTabEl && typeof bootstrap !== 'undefined' && bootstrap.Tab) {
        bsTabDatosAdultoMayor = new bootstrap.Tab(datosAdultoMayorTabEl);
    }

    // =========================================================================
    // === INICIO: MODIFICACIÓN DE VALIDACIÓN JAVASCRIPT ===
    // =========================================================================
    function validateField(input) {
        input.classList.remove('is-invalid', 'is-valid');
        const feedback = input.parentElement.querySelector('.invalid-feedback');
        let isValid = true;

        if (input.required) {
            if (input.type === 'checkbox') {
                if (!input.checked) {
                    isValid = false;
                }
            } else if (!input.value.trim()) {
                isValid = false;
            }
        }

        // Se elimina la validación de formato 'pattern' del lado del cliente.
        // La validación compleja (como el formato de CI) ahora es manejada
        // exclusivamente por el backend para mayor seguridad y consistencia.
        // Este script solo se encarga de verificar que los campos requeridos no estén vacíos.

        if (isValid) {
            input.classList.add('is-valid');
        } else {
            input.classList.add('is-invalid');
            if (feedback && !input.value.trim() && input.required) {
                // Se puede mostrar un mensaje genérico si se desea, pero el div ya tiene uno.
            }
        }
        return isValid;
    }
    // =========================================================================
    // === FIN: MODIFICACIÓN DE VALIDACIÓN JAVASCRIPT ===
    // =========================================================================

    // Añadir listeners de validación a todos los required
    form.querySelectorAll('input[required], select[required], textarea[required]').forEach(input => {
        input.addEventListener('input', () => validateField(input));
        input.addEventListener('change', () => validateField(input));
    });

    function validateTab(tabPaneId) {
        const tabPane = document.getElementById(tabPaneId);
        if (!tabPane) return { isValid: true, firstInvalidElement: null };
        let allFieldsValid = true;
        let firstInvalid = null;
        const fields = tabPane.querySelectorAll('input, select, textarea');
        fields.forEach(field => {
            if (field.required && (field.offsetWidth > 0 || field.offsetHeight > 0)) {
                if (!validateField(field)) {
                    allFieldsValid = false;
                    if (!firstInvalid) firstInvalid = field;
                }
            }
        });
        return { isValid: allFieldsValid, firstInvalidElement: firstInvalid };
    }

    // --- Botón "Siguiente" ---
    if (nextButton) {
        nextButton.addEventListener('click', function() {
            console.log('Botón "Siguiente" presionado.');
            const validationResult = validateTab('datosPersonales');
            if (validationResult.isValid) {
                if (bsTabDatosAdultoMayor) {
                    bsTabDatosAdultoMayor.show();
                }
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'Campos Incompletos o Inválidos',
                    html: 'Por favor, revise los campos marcados en la pestaña "Datos Personales".',
                    confirmButtonText: 'Entendido'
                }).then(() => {
                    if (validationResult.firstInvalidElement) {
                        validationResult.firstInvalidElement.focus();
                        validationResult.firstInvalidElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                });
            }
        });
    }

    // --- Botón "Anterior" ---
    if (prevButton) {
        prevButton.addEventListener('click', function() {
            console.log('Botón "Anterior" presionado.');
            if (bsTabDatosPersonales) {
                bsTabDatosPersonales.show();
            }
        });
    }

    // --- Envío del Formulario ---
    form.addEventListener('submit', function(event) {
        console.log('Evento submit del formulario disparado.');
        event.preventDefault();

        let allValid = true;
        let firstInvalidElementOverall = null;
        let tabIdOfFirstError = null;
        let errorMessages = [];

        // Validar Pestaña 1
        const personalValidation = validateTab('datosPersonales');
        if (!personalValidation.isValid) {
            allValid = false;
            firstInvalidElementOverall = personalValidation.firstInvalidElement;
            tabIdOfFirstError = 'datos-personales-tab';
            document.getElementById('datosPersonales').querySelectorAll('.is-invalid').forEach(el => {
                if (el.offsetWidth > 0 || el.offsetHeight > 0) {
                    const labelEl = form.querySelector(`label[for="${el.id}"]`);
                    const label = labelEl ? labelEl.textContent.replace('*','').trim() : (el.name || el.id);
                    const feedbackMsg = el.parentElement.querySelector('.invalid-feedback')?.textContent || 'Error desconocido.';
                    errorMessages.push(`<b>${label}:</b> ${feedbackMsg}`);
                }
            });
        }

        // Validar Pestaña 2
        const adultMayorValidation = validateTab('datosAdultoMayor');
        if (!adultMayorValidation.isValid) {
            allValid = false;
            if (!firstInvalidElementOverall) {
                firstInvalidElementOverall = adultMayorValidation.firstInvalidElement;
                tabIdOfFirstError = 'datos-adulto-mayor-tab';
            }
            document.getElementById('datosAdultoMayor').querySelectorAll('.is-invalid').forEach(el => {
                if (el.offsetWidth > 0 || el.offsetHeight > 0) {
                    const labelEl = form.querySelector(`label[for="${el.id}"]`);
                    const label = labelEl ? labelEl.textContent.replace('*','').trim() : (el.name || el.id);
                    const feedbackMsg = el.parentElement.querySelector('.invalid-feedback')?.textContent || 'Error desconocido.';
                    if (!errorMessages.some(msg => msg.startsWith(`<b>${label}:`))) {
                        errorMessages.push(`<b>${label}:</b> ${feedbackMsg}`);
                    }
                }
            });
        }

        errorMessages = [...new Set(errorMessages)];

        if (!allValid) {
            let htmlErrorMessages = 'Por favor, corrija los siguientes errores:<br><ul style="text-align: left; margin-left: 20px; padding-left:20px; list-style-type: disc;">';
            errorMessages.forEach(msg => {
                htmlErrorMessages += `<li>${msg}</li>`;
            });
            htmlErrorMessages += '</ul>';

            Swal.fire({
                icon: 'error',
                title: 'Formulario Incompleto o Inválido',
                html: htmlErrorMessages,
                confirmButtonText: 'Entendido',
                customClass: {
                    htmlContainer: 'text-start'
                }
            }).then(() => {
                if (tabIdOfFirstError && firstInvalidElementOverall) {
                    const tabButton = document.getElementById(tabIdOfFirstError);
                    if (tabButton && typeof bootstrap !== 'undefined' && bootstrap.Tab) {
                        const bsTabInstance = bootstrap.Tab.getInstance(tabButton) || new bootstrap.Tab(tabButton);
                        bsTabInstance.show();
                        setTimeout(() => {
                            firstInvalidElementOverall.focus();
                            firstInvalidElementOverall.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        }, 250);
                    } else {
                        firstInvalidElementOverall.focus();
                        firstInvalidElementOverall.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                }
            });
        } else {
            console.log('Formulario válido. Enviando...');
            Swal.fire({
                title: 'Procesando...',
                text: 'Registrando adulto mayor.',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });
            form.submit();
        }
    });

    const fechaInput = document.getElementById('fecha');
    if (fechaInput && !fechaInput.value) {
        const today = new Date().toISOString().split('T')[0];
        fechaInput.value = today;
    }
});
</script>

@include('footer')
