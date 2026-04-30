{{-- resources/views/Medico/indexRepFisio.blade.php --}}
@extends('layouts.main')

@section('content')

<div class="page-header">
    <h1 class="page-title">Módulo Médico / Reporte de Fichas de Fisioterapia</h1>
    <div>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
            <li class="breadcrumb-item active" aria-current="page">Reporte Fisioterapia</li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
        <div class="card overflow-hidden sales-card bg-success-gradient">
            <div class="card-body">
                <div class="row">
                    <div class="col">
                        <h6 class="card-text mb-0 text-white">Total Fichas Fisioterapia</h6>
                        <h4 class="mb-0 num-text text-white">{{ $totalFichasFisioterapia ?? 0 }}</h4>
                    </div>
                    <div class="col col-auto">
                        <div class="counter-icon bg-gradient-success ms-auto box-shadow-success">
                            <i class="fe fe-file-text text-white mb-5"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-2">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Fichas de Fisioterapia Registradas</h3>
            </div>
            <div class="card-body">
                {{-- Formulario de Filtros y Búsqueda --}}
                <form id="filterFormFichasFisio" action="{{ route('responsable.fisioterapia.reportefisio.index') }}" method="GET" class="form-filter mb-4">
                    <div class="row g-3">
                        {{-- Columna para Búsqueda y Filtros de Fecha --}}
                        <div class="col-md-9">
                            <div class="row g-3 align-items-end">
                                <div class="col-md-5">
                                    <label for="search_fichas_fisio" class="form-label">Buscar por Adulto Mayor o Motivo:</label>
                                    <input type="text" class="form-control" id="search_fichas_fisio" name="search" placeholder="CI, nombre, motivo..." value="{{ request('search') }}">
                                </div>
                                <div class="col-md-4">
                                    <label for="mes" class="form-label">Mes:</label>
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
                                <div class="col-md-3">
                                    <label for="anio" class="form-label">Año:</label>
                                    <select name="anio" id="anio" class="form-select">
                                        <option value="">-- Todos los años --</option>
                                        @foreach ($years as $year)
                                            <option value="{{ $year }}" {{ request('anio') == $year ? 'selected' : '' }}>{{ $year }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        {{-- Columna para Botones de Acción --}}
                        <div class="col-md-3 d-flex align-items-end justify-content-start gap-2">
                            <button type="submit" class="btn btn-primary"><i class="fe fe-search"></i> Filtrar</button>
                            <a href="{{ route('responsable.fisioterapia.reportefisio.index') }}" class="btn btn-outline-secondary"><i class="fe fe-x"></i> Limpiar</a>
                        </div>
                    </div>
                </form>

                {{-- Botón para ver reporte de Kinesiología y Exportaciones --}}
                <div class="mb-3 d-flex justify-content-start gap-2">
                    <a href="{{ route('responsable.kinesiologia.reportekine.index') }}" class="btn btn-info">
                        <i class="fe fe-list"></i> Ver Reporte de Fichas Kinesiología
                    </a>
                    {{-- Aquí puedes agregar botones de exportación general si los necesitas --}}
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Cod. Fisio</th>
                                <th>Adulto Mayor</th>
                                <th>CI</th>
                                <th>Fecha Programación</th>
                                <th>Motivo Consulta</th>
                                <th>Registrado Por</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($fichasFisioterapia as $fichaFisio)
                                <tr>
                                    <td>{{ $fichaFisio->cod_fisio }}</td>
                                    <td>
                                        <strong>{{ optional(optional($fichaFisio->adulto)->persona)->nombres }}</strong>
                                        {{ optional(optional($fichaFisio->adulto)->persona)->primer_apellido }}
                                        {{ optional(optional($fichaFisio->adulto)->persona)->segundo_apellido }}
                                    </td>
                                    <td>{{ optional(optional($fichaFisio->adulto)->persona)->ci }}</td>
                                    <td>{{ optional($fichaFisio->fecha_programacion)->format('d/m/Y') ?? 'N/A' }}</td>
                                    <td>{{ Str::limit($fichaFisio->motivo_consulta, 50) ?? 'N/A' }}</td>
                                    <td>{{ optional(optional($fichaFisio->usuario)->persona)->nombres }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('responsable.fisioterapia.reportefisio.exportWordIndividual', ['cod_fisio' => $fichaFisio->cod_fisio]) }}" class="btn btn-sm btn-info" data-bs-toggle="tooltip" title="Exportar Word">
                                                <i class="fe fe-file-text"></i>
                                            </a>
                                            <form action="{{ route('responsable.fisioterapia.reportefisio.destroy', ['cod_fisio' => $fichaFisio->cod_fisio]) }}" method="POST" class="d-inline form-delete-ficha-fisio">
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
                                    <td colspan="7" class="text-center text-muted">
                                        <i class="fe fe-inbox" style="font-size: 48px;"></i>
                                        <br>
                                        No se encontraron fichas que coincidan con los filtros aplicados.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    @if(method_exists($fichasFisioterapia, 'links') && $fichasFisioterapia->hasPages())
                        <div class="d-flex justify-content-center mt-3">
                            {{-- Se añaden los parámetros de filtro a la paginación --}}
                            {{ $fichasFisioterapia->appends(request()->query())->links() }}
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
        // Inicializar Feather Icons
        if (typeof feather !== 'undefined') {
            feather.replace();
        }

        // Inicializar Tooltips de Bootstrap
        if (typeof bootstrap !== 'undefined' && typeof bootstrap.Tooltip !== 'undefined') {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            })
        }

        // --- MANEJO DE ALERTAS CON SWEETALERT2 ---
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
                toast: true,
                position: 'top-end',
                icon: 'error',
                title: '¡Error!',
                text: "{{ session('error') }}",
                showConfirmButton: false,
                timer: 5000,
                timerProgressBar: true
            });
        @endif

        // --- CONFIRMACIÓN DE ELIMINACIÓN CON SWEETALERT2 ---
        document.querySelectorAll('.form-delete-ficha-fisio').forEach(form => {
            form.addEventListener('submit', function(event) {
                event.preventDefault(); // Prevenir el envío inmediato
                
                Swal.fire({
                    title: '¿Está seguro de que desea eliminar esta ficha?',
                    text: "¡Esta acción no se puede deshacer!",
                    icon: 'warning', // Icono más apropiado para advertencia
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit(); // Si el usuario confirma, se envía el formulario
                    }
                });
            });
        });
    });
</script>
@endpush
