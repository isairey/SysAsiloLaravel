@extends('layouts.main')

{{-- Define el título de la página --}}
@section('title', 'Gestionar Roles')

{{-- Estilos específicos de la vista --}}
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}"> {{-- Si este es un estilo global, puedes moverlo a layouts.main --}}
    <link rel="stylesheet" href="{{ asset('css/gestionarRoles.css') }}">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs5/dt-1.11.5/datatables.min.css"/>
    {{-- Cargamos SweetAlert2 CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endpush

@section('content')
                    <!-- Cabecera de la Página -->
                    <div class="page-header">
                        <h1 class="page-title">Gestionar Roles</h1>
                        <div>
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Gestionar Roles</li>
                            </ol>
                        </div>
                    </div>

                    {{-- Las Alertas de Sesión se manejarán con SweetAlert2 (mediante JavaScript en @push('scripts')) --}}

                    <!-- Contenido Principal: Tabla de Roles -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">

                                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                                    <h3 class="card-title text-white mb-0">Listado de Roles del Sistema</h3>

                                    @can('roles.create')
                                        {{-- Ruta: admin.gestionar-roles.create --}}
                                        <a href="{{ route('admin.gestionar-roles.create') }}" class="btn btn-light btn-sm">
                                            <i class="fe fe-plus-circle me-1"></i>Agregar Rol
                                        </a>
                                    @endcan

                                </div>

                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table id="rolesTable" class="table table-bordered table-striped w-100">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Nombre del Rol</th>
                                                    <th>Descripción</th>
                                                    <th class="text-center">Permisos</th>
                                                    <th class="text-center">Usuarios</th>
                                                    <th class="text-center">Estado</th>
                                                    <th class="text-center">Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($roles as $rol)
                                                    <tr>
                                                        <td><strong>{{ $rol->id_rol }}</strong></td>
                                                        <td>{{ $rol->nombre_rol }}</td>
                                                        <td>
                                                            <span title="{{ $rol->descripcion }}">{{ Str::limit($rol->descripcion, 50) }}</span>
                                                        </td>
                                                        <td class="text-center">
                                                            <span class="badge bg-info">{{ $rol->permissions_count ?? $rol->permissions->count() }}</span>
                                                        </td>
                                                        <td class="text-center">
                                                            <span class="badge bg-secondary">{{ $rol->users_count ?? $rol->users->count() }}</span>
                                                        </td>
                                                        <td class="text-center">
                                                            @if ($rol->active)
                                                                <span class="badge bg-success">Activo</span>
                                                            @else
                                                                <span class="badge bg-danger">Inactivo</span>
                                                            @endif
                                                        </td>
                                                        <td class="text-center">
                                                            <div class="btn-group" role="group">
                                                                @can('roles.edit')
                                                                    {{-- Ruta: admin.gestionar-roles.edit --}}
                                                                    <a href="{{ route('admin.gestionar-roles.edit', $rol->id_rol) }}" class="btn btn-sm btn-info" data-bs-toggle="tooltip" title="Editar Rol">
                                                                        <i class="fe fe-edit"></i>
                                                                    </a>
                                                                @endcan
                                                                @can('roles.destroy')
                                                                    @if (strtolower($rol->nombre_rol) !== 'admin')
                                                                        {{-- Formulario para eliminar rol con SweetAlert2 --}}
                                                                        {{-- Ruta: admin.gestionar-roles.destroy --}}
                                                                        <form action="{{ route('admin.gestionar-roles.destroy', $rol->id_rol) }}" method="POST" class="d-inline form-delete-role">
                                                                            @csrf
                                                                            @method('DELETE')
                                                                            <button type="submit" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="Eliminar Rol">
                                                                                <i class="fe fe-trash-2"></i>
                                                                            </button>
                                                                        </form>
                                                                    @else
                                                                        <button type="button" class="btn btn-sm btn-secondary" data-bs-toggle="tooltip" title="El rol de Administrador no puede ser eliminado" disabled>
                                                                            <i class="fe fe-trash-2"></i>
                                                                        </button>
                                                                    @endif
                                                                @endcan
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="7" class="text-center text-muted">
                                                            <i class="fe fe-inbox fs-3"></i><br>
                                                            No hay roles registrados en el sistema.
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

@endsection

{{-- Scripts específicos de la vista --}}
@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> {{-- Asegúrate que jQuery no se duplique si ya está en layouts.main --}}
<script type="text/javascript" src="https://cdn.datatables.net/v/bs5/dt-1.11.5/datatables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
{{-- Cargamos SweetAlert2 JS --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
        
        // Inicializar DataTables
        // Verifica si DataTable existe antes de inicializar para evitar errores si no se carga.
        // Asumo que 'assets/translates/Spanish.json' es una ruta válida para el archivo de idioma.
        if ($.fn.DataTable) { // Usamos $.fn.DataTable para comprobar la existencia de la función de jQuery DataTables
            $('#rolesTable').DataTable({
                language: {
                    url: '{{ asset('assets/translates/Spanish.json') }}'
                },
                responsive: true,
                order: [[0, 'asc']],
                dom: 'lfrtip',
                columnDefs: [
                    { targets: [3, 4, 5, 6], orderable: false, searchable: false } // Columnas de permisos, usuarios, estado y acciones
                ]
            });
        }

        // Inicializar Tooltips de Bootstrap
        // Asegúrate que bootstrap.bundle.min.js o bootstrap.min.js esté cargado en layouts.main
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });

        // --- Manejo de Alertas de Sesión con SweetAlert2 ---
        @if (session('success'))
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: '¡Éxito!',
                text: '{{ session('success') }}',
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: true
            });
        @endif
        
        @if (session('error'))
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'error',
                title: '¡Error!',
                text: '{{ session('error') }}',
                showConfirmButton: false, // Puedes cambiar a true si quieres que el usuario cierre el error manualmente
                timer: 5000,
                timerProgressBar: true
            });
        @endif

        // --- Confirmación para eliminar Rol con SweetAlert2 ---
        document.querySelectorAll('.form-delete-role').forEach(form => {
            form.addEventListener('submit', function(event) {
                event.preventDefault(); // Evitar el envío normal del formulario
                Swal.fire({
                    title: '¿Está seguro de eliminar este rol?',
                    text: "¡Esta acción no se puede deshacer! Se recomienda reasignar usuarios antes de eliminar un rol.",
                    icon: 'warning', // Usar 'warning' para eliminaciones
                    showCancelButton: true,
                    confirmButtonColor: '#d33', // Rojo para confirmar eliminación
                    cancelButtonColor: '#6c757d', // Gris para cancelar
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit(); // Si el usuario confirma, enviar el formulario
                    }
                });
            });
        });

    });
</script>
@endpush