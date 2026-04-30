@extends('layouts.main')

@push('styles')
    <style>
        .nav-tabs .nav-link.active {
            background-color: #f0f2f7;
            border-color: #dee2e6 #dee2e6 #f0f2f7;
            font-weight: 600;
        }
        .btn-action {
            margin: 0 2px;
        }
        .input-group-text {
            background-color: #f8f9fa;
        }
    </style>
@endpush

@section('content')
<div class="page-header">
    <h1 class="page-title">Papelera de Reciclaje</h1>
    <div>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.gestionar-usuarios.index') }}">Gestionar Usuarios</a></li>
            <li class="breadcrumb-item active" aria-current="page">Papelera</li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header border-bottom-0">
                <h3 class="card-title">Registros Eliminados</h3>
                <div class="card-options">
                    <a href="{{ route('admin.gestionar-usuarios.index') }}" class="btn btn-primary btn-sm"><i class="fe fe-arrow-left me-2"></i>Volver a Usuarios</a>
                </div>
            </div>
            <div class="card-body">

                @if (session('success'))
                    <div class="alert alert-success" role="alert">
                        <i class="fe fe-check-circle me-2"></i>{{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger" role="alert">
                        <i class="fe fe-alert-triangle me-2"></i>{{ session('error') }}
                    </div>
                @endif

                {{-- ===== INICIO DE MEJORA: BUSCADOR ===== --}}
                <div class="row mb-4">
                    <div class="col-md-8 offset-md-2">
                        <form action="{{ route('admin.gestionar-usuarios.trash') }}" method="GET">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fe fe-search"></i></span>
                                <input type="text" name="search" class="form-control" placeholder="Buscar por CI en la papelera..." value="{{ request('search') }}">
                                <button class="btn btn-primary" type="submit">Buscar</button>
                                @if(request('search'))
                                    <a href="{{ route('admin.gestionar-usuarios.trash') }}" class="btn btn-secondary" data-bs-toggle="tooltip" title="Limpiar búsqueda">
                                        <i class="fe fe-x"></i>
                                    </a>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
                {{-- ===== FIN DE MEJORA: BUSCADOR ===== --}}


                <ul class="nav nav-tabs" id="trashTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="usuarios-tab" data-bs-toggle="tab" data-bs-target="#usuarios" type="button" role="tab" aria-controls="usuarios" aria-selected="true">
                            <i class="fe fe-users me-2"></i>Usuarios del Sistema ({{ $deletedUsers->count() }})
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="adultos-tab" data-bs-toggle="tab" data-bs-target="#adultos" type="button" role="tab" aria-controls="adultos" aria-selected="false">
                            <i class="fe fe-user-check me-2"></i>Adultos Mayores ({{ $deletedAdultosMayores->count() }})
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="trashTabContent">
                    {{-- Pestaña para Usuarios del Sistema --}}
                    <div class="tab-pane fade show active" id="usuarios" role="tabpanel" aria-labelledby="usuarios-tab">
                        <div class="table-responsive mt-3">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>CI</th>
                                        <th>Nombre Completo</th>
                                        <th>Rol</th>
                                        <th>Fecha de Eliminación</th>
                                        <th class="text-center">Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($deletedUsers as $user)
                                        <tr>
                                            <td>{{ $user->ci }}</td>
                                            <td>{{ optional($user->persona)->nombres ?? 'N/A' }} {{ optional($user->persona)->primer_apellido ?? '' }}</td>
                                            <td><span class="badge bg-secondary">{{ optional($user->rol)->nombre_rol ?? 'N/A' }}</span></td>
                                            <td>{{ $user->deleted_at->format('d/m/Y H:i:s') }}</td>
                                            <td class="text-center">
                                                <form action="{{ route('admin.gestionar-usuarios.restore', $user->ci) }}" method="POST" class="d-inline form-restore">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="btn btn-sm btn-success btn-action" data-bs-toggle="tooltip" title="Restaurar Usuario">
                                                        <i class="fe fe-rotate-ccw"></i> Restaurar
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="5" class="text-center text-muted p-4"><i>No se encontraron usuarios del sistema en la papelera.</i></td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Pestaña para Adultos Mayores --}}
                    <div class="tab-pane fade" id="adultos" role="tabpanel" aria-labelledby="adultos-tab">
                         <div class="table-responsive mt-3">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>CI</th>
                                        <th>Nombre Completo</th>
                                        <th>Fecha de Eliminación</th>
                                        <th class="text-center">Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($deletedAdultosMayores as $adulto)
                                        <tr>
                                            <td>{{ $adulto->ci }}</td>
                                            <td>{{ optional($adulto->persona)->nombres ?? 'N/A' }} {{ optional($adulto->persona)->primer_apellido ?? '' }}</td>
                                            <td>{{ $adulto->deleted_at->format('d/m/Y H:i:s') }}</td>
                                            <td class="text-center">
                                                <form action="{{ route('admin.gestionar-usuarios.restore', $adulto->ci) }}" method="POST" class="d-inline form-restore">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="btn btn-sm btn-success btn-action" data-bs-toggle="tooltip" title="Restaurar Adulto Mayor">
                                                        <i class="fe fe-rotate-ccw"></i> Restaurar
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="4" class="text-center text-muted p-4"><i>No se encontraron adultos mayores en la papelera.</i></td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Activar tooltips de Bootstrap
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Lógica para SweetAlert2 en los formularios de restauración
    document.querySelectorAll('.form-restore').forEach(form => {
        form.addEventListener('submit', function (event) {
            event.preventDefault(); // Prevenir envío automático
            
            Swal.fire({
                title: '¿Desea restaurar este registro?',
                text: "El registro volverá a estar activo en el sistema.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, ¡Restaurar!',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Si el usuario confirma, se envía el formulario
                    this.submit();
                }
            });
        });
    });
});
</script>
@endpush
