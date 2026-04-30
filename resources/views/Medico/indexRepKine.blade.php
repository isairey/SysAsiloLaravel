@extends('layouts.main')

{{-- Define el título de la página --}}
@section('title', 'Módulo Médico / Reporte de Fichas de Kinesiología')

{{-- Estilos específicos de la vista --}}
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/Medico/indexFisioKine.css') }}"> 
    {{-- SweetAlert2 CSS para alertas y confirmaciones --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endpush

@section('content')
    <div class="page-header">
        <h1 class="page-title">Módulo Médico / Reporte de Fichas de Kinesiología</h1>
        <div>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
                <li class="breadcrumb-item active" aria-current="page">Reporte Kinesiología</li>
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
                            <h6 class="card-text mb-0 text-white">Total Fichas Kinesiología</h6>
                            <h4 class="mb-0 num-text text-white">{{ $totalFichasKinesiologia ?? 0 }}</h4>
                        </div>
                        <div class="col col-auto">
                            <div class="counter-icon bg-gradient-primary ms-auto box-shadow-primary">
                                <i class="fe fe-file-text text-white mb-5"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Listado de Fichas de Kinesiología Registradas -->
    <div class="row mt-2">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Fichas de Kinesiología Registradas</h3>
                </div>
                <div class="card-body">
                    {{-- Formulario de Filtros y Búsqueda --}}
                    <form id="filterFormFichasKine" action="{{ route('responsable.kinesiologia.reportekine.index') }}" method="GET" class="form-filter mb-4">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-4">
                                <label for="search_fichas_kine" class="form-label">Buscar por Adulto Mayor:</label>
                                <input type="text" class="form-control" id="search_fichas_kine" name="search" placeholder="CI, nombre..." value="{{ request('search') }}">
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
                                <a href="{{ route('responsable.kinesiologia.reportekine.index') }}" class="btn btn-outline-secondary"><i class="fe fe-x"></i> Limpiar</a>
                            </div>
                        </div>
                    </form>
                    
                    <!-- Botones de Navegación y Exportación -->
                    <div class="mb-3 d-flex justify-content-between align-items-center">
                        <a href="{{ route('responsable.fisioterapia.reportefisio.index') }}" class="btn btn-info">
                            <i class="fe fe-list"></i> Ver Reporte de Fichas Fisioterapia
                        </a>
                        <button type="button" class="btn btn-success btn-sm" id="exportarExcelGeneralBtn">
                            <i class="fe fe-download"></i> Exportar a Excel
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Cod. Kine</th>
                                    <th>Adulto Mayor</th>
                                    <th>CI</th>
                                    <th>Fecha Registro</th>
                                    <th>Servicios</th>
                                    <th>Turnos</th>
                                    <th>Registrado Por</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($fichasKinesiologia as $fichaKine)
                                    <tr>
                                        <td>{{ $fichaKine->cod_kine }}</td>
                                        <td>
                                            <strong>{{ optional(optional($fichaKine->adulto)->persona)->nombres }}</strong>
                                            {{ optional(optional($fichaKine->adulto)->persona)->primer_apellido }}
                                            {{ optional(optional($fichaKine->adulto)->persona)->segundo_apellido }}
                                        </td>
                                        <td>{{ optional(optional($fichaKine->adulto)->persona)->ci }}</td>
                                        <td>{{ optional($fichaKine->created_at)->format('d/m/Y H:i') ?? 'N/A' }}</td>
                                        <td>
                                            @php
                                                $services = [];
                                                if($fichaKine->entrenamiento_funcional) $services[] = 'EF';
                                                if($fichaKine->gimnasio_maquina) $services[] = 'GM';
                                                if($fichaKine->aquafit) $services[] = 'AQ';
                                                if($fichaKine->hidroterapia) $services[] = 'HT';
                                                echo empty($services) ? 'N/A' : implode(', ', $services);
                                            @endphp
                                        </td>
                                        <td>
                                            @php
                                                $turns = [];
                                                if($fichaKine->manana) $turns[] = 'Mañana';
                                                if($fichaKine->tarde) $turns[] = 'Tarde';
                                                echo empty($turns) ? 'N/A' : implode(', ', $turns);
                                            @endphp
                                        </td>
                                        <td>{{ optional(optional($fichaKine->usuario)->persona)->nombres }} {{ optional(optional($fichaKine->usuario)->persona)->primer_apellido }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <form action="{{ route('responsable.kinesiologia.reportekine.destroy', ['cod_kine' => $fichaKine->cod_kine]) }}" method="POST" class="d-inline form-delete-kine-report">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="Eliminar Ficha">
                                                        <i class="fe fe-trash-2"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted">
                                            <i class="fe fe-inbox" style="font-size: 48px;"></i>
                                            <br>
                                            No se encontraron fichas que coincidan con los filtros aplicados.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        
                        @if(method_exists($fichasKinesiologia, 'links') && $fichasKinesiologia->hasPages())
                            <div class="d-flex justify-content-center mt-3">
                                {{ $fichasKinesiologia->appends(request()->query())->links() }}
                            </div>
                        @endif
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
            if (typeof feather !== 'undefined') {
                feather.replace();
            }

            if (typeof bootstrap !== 'undefined' && typeof bootstrap.Tooltip !== 'undefined') {
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
                var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl)
                })
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

            document.getElementById('exportarExcelGeneralBtn').addEventListener('click', function() {
                const form = document.getElementById('filterFormFichasKine');
                const queryString = new URLSearchParams(new FormData(form)).toString();
                window.location.href = `{{ route('responsable.kinesiologia.reportekine.exportExcel') }}?${queryString}`;
            });

            document.querySelectorAll('.form-delete-kine-report').forEach(form => {
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
