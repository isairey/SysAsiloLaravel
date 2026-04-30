@extends('layouts.main')

@section('content')
{{-- Incluir el archivo CSS --}}
<link rel="stylesheet" href="{{ asset('css/gestionarRolescss/editRoles.css') }}">


<div class="edit-role-container">
    <h1 class="edit-role-title">
        Editar Rol: <span class="role-name-highlight">{{ $rol->nombre_rol }}</span>
        @if(strtolower($rol->nombre_rol) === 'admin' || strtolower($rol->nombre_rol) === 'administrador')
            <span class="admin-badge">🔒 ADMIN</span>
        @endif
    </h1>

    @if ($errors->any())
        <div class="alert alert-error fade-in" role="alert">
            <p class="alert-title">Error de Validación</p>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    
    @if (session('error'))
        <div class="alert alert-error fade-in" role="alert">
            <p class="alert-title">Error</p>
            <p>{{ session('error') }}</p>
        </div>
    @endif

    <div class="form-card fade-in">
        {{-- CORRECCIÓN: Se pasa el objeto $rol completo. Laravel ahora sabe que 'rol' es el parámetro a usar. --}}
        <form action="{{ route('admin.gestionar-roles.update', $rol) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="nombre_rol" class="form-label">
                    Nombre del Rol <span class="required-asterisk">*</span>
                </label>
                <input type="text" 
                       name="nombre_rol" 
                       id="nombre_rol" 
                       value="{{ old('nombre_rol', $rol->nombre_rol) }}" 
                       required
                       class="form-input @error('nombre_rol') error @enderror"
                       placeholder="Ej: Editor">
                @error('nombre_rol')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="descripcion" class="form-label">Descripción</label>
                <textarea name="descripcion" 
                          id="descripcion" 
                          rows="3"
                          class="form-textarea @error('descripcion') error @enderror"
                          placeholder="Describe brevemente el propósito de este rol">{{ old('descripcion', $rol->descripcion) }}</textarea>
                @error('descripcion')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Estado</label>
                <div class="checkbox-container">
                    <div class="custom-checkbox">
                        <input type="checkbox" 
                               name="active" 
                               id="active" 
                               value="1" 
                               {{ old('active', $rol->active) ? 'checked' : '' }}
                               {{ (strtolower($rol->nombre_rol) === 'admin' || strtolower($rol->nombre_rol) === 'administrador') ? 'disabled' : '' }}>
                        <span class="checkbox-checkmark"></span>
                    </div>
                    <label for="active" class="checkbox-label">Activo</label>
                </div>
                
                @if(strtolower($rol->nombre_rol) === 'admin' || strtolower($rol->nombre_rol) === 'administrador')
                    <input type="hidden" name="active" value="1">
                    <div class="info-note">
                        <p>🔒 El rol de Administrador no puede ser desactivado por seguridad.</p>
                    </div>
                @endif
            </div>

            <div class="permissions-section">
                <h3 class="permissions-title">⚙️ Asignar Permisos</h3>
                
                @if($permissions->isEmpty())
                    <div class="permissions-empty">
                        <p>📝 No hay permisos disponibles para asignar.</p>
                    </div>
                @else
                    <div class="permissions-grid {{ (strtolower($rol->nombre_rol) === 'admin' || strtolower($rol->nombre_rol) === 'administrador') ? 'admin-protected' : '' }}">
                        @foreach ($permissions as $permission)
                            <div class="permission-item">
                                <div class="custom-checkbox permission-checkbox">
                                    <input type="checkbox" 
                                           name="permissions[]" 
                                           id="permission_{{ $permission->id }}" 
                                           value="{{ $permission->id }}"
                                           {{ (is_array(old('permissions')) && in_array($permission->id, old('permissions'))) || (empty(old('permissions')) && in_array($permission->id, $rolePermissions)) ? 'checked' : '' }}
                                           {{ (strtolower($rol->nombre_rol) === 'admin' || strtolower($rol->nombre_rol) === 'administrador') ? 'checked disabled' : '' }}>
                                    <span class="checkbox-checkmark"></span>
                                </div>
                                <label for="permission_{{ $permission->id }}" class="permission-label">
                                    <span class="permission-name">{{ $permission->name }}</span>
                                    <span class="permission-description">{{ $permission->description }}</span>
                                </label>
                            </div>
                        @endforeach
                    </div>
                    
                    @if(strtolower($rol->nombre_rol) === 'admin' || strtolower($rol->nombre_rol) === 'administrador')
                        @foreach ($permissions as $permission)
                            <input type="hidden" name="permissions[]" value="{{ $permission->id }}">
                        @endforeach
                        <div class="info-note">
                            <p>🔐 El rol de Administrador tiene todos los permisos asignados automáticamente y no se pueden modificar por seguridad del sistema.</p>
                        </div>
                    @endif
                @endif
                
                @error('permissions')
                    <p class="error-message">{{ $message }}</p>
                @enderror
                @error('permissions.*')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>

            <div class="button-group">
                <a href="{{ route('admin.gestionar-roles.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left btn-icon"></i>Cancelar
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-sync-alt btn-icon"></i>Actualizar Rol
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
