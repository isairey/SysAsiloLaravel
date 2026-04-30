@extends('layouts.main')

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        @if(isset($kinesiologia) && $kinesiologia->exists)
            Editar Ficha de Kinesiología
        @else
            Registrar Ficha de Kinesiología
        @endif
    </title>
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/Medico/registrarFichaKine.css') }}"> 
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

@section('content')
<div class="navigation-buttons full-width">
    <a href="{{ route('responsable.kinesiologia.fisiokine.indexKine') }}" class="btn btn-secondary">
        <i data-feather="arrow-left"></i> Volver al listado
    </a>
</div>
<h6 style="color: white;">
    @if(isset($kinesiologia) && $kinesiologia->exists)
        EDITAR FICHA DE KINESIOLOGÍA PARA: <strong>{{ optional($adulto->persona)->nombres }} {{ optional($adulto->persona)->primer_apellido }} {{ optional($adulto->persona)->segundo_apellido }}</strong>
    @else
        REGISTRO DE FICHA DE KINESIOLOGÍA PARA: <strong>{{ optional($adulto->persona)->nombres }} {{ optional($adulto->persona)->primer_apellido }} {{ optional($adulto->persona)->segundo_apellido }}</strong>
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
    <form id="kinesiologia-form" action="@if(isset($kinesiologia) && $kinesiologia->exists)
                                        {{ route('responsable.kinesiologia.fisiokine.updateKine', ['cod_kine' => $kinesiologia->cod_kine]) }}
                                      @else
                                        {{ route('responsable.kinesiologia.fisiokine.storeKine', ['id_adulto' => $adulto->id_adulto]) }}
                                      @endif" method="POST">
        @csrf
        @if(isset($kinesiologia) && $kinesiologia->exists)
            @method('PUT')
        @endif

        <input type="hidden" name="id_adulto" value="{{ $adulto->id_adulto }}">
        @if (isset($historiaClinica) && $historiaClinica)
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
            <div class="col-md-6">
                <div class="form-group">
                    <label for="lugar_nacimiento_hc">LUGAR DE NACIMIENTO (HIST. CLÍNICA):</label>
                    <div class="read-only-field">
                        @if(optional($historiaClinica)->lugar_nacimiento_provincia || optional($historiaClinica)->lugar_nacimiento_departamento)
                            {{ optional($historiaClinica)->lugar_nacimiento_provincia ?? '' }}
                            @if(optional($historiaClinica)->lugar_nacimiento_provincia && optional($historiaClinica)->lugar_nacimiento_departamento)
                                , 
                            @endif
                            {{ optional($historiaClinica)->lugar_nacimiento_departamento ?? '' }}
                        @else
                            N/A
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="barrio_am">BARRIO:</label>
                    <div class="read-only-field">
                        {{ optional($adulto->persona)->zona_comunidad ?? 'N/A' }} / {{ optional($adulto->persona)->domicilio ?? 'N/A' }}
                    </div>
                </div>
            </div>
        </div>

        <h5 class="mt-4">SERVICIOS REALIZADOS:</h5>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <div class="checkbox-group">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="entrenamiento_funcional" id="entrenamiento_funcional" value="1"
                                {{ old('entrenamiento_funcional', (isset($kinesiologia) && $kinesiologia->exists) ? $kinesiologia->entrenamiento_funcional : false) ? 'checked' : '' }}>
                            <label1 class="form-check-label" for="entrenamiento_funcional">Entrenamiento Funcional</label1>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="gimnasio_maquina" id="gimnasio_maquina" value="1"
                                {{ old('gimnasio_maquina', (isset($kinesiologia) && $kinesiologia->exists) ? $kinesiologia->gimnasio_maquina : false) ? 'checked' : '' }}>
                            <label1 class="form-check-label" for="gimnasio_maquina">Gimnasio Máquinas</label1>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="aquafit" id="aquafit" value="1"
                                {{ old('aquafit', (isset($kinesiologia) && $kinesiologia->exists) ? $kinesiologia->aquafit : false) ? 'checked' : '' }}>
                            <label1 class="form-check-label" for="aquafit">Aquafit</label1>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="hidroterapia" id="hidroterapia" value="1"
                                {{ old('hidroterapia', (isset($kinesiologia) && $kinesiologia->exists) ? $kinesiologia->hidroterapia : false) ? 'checked' : '' }}>
                            <label1 class="form-check-label" for="hidroterapia">Hidroterapia</label1>
                        </div>
                        @error('entrenamiento_funcional')<span class="error-message">{{ $message }}</span>@enderror
                        @error('gimnasio_maquina')<span class="error-message">{{ $message }}</span>@enderror
                        @error('aquafit')<span class="error-message">{{ $message }}</span>@enderror
                        @error('hidroterapia')<span class="error-message">{{ $message }}</span>@enderror
                    </div>
                </div>
            </div>
        </div>

        <h5 class="mt-4">TURNOS:</h5>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <div class="checkbox-group">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="manana" id="manana" value="1"
                                {{ old('manana', (isset($kinesiologia) && $kinesiologia->exists) ? $kinesiologia->manana : false) ? 'checked' : '' }}>
                            <label1 class="form-check-label" for="manana">Mañana</label1>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="tarde" id="tarde" value="1"
                                {{ old('tarde', (isset($kinesiologia) && $kinesiologia->exists) ? $kinesiologia->tarde : false) ? 'checked' : '' }}>
                            <label1 class="form-check-label" for="tarde">Tarde</label1>
                        </div>
                        @error('manana')<span class="error-message">{{ $message }}</span>@enderror
                        @error('tarde')<span class="error-message">{{ $message }}</span>@enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="navigation-buttons full-width">
            <button type="submit" class="btn btn-primary">
                <i data-feather="save"></i> @if(isset($kinesiologia) && $kinesiologia->exists) Guardar Cambios @else Guardar Ficha @endif
            </button>
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

        // Validaciones con SweetAlert2
        const form = document.getElementById('kinesiologia-form');
        if (form) {
            form.addEventListener('submit', function (event) {
                let errors = [];

                // Validar Servicios Realizados (al menos uno debe estar seleccionado)
                const servicios = [
                    { id: 'entrenamiento_funcional', label: 'Entrenamiento Funcional' },
                    { id: 'gimnasio_maquina', label: 'Gimnasio Máquinas' },
                    { id: 'aquafit', label: 'Aquafit' },
                    { id: 'hidroterapia', label: 'Hidroterapia' }
                ];
                const algunServicioSeleccionado = servicios.some(field => document.getElementById(field.id).checked);
                if (!algunServicioSeleccionado) {
                    errors.push('Debe seleccionar al menos un servicio realizado (Entrenamiento Funcional, Gimnasio Máquinas, Aquafit o Hidroterapia).');
                }

                // Validar Turnos (al menos uno debe estar seleccionado)
                const turnos = [
                    { id: 'manana', label: 'Mañana' },
                    { id: 'tarde', label: 'Tarde' }
                ];
                const algunTurnoSeleccionado = turnos.some(field => document.getElementById(field.id).checked);
                if (!algunTurnoSeleccionado) {
                    errors.push('Debe seleccionar al menos un turno (Mañana o Tarde).');
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