@extends('layouts.main') {{-- Usamos el layout principal --}}

@section('content')
<div class="page-header">
    <h1 class="page-title">Editar Adulto Mayor</h1>
    <div>
        <ol class="breadcrumb">
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
            <li class="breadcrumb-item"><a href="{{ route('gestionar-adultomayor.index') }}">Gestionar Adultos Mayores</a></li>
            <li class="breadcrumb-item active" aria-current="page">Editar Adulto Mayor</li>
        </ol>
    </div>
</div>

{{-- Bloque para mostrar errores --}}
@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong><i class="fe fe-alert-triangle me-2"></i>¡Error de Validación!</strong> Por favor, corrija los siguientes errores:
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
@if(session('error_actualizacion'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fe fe-alert-triangle me-2"></i>{{ session('error_actualizacion') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h3 class="card-title text-white mb-0">
                    <i class="fe fe-edit me-2"></i>Formulario de Edición de Adulto Mayor
                </h3>
            </div>
            <div class="card-body">
                <form action="{{ route('gestionar-adultomayor.actualizar', $adultoMayor->ci) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <h4 class="mb-4 text-primary"><i class="fe fe-user me-2"></i>Datos Personales</h4>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="nombres" class="form-label">Nombres <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nombres') is-invalid @enderror" id="nombres" name="nombres" value="{{ old('nombres', $adultoMayor->nombres) }}" required>
                            @error('nombres')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="primer_apellido" class="form-label">Primer Apellido <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('primer_apellido') is-invalid @enderror" id="primer_apellido" name="primer_apellido" value="{{ old('primer_apellido', $adultoMayor->primer_apellido) }}" required>
                            @error('primer_apellido')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="segundo_apellido" class="form-label">Segundo Apellido</label>
                            <input type="text" class="form-control @error('segundo_apellido') is-invalid @enderror" id="segundo_apellido" name="segundo_apellido" value="{{ old('segundo_apellido', $adultoMayor->segundo_apellido) }}">
                            @error('segundo_apellido')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="ci" class="form-label">CI <span class="text-danger">*</span></label>
                            {{-- Este campo ya es correcto, no tiene 'pattern' y permitirá letras, números y guiones --}}
                            <input type="text" class="form-control @error('ci') is-invalid @enderror" id="ci" name="ci" value="{{ old('ci', $adultoMayor->ci) }}" required>
                            @error('ci')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="fecha_nacimiento" class="form-label">Fecha de Nacimiento <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('fecha_nacimiento') is-invalid @enderror" id="fecha_nacimiento" name="fecha_nacimiento" value="{{ old('fecha_nacimiento', \Carbon\Carbon::parse($adultoMayor->fecha_nacimiento)->format('Y-m-d')) }}" required>
                            @error('fecha_nacimiento')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="sexo" class="form-label">Sexo <span class="text-danger">*</span></label>
                            <select class="form-select @error('sexo') is-invalid @enderror" id="sexo" name="sexo" required>
                                <option value="" disabled>Seleccione...</option>
                                <option value="F" {{ old('sexo', $adultoMayor->sexo) == 'F' ? 'selected' : '' }}>Femenino</option>
                                <option value="M" {{ old('sexo', $adultoMayor->sexo) == 'M' ? 'selected' : '' }}>Masculino</option>
                                <option value="O" {{ old('sexo', $adultoMayor->sexo) == 'O' ? 'selected' : '' }}>Otro</option>
                            </select>
                            @error('sexo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="estado_civil" class="form-label">Estado Civil <span class="text-danger">*</span></label>
                            <select class="form-select @error('estado_civil') is-invalid @enderror" id="estado_civil" name="estado_civil" required>
                                <option value="" disabled>Seleccione...</option>
                                <option value="soltero" {{ old('estado_civil', $adultoMayor->estado_civil) == 'soltero' ? 'selected' : '' }}>Soltero/a</option>
                                <option value="casado" {{ old('estado_civil', $adultoMayor->estado_civil) == 'casado' ? 'selected' : '' }}>Casado/a</option>
                                <option value="divorciado" {{ old('estado_civil', $adultoMayor->estado_civil) == 'divorciado' ? 'selected' : '' }}>Divorciado/a</option>
                                <option value="otro" {{ old('estado_civil', $adultoMayor->estado_civil) == 'otro' ? 'selected' : '' }}>Otro</option>
                            </select>
                            @error('estado_civil')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="domicilio" class="form-label">Domicilio <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('domicilio') is-invalid @enderror" id="domicilio" name="domicilio" value="{{ old('domicilio', $adultoMayor->domicilio) }}" required>
                            @error('domicilio')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="telefono" class="form-label">Teléfono <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control @error('telefono') is-invalid @enderror" id="telefono" name="telefono" value="{{ old('telefono', $adultoMayor->telefono) }}" required>
                            @error('telefono')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="zona_comunidad" class="form-label">Zona/Comunidad</label>
                            <input type="text" class="form-control @error('zona_comunidad') is-invalid @enderror" id="zona_comunidad" name="zona_comunidad" value="{{ old('zona_comunidad', $adultoMayor->zona_comunidad) }}">
                             @error('zona_comunidad')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <hr class="my-4">

                    <h4 class="mb-4 text-primary"><i class="fe fe-activity me-2"></i>Datos Específicos del Adulto Mayor</h4>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="discapacidad" class="form-label">Discapacidades o Condiciones Especiales</label>
                            <textarea class="form-control @error('discapacidad') is-invalid @enderror" id="discapacidad" name="discapacidad" rows="3">{{ old('discapacidad', $adultoMayor->discapacidad) }}</textarea>
                            @error('discapacidad')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="vive_con" class="form-label">¿Con quién vive?</label>
                            <textarea class="form-control @error('vive_con') is-invalid @enderror" id="vive_con" name="vive_con" rows="3">{{ old('vive_con', $adultoMayor->vive_con) }}</textarea>
                            @error('vive_con')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="migrante" class="form-label">Situación Migratoria <span class="text-danger">*</span></label>
                            <select class="form-select @error('migrante') is-invalid @enderror" id="migrante" name="migrante" required>
                                <option value="0" {{ old('migrante', $adultoMayor->migrante) == '0' ? 'selected' : '' }}>No es migrante</option>
                                <option value="1" {{ old('migrante', $adultoMayor->migrante) == '1' ? 'selected' : '' }}>Es migrante</option>
                            </select>
                            @error('migrante')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        
                        <!-- Campo condicional para el origen del migrante -->
                        <div class="col-md-8 mb-3" id="origen_migracion_wrapper" style="display: none;">
                            <label for="origen_migracion" class="form-label">Lugar de Origen (Migración) <span class="text-danger" id="origen_migracion_asterisk" style="display: none;">*</span></label>
                            <input type="text" class="form-control @error('origen_migracion') is-invalid @enderror" id="origen_migracion" name="origen_migracion" value="{{ old('origen_migracion', $adultoMayor->origen_migracion) }}" placeholder="País, Departamento o Ciudad">
                            @error('origen_migracion')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nro_caso" class="form-label">Número de Caso (si aplica)</label>
                            <input type="text" class="form-control @error('nro_caso') is-invalid @enderror" id="nro_caso" name="nro_caso" value="{{ old('nro_caso', $adultoMayor->nro_caso) }}">
                            @error('nro_caso')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="fecha" class="form-label">Fecha de Registro (Adulto Mayor) <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('fecha') is-invalid @enderror" id="fecha" name="fecha" value="{{ old('fecha', \Carbon\Carbon::parse($adultoMayor->fecha_registro_am ?? $adultoMayor->fecha)->format('Y-m-d')) }}" required>
                            @error('fecha')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mt-4 text-end">
                        <a href="{{ route('gestionar-adultomayor.index') }}" class="btn btn-secondary me-2">
                            <i class="fe fe-x-circle me-1"></i>Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fe fe-save me-1"></i>Actualizar Adulto Mayor
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .card-header.bg-primary {
        background-color: #0d6efd !important;
    }
    .text-primary {
        color: #0d6efd !important;
    }
</style>
@endpush

{{-- ========================================================================= --}}
{{-- === INICIO: SCRIPT PARA CAMPO CONDICIONAL === --}}
{{-- ========================================================================= --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const migranteSelect = document.getElementById('migrante');
    const origenMigracionWrapper = document.getElementById('origen_migracion_wrapper');
    const origenMigracionInput = document.getElementById('origen_migracion');
    const origenMigracionAsterisk = document.getElementById('origen_migracion_asterisk');

    function toggleOrigenMigracion() {
        if (!migranteSelect || !origenMigracionWrapper || !origenMigracionInput || !origenMigracionAsterisk) {
            return;
        }

        if (migranteSelect.value === '1') { // '1' para "Es migrante"
            origenMigracionWrapper.style.display = 'block';
            origenMigracionInput.required = true;
            origenMigracionAsterisk.style.display = 'inline';
        } else {
            origenMigracionWrapper.style.display = 'none';
            origenMigracionInput.required = false;
            // En edición, no limpiamos el valor para no perder datos si el usuario se equivoca y revierte la selección.
        }
    }

    if (migranteSelect) {
        migranteSelect.addEventListener('change', toggleOrigenMigracion);
        toggleOrigenMigracion(); // Llamada inicial para establecer el estado correcto al cargar la página
    }
});
</script>
@endpush
{{-- ========================================================================= --}}
{{-- === FIN: SCRIPT PARA CAMPO CONDICIONAL === --}}
{{-- ========================================================================= --}}
