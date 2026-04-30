{{-- resources/views/Admin/gestionarUsuarios.blade.php --}}
@extends('layouts.main')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/gestionarUsuarios.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    {{-- Cargamos DataTables --}}
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.11.5/datatables.min.css"/>
    {{-- Cargamos SweetAlert2 --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endpush

@section('content')
                      <div class="page-header">
                            <h1 class="page-title">Gestionar Usuarios</h1>
                            <div>
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Gestionar Usuarios</li>
                                </ol>
                            </div>
                      </div>

                      {{-- Los mensajes de sesión se manejarán con SweetAlert2 en el script --}}

                      <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header bg-primary text-white">
                                        <h3 class="card-title text-white">Listado de Usuarios del Sistema</h3>
                                        <div class="card-options">
                                            <a href="{{ route('admin.registrar-responsable-salud') }}" class="btn btn-white btn-sm">
                                                <i data-feather="plus-circle"></i> Registrar Nuevo Responsable
                                            </a>
                                             {{-- ===== AÑADIR ESTE BOTÓN ===== --}}
                                                <a href="{{ route('admin.gestionar-usuarios.trash') }}" class="btn btn-outline-warning btn-sm ms-2" data-bs-toggle="tooltip" title="Ver registros eliminados">
                                                    <i class="fe fe-trash-2"></i> Papelera
                                                </a>
                                             {{-- ============================== --}}
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table id="usersTable" class="table table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>CI (ID Usuario)</th>
                                                        <th>CI/Usuario</th>
                                                        <th>Nombre Completo</th>
                                                        <th>Rol</th>
                                                        <th>Estado</th>
                                                        <th>Fecha Registro</th>
                                                        <th>Acciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse ($users as $usuario)
                                                        <tr>
                                                            <td><strong>{{ $usuario->ci }}</strong></td>
                                                            <td>{{ $usuario->username ?? $usuario->ci ?? 'N/A' }}</td>
                                                            <td>
                                                                {{ $usuario->persona->nombres ?? 'N/A' }} 
                                                                {{ $usuario->persona->primer_apellido ?? '' }} 
                                                                {{ $usuario->persona->segundo_apellido ?? '' }}
                                                            </td>
                                                            <td>
                                                                <span class="badge bg-info">{{ $usuario->rol->nombre_rol ?? 'Sin rol asignado' }}</span>
                                                            </td>
                                                            <td>
                                                                @if ($usuario->active)
                                                                    <span class="badge bg-success">Activo</span>
                                                                @else
                                                                    <span class="badge bg-danger">Inactivo</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if($usuario->created_at)
                                                                    {{ $usuario->created_at->format('d/m/Y') }}
                                                                @else
                                                                    <span class="text-muted">N/A</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <div class="btn-group" role="group">
                                                                    {{-- Botón para editar usuario --}}
                                                                    <a href="{{ route('admin.gestionar-usuarios.edit', $usuario->id_usuario) }}" 
                                                                       class="btn btn-sm btn-info" 
                                                                       data-bs-toggle="tooltip" 
                                                                       title="Editar">
                                                                        <i class="fe fe-edit"></i>
                                                                    </a>

                                                                    {{-- Formulario para activar/desactivar usuario --}}
                                                                    <form action="{{ route('admin.gestionar-usuarios.toggleActivity', $usuario->id_usuario) }}" 
                                                                          method="POST" 
                                                                          class="d-inline form-toggle-activity">
                                                                        @csrf
                                                                        @method('PATCH')
                                                                        <button type="submit"
                                                                                class="btn btn-sm {{ $usuario->active ? 'btn-warning' : 'btn-success' }}"
                                                                                data-bs-toggle="tooltip" 
                                                                                title="{{ $usuario->active ? 'Desactivar Usuario' : 'Activar Usuario' }}">
                                                                            <i class="fe {{ $usuario->active ? 'fe-user-x' : 'fe-user-check' }}"></i>
                                                                        </button>
                                                                    </form>

                                                                    {{-- Formulario para eliminar usuario --}}
                                                                    <form action="{{ route('admin.gestionar-usuarios.destroy', $usuario->id_usuario) }}" 
                                                                          method="POST" 
                                                                          class="d-inline form-delete-user">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit" 
                                                                                class="btn btn-sm btn-danger" 
                                                                                data-bs-toggle="tooltip" 
                                                                                title="Eliminar Usuario">
                                                                            <i class="fe fe-trash-2"></i>
                                                                        </button>
                                                                    </form>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="7" class="text-center text-muted">
                                                                <i class="fe fe-inbox"></i>
                                                                <br>
                                                                No hay usuarios registrados
                                                            </td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                        
                                        {{-- Paginación si está disponible --}}
                                        @if(method_exists($users, 'links') && $users->hasPages())
                                            <div class="d-flex justify-content-center mt-3">
                                                {{ $users->links() }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                      </div>
   
{{-- Modal de Detalles (se mantiene sin cambios) --}}
<div class="modal fade" id="userDetailsModal" tabindex="-1" aria-labelledby="userDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userDetailsModalLabel">Detalles del Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="userDetailsContent">
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

{{-- El modal de confirmación personalizado ya no es necesario --}}

@endsection



@push('scripts')
<script src="https://cdn.datatables.net/v/dt/dt-1.11.5/datatables.min.js"></script>
{{-- Cargamos SweetAlert2 --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar Feather Icons
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
        
        // Inicialización de DataTables
        if (typeof $().DataTable === 'function') {
            $('#usersTable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json'
                },
                responsive: true,
                paging: true,
                searching: true,
                ordering: true,
                info: true,
                pageLength: 25,
                order: [[0, 'asc']], // Ordenar por CI ascendente
                columnDefs: [
                    {
                        targets: [6], // Columna de acciones
                        orderable: false,
                        searchable: false
                    }
                ],
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: 'excel',
                        text: '<i class="fe fe-download"></i> Excel',
                        className: 'btn btn-success btn-sm'
                    },
                    {
                        extend: 'pdf',
                        text: '<i class="fe fe-file-text"></i> PDF',
                        className: 'btn btn-danger btn-sm'
                    },
                    {
                        extend: 'print',
                        text: '<i class="fe fe-printer"></i> Imprimir',
                        className: 'btn btn-info btn-sm'
                    }
                ]
            });
        }

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
                showConfirmButton: false,
                timer: 5000,
                timerProgressBar: true
            });
        @endif

        // --- Confirmación para cambiar estado del usuario ---
        document.querySelectorAll('.form-toggle-activity').forEach(form => {
            form.addEventListener('submit', function(event) {
                event.preventDefault();
                Swal.fire({
                    title: '¿Está seguro?',
                    text: "Se cambiará el estado de este usuario.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, cambiar estado',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit();
                    }
                });
            });
        });

        // --- Confirmación para eliminar usuario ---
        document.querySelectorAll('.form-delete-user').forEach(form => {
            form.addEventListener('submit', function(event) {
                event.preventDefault();
                Swal.fire({
                    title: '¿Está seguro de eliminar este usuario?',
                    text: "¡Esta acción no se puede deshacer!",
                    icon: 'error',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit();
                    }
                });
            });
        });
    });

    // --- Función para ver detalles del usuario (sin cambios) ---
    function viewUser(userId) {
        const modalElement = document.getElementById('userDetailsModal');
        const modal = new bootstrap.Modal(modalElement);
        const content = document.getElementById('userDetailsContent');
        
        content.innerHTML = `
            <div class="text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
            </div>
        `;
        
        modal.show();
        
        // Aquí iría la lógica AJAX para obtener los detalles
        // Por ahora, mostramos un placeholder
        content.innerHTML = `
            <div class="alert alert-info">
                <i data-feather="info"></i>
                Detalles del usuario con ID: ${userId}
                <br><small>Esta funcionalidad puede ser implementada con AJAX para mostrar información detallada.</small>
            </div>
        `;
        
        if (typeof feather !== 'undefined') {
            feather.replace({ parent: content });
        }
    }
</script>
@endpush