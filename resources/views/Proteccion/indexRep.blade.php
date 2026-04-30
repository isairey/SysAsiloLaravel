@extends('layouts.main')

{{-- Mover las etiquetas <head> a los bloques @push('styles') y @section('title') --}}
@section('title', 'Reportes de Casos de Protección')

@push('styles')
    <!-- Añadimos el CSS del dashboard y el nuevo CSS específico -->
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/Proteccion/indexRep.css') }}">
    
    <!-- DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.11.5/datatables.min.css"/>
    
    {{-- Cargamos SweetAlert2 CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endpush

@section('content')
    <div class="page-header">
        <h1 class="page-title">Módulo de Protección / Reportes de Casos</h1>
        <div>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Inicio</a></li>
                <li class="breadcrumb-item active" aria-current="page">Reportes de Casos</li>
            </ol>
        </div>
    </div>

    {{-- Las alertas de sesión ahora se manejan con SweetAlert2 en el script --}}

    <!-- Tarjetas de estadísticas (ajustado para paginación) -->
    <div class="row">
        <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
            <div class="card overflow-hidden sales-card bg-primary-gradient">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h6 class="card-text mb-0 text-white">Total Casos (Página Actual)</h6>
                            <h4 class="mb-0 num-text text-white">{{ $casos->count() ?? 0 }}</h4>
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

    <!-- Tabla de casos -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Listado de Casos de Protección</h3>
                </div>
                <!-- Filtros -->
                <div class="card-body">
                    <form action="{{ route('legal.reportes_proteccion.index') }}" method="GET" class="form-filter">
                        <div class="row mb-3"> {{-- Removido align-items-end --}}
                            <div class="col-md-2 mb-3 mb-md-0"> {{-- Columna para Nro. Caso (ajustado a 2) --}}
                                <label for="nro_caso_filter" class="form-label text-xs text-gray-600 mb-1">Filtrar por Nro. Caso:</label>
                                <input type="text"
                                    class="form-control"
                                    id="nro_caso_filter"
                                    name="nro_caso_filter"
                                    placeholder="Ej. 2024-001"
                                    value="{{ $nro_caso_filter ?? '' }}">
                            </div>

                            <div class="col-md-4 mb-3 mb-md-0"> {{-- Columna para Buscador General --}}
                                <label for="busquedaAdultoInput" class="form-label text-xs text-gray-600 mb-1">Buscador General (Adulto Mayor):</label>
                                <div class="input-group">
                                    <button type="submit" class="btn btn-primary" id="buscarAdultoButton">
                                        <i class="fe fe-search"></i>
                                    </button>
                                    <input type="text"
                                        class="form-control"
                                        id="busquedaAdultoInput"
                                        name="search"
                                        placeholder="Buscar por CI, nombres o apellidos del AM..."
                                        autocomplete="off"
                                        value="{{ $search ?? '' }}">
                                </div>
                            </div>
                            
                            <div class="col-md-4 mb-3 mb-md-0"> {{-- Columna para Buscador Ofensor --}}
                                <label for="busquedaDenunciadoInput" class="form-label text-xs text-gray-600 mb-1">Buscador (Ofensor):</label>
                                <div class="input-group">
                                    <button type="submit" class="btn btn-primary" id="buscarDenunciadoButton">
                                        <i class="fe fe-search"></i>
                                    </button>
                                    <input type="text"
                                        class="form-control"
                                        id="busquedaDenunciadoInput"
                                        name="denunciado_search"
                                        placeholder="Buscar por nombres o apellidos del Ofensor..."
                                        autocomplete="off"
                                        value="{{ $denunciado_search ?? '' }}">
                                </div>
                            </div>

                            <div class="col-md-2 d-grid gap-2"> {{-- Columna para botones de acción (ajustado a 2) --}}
                                {{-- Los botones se apilarán verticalmente dentro de esta columna --}}
                                <button type="submit" class="btn btn-primary">Aplicar Filtros</button>
                                <button type="button"
                                    class="btn btn-outline-secondary reset"
                                    onclick="window.location.href='{{ route('legal.reportes_proteccion.index')}}'">
                                    <i class="fe fe-x"></i> Restablecer
                                </button>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table id="casosTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Nro. Caso</th>
                                    <th>Fecha Registro</th>
                                    <th>Nombres Adulto</th>
                                    <th>Apellidos Adulto</th>
                                    <th>CI Adulto</th>
                                    <th>Nombre Completo Ofensor</th>
                                    <th>Discapacidad</th>
                                    <th>Vive con</th>
                                    <th>Migrante</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($casos as $caso)
                                    <tr>
                                        <td>{{ mb_strtoupper($caso->nro_caso ?? 'N/A') }}</td>
                                        <td>{{ mb_strtoupper(optional($caso->fecha)->format('d/m/Y') ?? 'N/A') }}</td>
                                        <td>{{ mb_strtoupper(optional($caso->persona)->nombres ?? 'N/A') }}</td>
                                        <td>
                                            {{ mb_strtoupper(optional($caso->persona)->primer_apellido ?? 'N/A') }}
                                            @if(optional($caso->persona)->segundo_apellido)
                                                {{ mb_strtoupper(optional($caso->persona)->segundo_apellido) }}
                                            @endif
                                        </td>
                                        <td>{{ mb_strtoupper(optional($caso->persona)->ci ?? 'N/A') }}</td>
                                        <td>
                                            @if(optional($caso->denunciado)->personaNatural)
                                                {{ mb_strtoupper(optional($caso->denunciado->personaNatural)->nombres ?? '') }}
                                                {{ mb_strtoupper(optional($caso->denunciado->personaNatural)->primer_apellido ?? '') }}
                                                {{ mb_strtoupper(optional($caso->denunciado->personaNatural)->segundo_apellido ?? '') }}
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td>{{ mb_strtoupper($caso->discapacidad ?? 'N/A') }}</td>
                                        <td>{{ mb_strtoupper($caso->vive_con ?? 'N/A') }}</td>
                                        <td>{{ mb_strtoupper(($caso->migrante === true ? 'Sí' : ($caso->migrante === false ? 'No' : 'N/A'))) }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('legal.reportes_proteccion.exportWordIndividual', ['id_adulto' => $caso->id_adulto]) }}"
                                                    class="btn btn-sm btn-primary"
                                                    data-bs-toggle="tooltip"
                                                    title="Exportar a Word">
                                                    <i class="fe fe-file-text"></i>
                                                </a>
                                                {{-- NUEVO: Botón "Exportar a PDF" --}}
                                                <a href="{{ route('legal.reportes_proteccion.exportPdfIndividual', ['id_adulto' => $caso->id_adulto]) }}"
                                                    class="btn btn-sm btn-info" 
                                                    data-bs-toggle="tooltip"
                                                    title="Exportar a PDF">
                                                    <i class="fe fe-file"></i>
                                                </a>
                                                {{-- Se reemplaza onsubmit por una clase para manejar con JS --}}
                                                <form action="{{ route('legal.caso.destroy', ['id_adulto' => $caso->id_adulto]) }}"
                                                    method="POST"
                                                    style="display:inline-block;"
                                                    class="d-inline form-delete-case">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="btn btn-sm btn-danger"
                                                            data-bs-toggle="tooltip"
                                                            title="Eliminar Caso">
                                                        <i class="fe fe-trash-2"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center text-muted">
                                            <i class="fe fe-inbox"></i>
                                            <br>
                                            No se encontraron casos de protección
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    @if(method_exists($casos, 'links') && $casos->hasPages())
                        <div class="d-flex justify-content-center mt-3">
                            {{ $casos->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
{{-- Añadimos jQuery (requerido por DataTables) --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- DataTables JS -->
<script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.11.5/datatables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
{{-- Cargamos SweetAlert2 JS --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof feather !== 'undefined') {
        feather.replace();
    }

    // Inicialización de DataTables
    if (typeof $().DataTable === 'function') {
        var table = $('#casosTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json'
            },
            responsive: true,
            paging: false,
            info: false,
            searching: false,
            ordering: true,
            order: [[1, 'desc']],
            columnDefs: [
                { targets: [9], orderable: false, searchable: false }
            ],
            dom: 'Bfrtip',
            buttons: [
                // Estos botones de DataTable solo funcionarán para los datos visibles en la página actual.
                // Si la paginación es manejada por Laravel, es mejor tener botones de exportación custom.
                // Los mantengo por si es un requisito, pero ten en cuenta la limitación.
                { extend: 'excel', text: '<i class="fe fe-download"></i> Excel', className: 'btn btn-success btn-sm' },
                { extend: 'pdf', text: '<i class="fe fe-file-text"></i> PDF', className: 'btn btn-danger btn-sm' },
                { extend: 'print', text: '<i class="fe fe-printer"></i> Imprimir', className: 'btn btn-info btn-sm' }
            ]
        });
    }

    // Evento para el botón "Restablecer"
    $('.reset').on('click', function() {
        window.location.href = '{{ route('legal.reportes_proteccion.index') }}';
    });

    // Inicializar tooltips de Bootstrap
    if (typeof bootstrap !== 'undefined' && typeof bootstrap.Tooltip !== 'undefined') {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }

    // --- MANEJO DE ALERTAS CON SWEETALERT2 ---

    // Alertas de sesión
    @if(session('success'))
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

    // Confirmación para eliminar caso
    $('.form-delete-case').on('submit', function(event) {
        event.preventDefault();
        const form = this;
        Swal.fire({
            title: '¿Está seguro de que desea eliminar este caso?',
            text: "Esto eliminará todos los datos relacionados y la acción no se puede deshacer.",
            icon: 'error',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
});
</script>
@endpush
