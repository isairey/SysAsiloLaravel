@extends('layouts.main')

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        @if($modoEdicion)
            Editar Atención de Enfermería
        @else
            Registrar Atención de Enfermería
        @endif
    </title>
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/Medico/registrarAtencionEnfermeria.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

@section('content')
<body>
    <div class="navigation-buttons">
        <a href="{{ route('responsable.enfermeria.enfermeria.index') }}" class="btn btn-secondary">Volver al listado</a>
    </div>

    <h6 style="color: white;">
        @if($modoEdicion)
            Editar Atención de Enfermería para: <strong>{{ optional($adulto->persona)->nombres }} {{ optional($adulto->persona)->primer_apellido }} {{ optional($adulto->persona)->segundo_apellido }}</strong>
        @else
            REGISTRO DE ATENCIÓN DE ENFERMERÍA
        @endif
    </h6>

    @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    icon: 'success',
                    title: 'Éxito',
                    html: '<p>{{ session('success') }}</p>',
                    confirmButtonText: 'Aceptar'
                });
            });
        </script>
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
        <form id="atencion-enfermeria-form" action="{{ $modoEdicion ? route('responsable.enfermeria.enfermeria.update', ['cod_enf' => optional($fichaEnfermeria)->cod_enf]) : route('responsable.enfermeria.enfermeria.store', ['id_adulto' => $adulto->id_adulto]) }}" method="POST">
            @csrf
            @if($modoEdicion)
                @method('PUT')
            @endif

            <input type="hidden" name="id_adulto" value="{{ $adulto->id_adulto }}">

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
                        <div class="read-only-field">{{ optional($adulto->persona)->edad ?? 'N/A' }}</div>
                    </div>
                </div>
            </div>

            <h5 class="mt-4">CONTROL DE SIGNOS VITALES:</h5>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="presion_arterial">Presión Arterial:</label>
                        <input type="text" id="presion_arterial" name="presion_arterial" class="form-control"
                               value="{{ old('presion_arterial', optional($fichaEnfermeria)->presion_arterial ?? '-') }}">
                        @error('presion_arterial')<span style="color: red;">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="frecuencia_cardiaca">Frecuencia Cardíaca:</label>
                        <input type="text" id="frecuencia_cardiaca" name="frecuencia_cardiaca" class="form-control"
                               value="{{ old('frecuencia_cardiaca', optional($fichaEnfermeria)->frecuencia_cardiaca ?? '-') }}">
                        @error('frecuencia_cardiaca')<span style="color: red;">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="frecuencia_respiratoria">Frecuencia Respiratoria:</label>
                        <input type="text" id="frecuencia_respiratoria" name="frecuencia_respiratoria" class="form-control"
                               value="{{ old('frecuencia_respiratoria', optional($fichaEnfermeria)->frecuencia_respiratoria ?? '-') }}">
                        @error('frecuencia_respiratoria')<span style="color: red;">{{ $message }}</span>@enderror
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="pulso">Pulso:</label>
                        <input type="text" id="pulso" name="pulso" class="form-control"
                               value="{{ old('pulso', optional($fichaEnfermeria)->pulso ?? '-') }}">
                        @error('pulso')<span style="color: red;">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="temperatura">Temperatura:</label>
                        <input type="text" id="temperatura" name="temperatura" class="form-control"
                               value="{{ old('temperatura', optional($fichaEnfermeria)->temperatura ?? '-') }}">
                        @error('temperatura')<span style="color: red;">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="control_oximetria">Control Oximetría:</label>
                        <input type="text" id="control_oximetria" name="control_oximetria" class="form-control"
                               value="{{ old('control_oximetria', optional($fichaEnfermeria)->control_oximetria ?? '-') }}">
                        @error('control_oximetria')<span style="color: red;">{{ $message }}</span>@enderror
                    </div>
                </div>
            </div>

            <h5 class="mt-4">ATENCIONES DE ENFERMERÍA:</h5>
            <div class="form-group">
                <label for="inyectables">INYECTABLES:</label>
                <textarea id="inyectables" name="inyectables" rows="3" class="form-control">{{ old('inyectables', optional($fichaEnfermeria)->inyectables ?? '-') }}</textarea>
                @error('inyectables')<span style="color: red;">{{ $message }}</span>@enderror
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="peso_talla">PESO Y TALLA:</label>
                        <input type="text" id="peso_talla" name="peso_talla" class="form-control"
                               value="{{ old('peso_talla', optional($fichaEnfermeria)->peso_talla ?? '-') }}">
                        @error('peso_talla')<span style="color: red;">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="orientacion_alimentacion">ORIENTACIÓN ALIMENTACIÓN:</label>
                        <textarea id="orientacion_alimentacion" name="orientacion_alimentacion" rows="3" class="form-control">{{ old('orientacion_alimentacion', optional($fichaEnfermeria)->orientacion_alimentacion ?? '-') }}</textarea>
                        @error('orientacion_alimentacion')<span style="color: red;">{{ $message }}</span>@enderror
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="lavado_oidos">LAVADO DE OÍDOS:</label>
                        <textarea id="lavado_oidos" name="lavado_oidos" rows="3" class="form-control">{{ old('lavado_oidos', optional($fichaEnfermeria)->lavado_oidos ?? '-') }}</textarea>
                        @error('lavado_oidos')<span style="color: red;">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="orientacion_tratamiento">ORIENTACIÓN TRATAMIENTO:</label>
                        <textarea id="orientacion_tratamiento" name="orientacion_tratamiento" rows="3" class="form-control">{{ old('orientacion_tratamiento', optional($fichaEnfermeria)->orientacion_tratamiento ?? '-') }}</textarea>
                        @error('orientacion_tratamiento')<span style="color: red;">{{ $message }}</span>@enderror
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="curacion">CURACIÓN:</label>
                        <textarea id="curacion" name="curacion" rows="3" class="form-control">{{ old('curacion', optional($fichaEnfermeria)->curacion ?? '-') }}</textarea>
                        @error('curacion')<span style="color: red;">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="adm_medicamentos">ADMINISTRACIÓN MEDICAMENTOS:</label>
                        <textarea id="adm_medicamentos" name="adm_medicamentos" rows="3" class="form-control">{{ old('adm_medicamentos', optional($fichaEnfermeria)->adm_medicamentos ?? '-') }}</textarea>
                        @error('adm_medicamentos')<span style="color: red;">{{ $message }}</span>@enderror
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="derivacion">DERIVACIÓN:</label>
                <textarea id="derivacion" name="derivacion" rows="3" class="form-control">{{ old('derivacion', optional($fichaEnfermeria)->derivacion ?? '-') }}</textarea>
                @error('derivacion')<span style="color: red;">{{ $message }}</span>@enderror
            </div>

            <div class="navigation-buttons full-width">
                <button type="submit" class="btn btn-primary">
                    <i class="fe fe-save"></i> @if($modoEdicion) Guardar Cambios @else Guardar Atención @endif
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
        const form = document.getElementById('atencion-enfermeria-form');
        if (form) {
            form.addEventListener('submit', function (event) {
                let errors = [];

                // Validar Signos Vitales
                const signosVitales = [
                    { id: 'presion_arterial', label: 'Presión Arterial', regex: /^\d{1,3}\/\d{1,3}$/, example: '120/80', maxLength: 255 },
                    { id: 'frecuencia_cardiaca', label: 'Frecuencia Cardíaca', isNumber: true, maxLength: 255 },
                    { id: 'frecuencia_respiratoria', label: 'Frecuencia Respiratoria', isNumber: true, maxLength: 255 },
                    { id: 'pulso', label: 'Pulso', isNumber: true, maxLength: 255 },
                    { id: 'temperatura', label: 'Temperatura', regex: /^\d{1,2}(\.\d{1,2})?$/, example: '36.5', maxLength: 255 },
                    { id: 'control_oximetria', label: 'Control Oximetría', isNumber: true, maxLength: 255 }
                ];
                signosVitales.forEach(field => {
                    const input = document.getElementById(field.id);
                    const value = input.value.trim();
                    if (value !== '' && value !== '-') {
                        if (field.regex && !field.regex.test(value)) {
                            errors.push(`El campo "${field.label}" debe tener el formato correcto (ejemplo: ${field.example}).`);
                        } else if (field.isNumber) {
                            const numValue = parseFloat(value);
                            if (isNaN(numValue) || numValue < 0) {
                                errors.push(`El campo "${field.label}" debe ser un número positivo.`);
                            }
                        }
                        if (value.length > field.maxLength) {
                            errors.push(`El campo "${field.label}" no debe exceder los ${field.maxLength} caracteres.`);
                        }
                    }
                });

                // Validar Atenciones de Enfermería
                const atenciones = [
                    { id: 'inyectables', label: 'Inyectables', maxLength: 1000 },
                    { id: 'orientacion_alimentacion', label: 'Orientación Alimentación', maxLength: 1000 },
                    { id: 'lavado_oidos', label: 'Lavado de Oídos', maxLength: 1000 },
                    { id: 'orientacion_tratamiento', label: 'Orientación Tratamiento', maxLength: 1000 },
                    { id: 'curacion', label: 'Curación', maxLength: 1000 },
                    { id: 'adm_medicamentos', label: 'Administración Medicamentos', maxLength: 1000 },
                    { id: 'derivacion', label: 'Derivación', maxLength: 255 }
                ];
                atenciones.forEach(field => {
                    const input = document.getElementById(field.id);
                    const value = input.value.trim();
                    if (value !== '' && value !== '-') {
                        if (value.length > field.maxLength) {
                            errors.push(`El campo "${field.label}" no debe exceder los ${field.maxLength} caracteres.`);
                        }
                    }
                });

                // Validar Peso y Talla
                const pesoTalla = document.getElementById('peso_talla');
                const pesoTallaValue = pesoTalla.value.trim();
                if (pesoTallaValue !== '' && pesoTallaValue !== '-') {
                    if (!/^\d{1,3}\/\d{1,3}$/.test(pesoTallaValue)) {
                        errors.push('El campo "Peso y Talla" debe tener el formato correcto (ejemplo: 70/165).');
                    }
                    if (pesoTallaValue.length > 255) {
                        errors.push('El campo "Peso y Talla" no debe exceder los 255 caracteres.');
                    }
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