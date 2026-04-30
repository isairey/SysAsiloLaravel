@extends('layouts.main')

@section('title', 'Módulo Médico / Reportes de Historia Clínica')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/Medico/indexHistoriaClinica.css') }}"> 
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endpush

@section('content')

    <div class="page-header">
        <h1 class="page-title">Módulo Médico / Reportes de Historia Clínica</h1>
        <div>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
                <li class="breadcrumb-item active" aria-current="page">Reportes de Historia Clínica</li>
            </ol>
        </div>
    </div>

    <!-- Tarjetas de estadísticas -->
    <div class="row">
        <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
            <div class="card overflow-hidden sales-card bg-primary-gradient">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h6 class="card-text mb-0 text-white">Total Adultos Mayores</h6>
                            <h4 class="mb-0 num-text text-white">{{ $totalAdultos ?? 0 }}</h4>
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
            <div class="card overflow-hidden sales-card bg-warning-gradient">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h6 class="card-text mb-0 text-white">Total Historias Clínicas</h6>
                            <h4 class="mb-0 num-text text-white">{{ $totalHistoriasClinicas ?? 0 }}</h4>
                        </div>
                        <div class="col col-auto">
                            <div class="counter-icon bg-gradient-warning ms-auto box-shadow-warning">
                                <i class="fe fe-file-text text-white mb-5"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Botones para Navegación entre Reportes -->
    <div class="mb-3 d-flex justify-content-start gap-2">
        <a href="{{ route('responsable.enfermeria.reportes_enfermeria.index') }}" class="btn btn-secondary">
            <i class="fe fe-heart"></i> Reportes de Atención de Enfermería
        </a>
        <a href="{{ route('responsable.enfermeria.reportes_historia_clinica.index') }}" class="btn btn-primary">
            <i class="fe fe-file-text"></i> Reportes de Historia Clínica
        </a>
    </div>

    <!-- Tabla de reportes de Historia Clínica -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Listado de Historias Clínicas</h3>
                </div>
                <div class="card-body">
                    <form id="filterFormHis" action="{{ route('responsable.enfermeria.reportes_historia_clinica.index') }}" method="GET" class="form-filter mb-4">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-4">
                                <label for="search_his" class="form-label">Búsqueda General:</label>
                                <input type="text" class="form-control" id="search_his" name="search" placeholder="CI, nombre, motivo, diagnóstico..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-3">
                                <label for="mes" class="form-label">Mes de Registro:</label>
                                <select name="mes" id="mes" class="form-select">
                                    <option value="">-- Todos los meses --</option>
                                    @php
                                        $meses = [
                                            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
                                            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
                                            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
                                        ];
                                    @endphp
                                    @foreach ($meses as $num => $nombre)
                                        <option value="{{ $num }}" {{ request('mes') == $num ? 'selected' : '' }}>{{ $nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="anio" class="form-label">Año de Registro:</label>
                                <select name="anio" id="anio" class="form-select">
                                    <option value="">-- Todos los años --</option>
                                    @foreach ($years as $year)
                                        <option value="{{ $year }}" {{ request('anio') == $year ? 'selected' : '' }}>{{ $year }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2"><i class="fe fe-search"></i> Filtrar</button>
                                <a href="{{ route('responsable.enfermeria.reportes_historia_clinica.index') }}" class="btn btn-outline-secondary"><i class="fe fe-x"></i> Limpiar</a>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table id="historiasTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID Historia</th>
                                    <th>Fecha Registro</th>
                                    <th>Adulto Mayor</th>
                                    <th>CI Adulto</th>
                                    <th>Registrado Por</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($historiasClinicas as $historia)
                                    <tr>
                                        <td>{{ $historia->id_historia }}</td>
                                        <td>{{ optional($historia->created_at)->format('d/m/Y H:i') ?? 'N/A' }}</td>
                                        <td>
                                            <strong>{{ optional($historia->adulto->persona)->nombres }} {{ optional($historia->adulto->persona)->primer_apellido }}</strong>
                                            @if(optional($historia->adulto->persona)->segundo_apellido)
                                                {{ optional($historia->adulto->persona)->segundo_apellido }}
                                            @endif
                                        </td>
                                        <td>{{ optional($historia->adulto->persona)->ci ?? 'N/A' }}</td>
                                        <td>{{ optional($historia->usuario->persona)->nombres }} {{ optional($historia->usuario->persona)->primer_apellido }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('responsable.enfermeria.reportes_historia_clinica.exportar_excel', ['id_historia' => $historia->id_historia]) }}" class="btn btn-sm btn-success" data-bs-toggle="tooltip" title="Exportar a Excel">
                                                    <i class="fe fe-file-text"></i>
                                                </a>
                                                <form action="{{ route('responsable.enfermeria.medico.historia_clinica.destroy', ['id_historia' => $historia->id_historia]) }}" method="POST" class="delete-form d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="Eliminar">
                                                        <i class="fe fe-trash-2"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">
                                            <i class="fe fe-inbox" style="font-size: 48px;"></i>
                                            <br>
                                            No se encontraron historias clínicas con los filtros aplicados.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    @if(method_exists($historiasClinicas, 'links') && $historiasClinicas->hasPages())
                        <div class="d-flex justify-content-center mt-3">
                            {{ $historiasClinicas->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof feather !== 'undefined') {
            feather.replace();
        }

        if (typeof bootstrap !== 'undefined' && typeof bootstrap.Tooltip !== 'undefined') {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        }

        @if(session('success'))
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: '¡Éxito!',
                text: "{{ session('success') }}",
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: true
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Error',
                html: '<p>{{ session('error') }}</p>',
                confirmButtonText: 'Aceptar'
            });
        @endif

        document.querySelectorAll('.delete-form').forEach(form => {
            form.addEventListener('submit', function (event) {
                event.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: '¿Está seguro?',
                    text: '¿Desea eliminar esta Historia Clínica? Esta acción no se puede deshacer.',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar',
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    });
</script>
@endpush
