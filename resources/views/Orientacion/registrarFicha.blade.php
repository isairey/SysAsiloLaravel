@extends('layouts.main')

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        @if($modoEdicion)
            Editar Ficha de Orientación
        @else
            Registrar Ficha de Orientación
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
            <a href="{{ route('legal.orientacion.index') }}" class="btn">Volver al listado</a>
        </div>

        <h6 style="color: white">
            @if($modoEdicion)
                Editar Ficha de Orientación para: <strong>{{ optional($adulto->persona)->nombres }} {{ optional($adulto->persona)->primer_apellido }} {{ optional($adulto->persona)->segundo_apellido }}</strong>
            @else
                FICHA DE ORIENTACIÓN
            @endif
        </h6>

        {{-- Manejo de notificaciones con SweetAlert2 --}}
        @if(session('success'))
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: '¡Éxito!',
                        text: '{{ session('success') }}',
                        showConfirmButton: false,
                        timer: 4500,
                        timerProgressBar: true
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
                        html: '<ul style="text-align: left;">' + @json($errors->all()).map(error => <li>${error}</li>).join('') + '</ul>',
                        confirmButtonText: 'Corregir'
                    });
                });
            </script>
        @endif

        <div class="form-section">
            <form id="ficha-orientacion-form" action="{{ $modoEdicion ? route('legal.orientacion.update', ['cod_or' => optional($orientacion)->cod_or]) : route('legal.orientacion.store') }}" method="POST">
                @csrf
                @if($modoEdicion)
                    @method('PUT')
                @endif

                <input type="hidden" name="id_adulto" value="{{ $adulto->id_adulto }}">

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label1 for="fecha_ingreso">FECHA DE INGRESO:</label1>
                            <input type="date" id="fecha_ingreso" name="fecha_ingreso" class="form-control"
                                value="{{ old('fecha_ingreso', optional($orientacion)->fecha_ingreso ? \Carbon\Carbon::parse($orientacion->fecha_ingreso)->format('Y-m-d') : date('Y-m-d')) }}">
                            @error('fecha_ingreso')
                                <span style="color: red;">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label1 for="nro_caso">CASO Nº:</label1>
                            <div class="read-only-field">{{ optional($adulto)->nro_caso ?? 'N/A' }}</div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label1>Tipo de Orientación:</label1>
                            <div class="radio-group">
                                <div>
                                    <input type="radio" id="orientacion_psicologica" name="tipo_orientacion" value="psicologica"
                                        {{ (old('tipo_orientacion') == 'psicologica' || (optional($orientacion)->tipo_orientacion == 'psicologica' && !old('tipo_orientacion'))) ? 'checked' : '' }}>
                                    <label1 for="orientacion_psicologica">PSICOLÓGICA</label1>
                                </div>
                                <div>
                                    <input type="radio" id="orientacion_social" name="tipo_orientacion" value="social"
                                        {{ (old('tipo_orientacion') == 'social' || (optional($orientacion)->tipo_orientacion == 'social' && !old('tipo_orientacion'))) ? 'checked' : '' }}>
                                    <label1 for="orientacion_social">SOCIAL</label1>
                                </div>
                                <div>
                                    <input type="radio" id="orientacion_legal" name="tipo_orientacion" value="legal"
                                        {{ (old('tipo_orientacion') == 'legal' || (optional($orientacion)->tipo_orientacion == 'legal' && !old('tipo_orientacion'))) ? 'checked' : '' }}>
                                    <label1 for="orientacion_legal">LEGAL</label1>
                                </div>
                            </div>
                            @error('tipo_orientacion')
                                <span style="color: red;">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <h5 class="mt-4">DATOS DE IDENTIFICACIÓN DEL ADULTO MAYOR Y/O SOLICITANTE:</h5>
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label1 for="nombre_completo_am">NOMBRE COMPLETO:</label1>
                            <div class="read-only-field">
                                {{ optional($adulto->persona)->nombres }}
                                {{ optional($adulto->persona)->primer_apellido }}
                                {{ optional($adulto->persona)->segundo_apellido }}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label1 for="edad_am">EDAD:</label1>
                            <div class="read-only-field">{{ optional($adulto->persona)->edad ?? 'N/A' }}</div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label1 for="barrio_comunidad_am">BARRIO/COMUNIDAD:</label1>
                            <div class="read-only-field">{{ optional($adulto->persona)->domicilio ?? 'N/A' }} / {{ optional($adulto->persona)->zona_comunidad ?? 'N/A' }}</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label1 for="telefono_am">TELÉFONO:</label1>
                            <div class="read-only-field">{{ optional($adulto->persona)->telefono ?? 'N/A' }}</div>
                        </div>
                    </div>
                </div>

                <div class="form-group full-width">
                    <label1 for="motivo_orientacion">MOTIVOS DE ORIENTACIÓN:</label1>
                    <textarea id="motivo_orientacion" name="motivo_orientacion" rows="5" class="form-control">{{ old('motivo_orientacion', optional($orientacion)->motivo_orientacion) }}</textarea>
                    @error('motivo_orientacion')
                        <span style="color: red;">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group full-width">
                    <label1 for="resultado_obtenido">RESULTADOS OBTENIDOS EN RELACIÓN A LA ENTREVISTA DE ORIENTACIÓN:</label1>
                    <textarea id="resultado_obtenido" name="resultado_obtenido" rows="5" class="form-control">{{ old('resultado_obtenido', optional($orientacion)->resultado_obtenido) }}</textarea>
                    @error('resultado_obtenido')
                        <span style="color: red;">{{ $message }}</span>
                    @enderror
                </div>

                <p class="text-muted full-width">EN CASO DE QUE SE IDENTIFIQUE ALGUN TIPO DE VIOLENCIA SE DEBE HACER LA DENUNCIA INMEDIATAMENTE POR LA VÍA CORRESPONDIENTE.</p>

                <div class="navigation-buttons full-width">
                    <button type="submit" class="btn btn-primary">
                        <i class="fe fe-save"></i> @if($modoEdicion) Guardar Cambios @else Guardar Ficha @endif
                    </button>
                </div>
            </form>
        </div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Reemplazar iconos Feather
        if (typeof feather !== 'undefined') {
            feather.replace();
        }

        // Validaciones con SweetAlert2
        const form = document.getElementById('ficha-orientacion-form');
        if (form) {
            form.addEventListener('submit', function (event) {
                let errors = [];

                // Validar Fecha de Ingreso
                const fechaIngreso = document.getElementById('fecha_ingreso').value.trim();
                if (!fechaIngreso) {
                    errors.push('El campo "Fecha de Ingreso" es obligatorio.');
                } else {
                    const fecha = new Date(fechaIngreso);
                    const hoy = new Date();
                    // Para comparar solo fechas, se ignoran las horas.
                    fecha.setHours(0,0,0,0);
                    hoy.setHours(0,0,0,0);
                    
                    if (isNaN(fecha.getTime())) {
                        errors.push('El campo "Fecha de Ingreso" debe ser una fecha válida.');
                    } else if (fecha > hoy) {
                        errors.push('El campo "Fecha de Ingreso" no puede ser una fecha futura.');
                    }
                }

                // Validar Tipo de Orientación
                const tipoOrientacion = form.querySelector('input[name="tipo_orientacion"]:checked');
                if (!tipoOrientacion) {
                    errors.push('Debe seleccionar un "Tipo de Orientación".');
                }

                // Validar Motivo de Orientación
                const motivoOrientacion = document.getElementById('motivo_orientacion').value.trim();
                if (!motivoOrientacion) {
                    errors.push('El campo "Motivos de Orientación" es obligatorio.');
                } else if (motivoOrientacion.length > 1000) {
                    errors.push('El campo "Motivos de Orientación" no debe exceder los 1000 caracteres.');
                }

                // Validar Resultado Obtenido (opcional, pero con límite)
                const resultadoObtenido = document.getElementById('resultado_obtenido').value.trim();
                if (resultadoObtenido.length > 1000) {
                    errors.push('El campo "Resultados Obtenidos" no debe exceder los 1000 caracteres.');
                }

                // Mostrar errores con SweetAlert2
                if (errors.length > 0) {
                    event.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Errores en el formulario',
                        html: '<ul style="text-align: left; padding-left: 1.5rem;">' + errors.map(error => <li>${error}</li>).join('') + '</ul>',
                        confirmButtonText: 'Corregir'
                    });
                }
            });
        }
    });
</script>
@endpush

</body>
</html>
@endsection
