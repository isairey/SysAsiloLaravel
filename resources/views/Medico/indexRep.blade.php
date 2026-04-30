@extends('layouts.main')

{{-- Define el título de la página --}}
@section('title', 'Módulo Médico / Reportes de Atención de Enfermería')

{{-- Estilos específicos de la vista --}}
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/Medico/indexHistoriaClinica.css') }}"> 
    {{-- SweetAlert2 CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endpush

@section('content')

    <div class="page-header">
        <h1 class="page-title">Módulo Médico / Reportes de Atención de Enfermería</h1>
        <div>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
                <li class="breadcrumb-item active" aria-current="page">Reportes de Enfermería</li>
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
            <div class="card overflow-hidden sales-card bg-info-gradient">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h6 class="card-text mb-0 text-white">Total Atenciones Enfermería</h6>
                            <h4 class="mb-0 num-text text-white">{{ $totalFichasEnfermeria ?? 0 }}</h4>
                        </div>
                        <div class="col col-auto">
                            <div class="counter-icon bg-gradient-info ms-auto box-shadow-info">
                                <i class="fe fe-heart text-white mb-5"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de reportes de Atención de Enfermería -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Listado de Fichas de Atención de Enfermería</h3>
                </div>
                <div class="card-body">
                    {{-- Formulario de Filtros y Búsqueda --}}
                    <form id="filterForm" action="{{ route('responsable.enfermeria.reportes_enfermeria.index') }}" method="GET" class="form-filter mb-4">
                        <div class="row g-3 align-items-end">
                            
                            <div class="col-md-4">
                                <label for="search" class="form-label">Búsqueda General:</label>
                                <input type="text" class="form-control" id="search" name="search" placeholder="CI, nombre, derivación..." value="{{ request('search') }}">
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
                                <a href="{{ route('responsable.enfermeria.reportes_enfermeria.index') }}" class="btn btn-outline-secondary"><i class="fe fe-x"></i> Limpiar</a>
                            </div>
                        </div>
                    </form>

                    {{-- Botones de Navegación y Exportación --}}
                    <div class="mb-3 d-flex justify-content-between align-items-center">
                        <a href="{{ route('responsable.enfermeria.reportes_historia_clinica.index') }}" class="btn btn-secondary">
                            <i class="fe fe-file-text"></i> Reportes de Historia Clínica
                        </a>
                        <button type="button" class="btn btn-success btn-sm" id="exportarExcelBtn">
                            <i class="fe fe-download"></i> Exportar a Excel
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table id="reportesTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID Atención</th>
                                    <th>Fecha Registro</th>
                                    <th>Adulto Mayor</th>
                                    <th>CI Adulto</th>
                                    <th>Presión Arterial</th>
                                    <th>Temperatura</th>
                                    <th>Derivación (Extracto)</th>
                                    <th>Registrado Por</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($reportes as $reporte)
                                    <tr>
                                        <td>{{ $reporte->cod_enf }}</td>
                                        <td>{{ optional($reporte->created_at)->format('d/m/Y H:i') ?? 'N/A' }}</td>
                                        <td>
                                            <strong>{{ optional($reporte->adulto->persona)->nombres }} {{ optional($reporte->adulto->persona)->primer_apellido }}</strong>
                                            @if(optional($reporte->adulto->persona)->segundo_apellido)
                                                {{ optional($reporte->adulto->persona)->segundo_apellido }}
                                            @endif
                                        </td>
                                        <td>{{ optional($reporte->adulto->persona)->ci ?? 'N/A' }}</td>
                                        <td>{{ $reporte->presion_arterial ?? 'N/A' }}</td>
                                        <td>{{ $reporte->temperatura ?? 'N/A' }}</td>
                                        <td>{{ $reporte->derivacion ? Str::limit($reporte->derivacion, 50, '...') : 'N/A' }}</td>
                                        <td>{{ optional($reporte->usuario->persona)->nombres }} {{ optional($reporte->usuario->persona)->primer_apellido }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <form action="{{ route('responsable.enfermeria.reportes_enfermeria.destroy_atencion_enfermeria', ['cod_enf' => $reporte->cod_enf]) }}" method="POST" style="display:inline-block;" class="form-delete-report">
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
                                        <td colspan="9" class="text-center text-muted">
                                            <i class="fe fe-inbox" style="font-size: 48px;"></i>
                                            <br>
                                            No se encontraron fichas que coincidan con los filtros aplicados.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    @if(method_exists($reportes, 'links') && $reportes->hasPages())
                        <div class="d-flex justify-content-center mt-3">
                            {{ $reportes->appends(request()->query())->links() }}
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
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
                var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl)
                });
            }

            @if(session('success'))
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: '¡Éxito!',
                    text: '{{ session('success') }}',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
            @endif

            @if(session('error'))
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

            document.getElementById('exportarExcelBtn').addEventListener('click', function() {
                const form = document.getElementById('filterForm');
                const queryString = new URLSearchParams(new FormData(form)).toString();
                window.location.href = `{{ route('responsable.enfermeria.reportes_enfermeria.exportar_excel') }}?${queryString}`;
            });

            document.querySelectorAll('.form-delete-report').forEach(form => {
                form.addEventListener('submit', function(event) {
                    event.preventDefault();
                    const currentForm = this;

                    Swal.fire({
                        title: '¿Está seguro?',
                        text: "¡Esta acción no se puede deshacer!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            currentForm.submit();
                        }
                    });
                });
            });
        });
    </script>
@endpush
