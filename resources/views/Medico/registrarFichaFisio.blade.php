@extends('layouts.main')
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        @if($fisioterapia->exists)
            Editar Ficha de Fisioterapia
        @else
            Registrar Ficha de Fisioterapia
        @endif
    </title>
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/Medico/registrarFichaFisio.css') }}"> 
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
@section('content')
<body>
    <div class="navigation-buttons full-width">
        <a href="{{ route('responsable.fisioterapia.fisiokine.indexFisio') }}" class="btn btn-secondary">
            <i data-feather="arrow-left"></i> Volver al listado
        </a>
    </div>
    <h6 style="color:white;">
        @if($fisioterapia->exists)
            EDITAR FICHA DE FISIOTERAPIA PARA: <strong>{{ optional($adulto->persona)->nombres }} {{ optional($adulto->persona)->primer_apellido }} {{ optional($adulto->persona)->segundo_apellido }}</strong>
        @else
            REGISTRO DE FICHA DE FISIOTERAPIA
        @endif
    </h6>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Errores en el formulario',
                    html: '<ul style="text-align: left;">' + @json($errors->all()).map(error => `<li>${error}</li>`).join('') + '</ul>',
                    confirmButtonText: 'Corregir'
                });
            });
        </script>
    @endif
    <div class="form-section">
        <form id="fisioterapia-form" action="@if($fisioterapia->exists)
                                        {{ route('responsable.fisioterapia.fisiokine.updateFisio', ['cod_fisio' => $fisioterapia->cod_fisio]) }}
                                      @else
                                        {{ route('responsable.fisioterapia.fisiokine.storeFisio', ['id_adulto' => $adulto->id_adulto]) }}
                                      @endif" method="POST">
            @csrf
            @if($fisioterapia->exists)
                @method('PUT')
            @endif
            <input type="hidden" name="id_adulto" value="{{ $adulto->id_adulto }}">
            @if ($historiaClinica)
                <input type="hidden" name="id_historia" value="{{ $historiaClinica->id_historia }}">
            @endif
            <h5 class="mt-4">DATOS DE IDENTIFICACIÓN DEL ADULTO MAYOR:</h5>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="nombre_completo_am">NOMBRE COMPLETO:</label>
                        <div class="read-only-field">
                            {{ optional($adulto->persona)->nombres }}
                            {{ optional($adulto->persona)->primer_apellido }}
                            {{ optional($adulto->persona)->segundo_apellido }}
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="sexo_am">SEXO:</label>
                        <div class="read-only-field">{{ optional($adulto->persona)->sexo ?? 'N/A' }}</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="edad_am">EDAD:</label>
                        <div class="read-only-field">{{ optional($adulto->persona->fecha_nacimiento) ? \Carbon\Carbon::parse($adulto->persona->fecha_nacimiento)->age . ' años' : 'N/A' }}</div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="ci_am">NÚMERO DE DOCUMENTO DE IDENTIDAD:</label>
                        <div class="read-only-field">{{ optional($adulto->persona)->ci ?? 'N/A' }}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="telefono_am">NÚMERO DE TELÉFONO:</label>
                        <div class="read-only-field">{{ optional($adulto->persona)->telefono ?? 'N/A' }}</div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="direccion_am">DIRECCIÓN DE DOMICILIO:</label>
                        <div class="read-only-field">{{ optional($adulto->persona)->domicilio ?? 'N/A' }}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="vive_con">CON QUIEN VIVE:</label>
                        <div class="read-only-field">{{ optional($adulto)->vive_con ?? 'N/A' }}</div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="estado_civil_am">ESTADO CIVIL:</label>
                        <div class="read-only-field">{{ ucfirst(optional($adulto->persona)->estado_civil ?? 'N/A') }}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="grado_instruccion_am">GRADO DE INSTRUCCIÓN:</label>
                        <div class="read-only-field">{{ optional($historiaClinica)->grado_instruccion ?? 'N/A' }}</div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="ocupacion_actual_am">OCUPACIÓN ACTUAL:</label>
                        <div class="read-only-field">{{ optional($historiaClinica)->ocupacion ?? 'N/A' }}</div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="num_emergencia" class="form-label">NÚMEROS DE EMERGENCIA:</label>
                        <input type="text" id="num_emergencia" name="num_emergencia" class="form-control {{ $errors->has('num_emergencia') ? 'is-invalid' : '' }}"
                               value="{{ old('num_emergencia', $fisioterapia->num_emergencia) }}">
                        @error('num_emergencia')<span class="error-message">{{ $message }}</span>@enderror
                    </div>
                </div>
            </div>
            <h5 class="mt-4">SITUACIÓN DE SALUD:</h5>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="enfermedades_actuales" class="form-label">ENFERMEDADES ACTUALES:</label>
                        <textarea id="enfermedades_actuales" name="enfermedades_actuales" rows="3" class="form-control {{ $errors->has('enfermedades_actuales') ? 'is-invalid' : '' }}">{{ old('enfermedades_actuales', $fisioterapia->enfermedades_actuales) }}</textarea>
                        <small class="form-text text-muted">Ej: Hipertensión arterial, Diabetes, Artrosis, Osteoporosis, Parkinson. Otras: (Especificar)</small>
                        @error('enfermedades_actuales')<span class="error-message">{{ $message }}</span>@enderror
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="alergias" class="form-label">ALERGIAS:</label>
                        <textarea id="alergias" name="alergias" rows="3" class="form-control {{ $errors->has('alergias') ? 'is-invalid' : '' }}">{{ old('alergias', $fisioterapia->alergias) }}</textarea>
                        <small class="form-text text-muted">Ej: Medicamentos. Indicar: / Alimentos. Indicar: / No refiere.</small>
                        @error('alergias')<span class="error-message">{{ $message }}</span>@enderror
                    </div>
                </div>
            </div>
            <h5 class="mt-4">PLAN DE PARTICIPACIÓN INDIVIDUAL O GRUPAL:</h5>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="fecha_programacion" class="form-label">FECHA DE PROGRAMACIÓN:</label>
                        <input type="date" id="fecha_programacion" name="fecha_programacion" class="form-control {{ $errors->has('fecha_programacion') ? 'is-invalid' : '' }}"
                               value="{{ old('fecha_programacion', optional(optional($fisioterapia)->fecha_programacion)->format('Y-m-d') ?? now()->format('Y-m-d')) }}">
                        @error('fecha_programacion')<span class="error-message">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="motivo_consulta" class="form-label">MOTIVO DE CONSULTA:</label>
                        <textarea id="motivo_consulta" name="motivo_consulta" rows="3" class="form-control {{ $errors->has('motivo_consulta') ? 'is-invalid' : '' }}">{{ old('motivo_consulta', $fisioterapia->motivo_consulta) }}</textarea>
                        @error('motivo_consulta')<span class="error-message">{{ $message }}</span>@enderror
                    </div>
                </div>
            </div>
            <!-- INICIO: NUEVOS CAMPOS -->
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="fecha_inicio" class="form-label">FECHA DE INICIO:</label>
                        <input type="date" id="fecha_inicio" name="fecha_inicio" class="form-control {{ $errors->has('fecha_inicio') ? 'is-invalid' : '' }}"
                               value="{{ old('fecha_inicio', optional(optional($fisioterapia)->fecha_inicio)->format('Y-m-d')) }}">
                        @error('fecha_inicio')<span class="error-message">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="fecha_fin" class="form-label">FECHA DE FIN:</label>
                        <input type="date" id="fecha_fin" name="fecha_fin" class="form-control {{ $errors->has('fecha_fin') ? 'is-invalid' : '' }}"
                               value="{{ old('fecha_fin', optional(optional($fisioterapia)->fecha_fin)->format('Y-m-d')) }}">
                        @error('fecha_fin')<span class="error-message">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="numero_sesiones" class="form-label">NÚMERO DE SESIONES:</label>
                        <input type="number" id="numero_sesiones" name="numero_sesiones" class="form-control {{ $errors->has('numero_sesiones') ? 'is-invalid' : '' }}"
                               value="{{ old('numero_sesiones', $fisioterapia->numero_sesiones) }}" min="0">
                        @error('numero_sesiones')<span class="error-message">{{ $message }}</span>@enderror
                    </div>
                </div>
            </div>
            <!-- FIN: NUEVOS CAMPOS -->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="solicitud_atencion" class="form-label">SOLICITUD ATENCIÓN:</label>
                        <textarea id="solicitud_atencion" name="solicitud_atencion" rows="3" class="form-control {{ $errors->has('solicitud_atencion') ? 'is-invalid' : '' }}">{{ old('solicitud_atencion', $fisioterapia->solicitud_atencion) }}</textarea>
                        @error('solicitud_atencion')<span class="error-message">{{ $message }}</span>@enderror
                    </div>
                </div>
            </div>
            <h5 class="mt-4">EQUIPOS:</h5>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="equipos" class="form-label">EQUIPOS UTILIZADOS:</label>
                        <input type="text" id="equipos" name="equipos" class="form-control {{ $errors->has('equipos') ? 'is-invalid' : '' }}"
                               value="{{ old('equipos', $fisioterapia->equipos) }}">
                        <small class="form-text text-muted">Ej: ELECTRO ESTIMULADOR, ULTRASONIDO, OTROS (Especificar)</small>
                        @error('equipos')<span class="error-message">{{ $message }}</span>@enderror
                    </div>
                </div>
            </div>
            <div class="navigation-buttons full-width">
                <button type="submit" class="btn btn-primary">
                    <i data-feather="save"></i> @if($fisioterapia->exists) Guardar Cambios @else Guardar Ficha @endif
                </button>
            </div>
        </form>
    </div>
</body>
@endsection
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Inicializar Feather Icons
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
        // Validaciones con SweetAlert2
        const form = document.getElementById('fisioterapia-form');
        if (form) {
            form.addEventListener('submit', function (event) {
                let errors = [];
                // Validar Números de Emergencia
                const numEmergencia = document.getElementById('num_emergencia');
                if (numEmergencia.value.trim() && !/^[0-9\s\-+]+$/.test(numEmergencia.value.trim())) {
                    errors.push('El campo "Números de Emergencia" debe contener solo dígitos, espacios, guiones o el signo "+".');
                } else if (numEmergencia.value.length > 20) {
                    errors.push('El campo "Números de Emergencia" no debe exceder los 20 caracteres.');
                }
                // Validar Situación de Salud
                const situacionSalud = [
                    { id: 'enfermedades_actuales', label: 'Enfermedades Actuales' },
                    { id: 'alergias', label: 'Alergias' }
                ];
                situacionSalud.forEach(field => {
                    const input = document.getElementById(field.id);
                    if (input.value.length > 1000) {
                        errors.push(`El campo "${field.label}" no debe exceder los 1000 caracteres.`);
                    }
                });
                
                // --- Validar Plan de Participación ---
                const fechaProgramacion = document.getElementById('fecha_programacion');
                if (!fechaProgramacion.value.trim()) {
                    errors.push('El campo "Fecha de Programación" es obligatorio.');
                } else {
                    const today = new Date();
                    today.setHours(0, 0, 0, 0);
                    const selectedDate = new Date(fechaProgramacion.value + 'T00:00:00'); // Asegurar que se compare solo la fecha
                    
                    // La validación original permitía la fecha actual. La mantendré.
                    // Si se quisiera que solo sea la fecha actual o pasada:
                    // if (selectedDate > today) {
                    //     errors.push('El campo "Fecha de Programación" no puede ser una fecha futura.');
                    // }
                }

                // --- Validar NUEVOS CAMPOS ---
                const fechaInicioInput = document.getElementById('fecha_inicio');
                const fechaFinInput = document.getElementById('fecha_fin');
                const numeroSesionesInput = document.getElementById('numero_sesiones');

                const fechaInicio = fechaInicioInput.value.trim();
                const fechaFin = fechaFinInput.value.trim();
                const numeroSesiones = numeroSesionesInput.value.trim();

                if (fechaInicio && fechaFin && new Date(fechaFin) < new Date(fechaInicio)) {
                    errors.push('La "Fecha de Fin" no puede ser anterior a la "Fecha de Inicio".');
                }

                if (numeroSesiones) {
                    if (!/^\d+$/.test(numeroSesiones)) {
                        errors.push('El "Número de Sesiones" debe ser un número entero.');
                    } else if (parseInt(numeroSesiones, 10) < 0) {
                        errors.push('El "Número de Sesiones" no puede ser negativo.');
                    }
                }
                // --- FIN Validar NUEVOS CAMPOS ---

                const planParticipacion = [
                    { id: 'motivo_consulta', label: 'Motivo de Consulta' },
                    { id: 'solicitud_atencion', label: 'Solicitud Atención' }
                ];
                planParticipacion.forEach(field => {
                    const input = document.getElementById(field.id);
                    if (input.value.length > 1000) {
                        errors.push(`El campo "${field.label}" no debe exceder los 1000 caracteres.`);
                    }
                });
                
                // Validar Equipos
                const equipos = document.getElementById('equipos');
                if (equipos.value.length > 255) {
                    errors.push('El campo "Equipos Utilizados" no debe exceder los 255 caracteres.');
                }
                
                // Mostrar errores con SweetAlert2
                if (errors.length > 0) {
                    event.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Errores en el formulario',
                        html: '<ul style="text-align: left;">' + errors.map(error => `<li>${error}</li>`).join('') + '</ul>',
                        confirmButtonText: 'Corregir'
                    });
                }
            });
        }
    });
</script>
@endpush

