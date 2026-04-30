@extends('layouts.main')

@section('content')

    <!-- PAGE-HEADER -->
    <div class="page-header">
        <h1 class="page-title">Admin Dashboard</h1>
        <div>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Inicio</a></li>
                <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
            </ol>
        </div>
    </div>
    <!-- PAGE-HEADER END -->

    <!-- CONTENIDO PRINCIPAL DEL DASHBOARD -->
    
    {{-- Tarjetas de estadísticas --}}
    <div class="row">
        <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
            <div class="card overflow-hidden sales-card bg-primary-gradient">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h6 class="card-text mb-0 text-white">Total Usuarios</h6>
                            <h4 class="mb-0 num-text text-white">{{ $totalUsers ?? 0 }}</h4>
                        </div>
                        <div class="col col-auto">
                            <div class="counter-icon bg-gradient-primary ms-auto box-shadow-primary">
                                <i class="fe fe-users text-white mb-5"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
            <div class="card overflow-hidden sales-card bg-success-gradient">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h6 class="card-text mb-0 text-white">Usuarios Activos</h6>
                            <h4 class="mb-0 num-text text-white">{{ $activeUsers ?? 0 }}</h4>
                        </div>
                        <div class="col col-auto">
                            <div class="counter-icon bg-gradient-success ms-auto box-shadow-success">
                                <i class="fe fe-check-circle text-white mb-5"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
            <div class="card overflow-hidden sales-card bg-warning-gradient">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h6 class="card-text mb-0 text-white">Usuarios Inactivos</h6>
                            <h4 class="mb-0 num-text text-white">{{ $inactiveUsers ?? 0 }}</h4>
                        </div>
                        <div class="col col-auto">
                            <div class="counter-icon bg-gradient-warning ms-auto box-shadow-warning">
                                <i class="fe fe-user-x text-white mb-5"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- NUEVA TARJETA COMBINADA: FECHA Y HORA --}}
        <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
            <div class="card overflow-hidden sales-card bg-info-gradient">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h6 id="current-date" class="card-text mb-0 text-white">Cargando fecha...</h6>
                            <h4 id="live-clock" class="mb-0 num-text text-white">Cargando hora...</h4>
                        </div>
                        <div class="col col-auto">
                            <div class="counter-icon bg-gradient-info ms-auto box-shadow-info">
                                <i class="fe fe-calendar text-white mb-5"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabla de usuarios --}}
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Listado de Usuarios</h3>
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
                                @forelse($users as $user)
                                <tr>
                                    <td><strong>{{ $user->ci }}</strong></td>
                                    <td>{{ $user->username ?? $user->ci ?? 'N/A' }}</td>
                                    <td>
                                        @if(isset($user->persona))
                                            {{ $user->persona->nombres }} 
                                            {{ $user->persona->primer_apellido }}
                                            @if($user->persona->segundo_apellido)
                                                {{ $user->persona->segundo_apellido }}
                                            @endif
                                        @elseif(isset($user->nombres))
                                            {{ $user->nombres }} 
                                            {{ $user->primer_apellido }}
                                            @if($user->segundo_apellido)
                                                {{ $user->segundo_apellido }}
                                            @endif
                                        @elseif($user->name)
                                            {{ $user->name }}
                                        @else
                                            <span class="text-muted">Sin nombre</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if(isset($user->rol))
                                            <span class="badge bg-info">{{ $user->rol->nombre_rol }}</span>
                                        @elseif(isset($user->role_name))
                                            <span class="badge bg-info">{{ ucfirst($user->role_name) }}</span>
                                        @elseif($user->id_rol)
                                            <span class="badge bg-secondary">Rol ID: {{ $user->id_rol }}</span>
                                        @else
                                            <span class="badge bg-secondary">Sin rol</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if(isset($user->active))
                                            @if($user->active)
                                                <span class="badge bg-success">Activo</span>
                                            @else
                                                <span class="badge bg-danger">Inactivo</span>
                                            @endif
                                        @elseif(isset($user->activo))
                                            @if($user->activo)
                                                <span class="badge bg-success">Activo</span>
                                            @else
                                                <span class="badge bg-danger">Inactivo</span>
                                            @endif
                                        @else
                                            <span class="badge bg-warning">Desconocido</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($user->created_at)
                                            {{ $user->created_at->format('d/m/Y') }}
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            
                                            {{-- Botón Ver Detalles usando CI como identificador --}}
                                            <button 
                                                type="button" 
                                                class="btn btn-sm btn-info" 
                                                data-user-ci="{{ $user->ci }}" 
                                                onclick="viewUser(this.dataset.userCi)" 
                                                title="Ver detalles"
                                            >
                                                <i class="fe fe-eye"></i>
                                            </button>
                                            
                                            {{-- Botón Activar/Desactivar --}}
                                            @if(Route::has('admin.users.toggle_active'))
                                                <form action="{{ route('admin.users.toggle_active', $user->ci) }}" 
                                                      method="POST" 
                                                      style="display:inline"
                                                      onsubmit="return confirm('¿Está seguro de cambiar el estado de este usuario?')">
                                                    @csrf
                                                    <button type="submit"
                                                            class="btn btn-sm {{ ($user->active ?? false) ? 'btn-warning' : 'btn-success' }}"
                                                            title="{{ ($user->active ?? false) ? 'Desactivar usuario' : 'Activar usuario' }}">
                                                        <i class="fe {{ ($user->active ?? false) ? 'fe-user-x' : 'fe-user-check' }}"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            
                                            {{-- Botón Eliminar (opcional) --}}
                                            @if(Route::has('admin.users.destroy'))
                                                <form action="{{ route('admin.users.destroy', $user->ci) }}" 
                                                      method="POST" 
                                                      style="display:inline"
                                                      onsubmit="return confirm('¿Está seguro de eliminar este usuario? Esta acción no se puede deshacer.')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Eliminar usuario">
                                                        <i class="fe fe-trash-2"></i>
                                                    </button>
                                                </form>
                                            @endif
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
                    @if(method_exists($users, 'links'))
                        <div class="d-flex justify-content-center mt-3">
                            {{ $users->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <!-- FIN DEL CONTENIDO PRINCIPAL -->

    {{-- Modal para ver detalles del usuario --}}
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

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // --- INICIALIZACIÓN DE DATATABLE (SIN CAMBIOS) ---
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
            order: [[0, 'asc']], // Ordenar por CI ascendente por defecto
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

        // --- FUNCIONALIDAD: FECHA Y HORA COMBINADA ---

        function updateDateTime() {
            const now = new Date();
            
            // 1. Formato de Hora (HH:MM:SS)
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            $('#live-clock').text(`${hours}:${minutes}:${seconds}`);
            
            // 2. Formato de Fecha (ej: jueves, 26 de junio de 2025)
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            const dateString = now.toLocaleDateString('es-ES', options);
            // Capitalizar la primera letra del día
            const capitalizedDate = dateString.charAt(0).toUpperCase() + dateString.slice(1);
            $('#current-date').text(capitalizedDate);
        }
        
        // Actualizar cada segundo
        setInterval(updateDateTime, 1000);
        
        // Llamada inicial para que no haya retraso al cargar la página
        updateDateTime(); 
    });

    // --- FUNCIÓN PARA VER DETALLES (SIN CAMBIOS) ---
    function viewUser(userCi) {
        const modal = new bootstrap.Modal(document.getElementById('userDetailsModal'));
        const content = document.getElementById('userDetailsContent');
        
        // Mostrar spinner de carga
        content.innerHTML = `
            <div class="text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
            </div>
        `;
        
        modal.show();
        
        // Simulación de carga. Reemplazar con una llamada AJAX real.
        // fetch(`/admin/users/${userCi}/details`)
        //     .then(response => response.json())
        //     .then(data => {
        //         content.innerHTML = `... HTML con los detalles ...`;
        //     })
        //     .catch(error => {
        //         content.innerHTML = '<div class="alert alert-danger">Error al cargar los detalles del usuario.</div>';
        //     });
        
        // Por ahora, mostrar información básica de ejemplo
        setTimeout(() => {
            content.innerHTML = `
                <div class="alert alert-info">
                    <i class="fe fe-info"></i>
                    Detalles del usuario con CI: <strong>${userCi}</strong>
                    <br><small>Esta funcionalidad puede ser implementada con AJAX para mostrar información detallada del usuario desde la base de datos.</small>
                </div>
            `;
        }, 1000); // Simula un retraso de red
    }

</script>
@endpush
