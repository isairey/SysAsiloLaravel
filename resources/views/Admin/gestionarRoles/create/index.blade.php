@extends('layouts.main')

@section('styles')
    {{-- Se puede añadir CSS específico si es necesario, pero la estructura ahora usa Bootstrap --}}
    <link href="{{ asset('css/gestionarRolescss/createRoles.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="page">
    <div class="page-main">
        <div class="main-content app-content mt-0">
            <div class="side-app">
                <div class="main-container container-fluid">

                    <!-- Cabecera de la Página -->
                    <div class="page-header">
                        <h1 class="page-title">Crear Nuevo Rol</h1>
                        <div>
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('admin.gestionar-roles.index') }}">Gestionar Roles</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Crear Rol</li>
                            </ol>
                        </div>
                    </div>

                    <!-- Contenido Principal: Formulario de Creación -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Detalles del Rol</h3>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('admin.gestionar-roles.store') }}" method="POST">
                                        @csrf

                                        {{-- Fila para Nombre del Rol y Estado --}}
                                        <div class="row">
                                            <div class="col-md-9">
                                                <div class="mb-3">
                                                    <label for="nombre_rol" class="form-label">Nombre del Rol <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control @error('nombre_rol') is-invalid @enderror" id="nombre_rol" name="nombre_rol" value="{{ old('nombre_rol') }}" required placeholder="Ej: Editor de Contenido">
                                                    @error('nombre_rol')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="mb-3">
                                                    <label for="active" class="form-label">Estado</label>
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox" name="active" id="active" value="1" {{ old('active', true) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="active">Activo</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        {{-- Campo de Descripción --}}
                                        <div class="mb-4">
                                            <label for="descripcion" class="form-label">Descripción</label>
                                            <textarea class="form-control @error('descripcion') is-invalid @enderror" id="descripcion" name="descripcion" rows="3" placeholder="Describe brevemente el propósito de este rol">{{ old('descripcion') }}</textarea>
                                            @error('descripcion')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <hr>

                                        {{-- Sección de Permisos --}}
                                        <div class="mb-4">
                                            <h3 class="card-title mb-3">Asignar Permisos</h3>
                                            @if($permissions->isEmpty())
                                                <p class="text-muted">No hay permisos disponibles para asignar.</p>
                                            @else
                                                <div class="row">
                                                    @foreach ($permissions as $permission)
                                                        <div class="col-md-4 col-lg-3">
                                                            <div class="form-check mb-2">
                                                                <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $permission->id }}" id="permission_{{ $permission->id }}"
                                                                    {{ (is_array(old('permissions')) && in_array($permission->id, old('permissions'))) ? 'checked' : '' }}>
                                                                <label class="form-check-label" for="permission_{{ $permission->id }}">
                                                                    <span class="fw-bold">{{ $permission->name }}</span>
                                                                    <p class="text-muted small mb-0">{{ $permission->description }}</p>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                            @error('permissions')
                                                <div class="text-danger mt-2 small">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        {{-- Botones de Acción --}}
                                        <div class="card-footer text-end">
                                            <a href="{{ route('admin.gestionar-roles.index') }}" class="btn btn-secondary">Cancelar</a>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fe fe-save me-1"></i>Guardar Rol
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- Scripts adicionales si son necesarios --}}
@endpush