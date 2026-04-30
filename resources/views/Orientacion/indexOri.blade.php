indexOri.blade.php:
@extends('layouts.main')

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Módulo de Orientación / Adultos Mayores</title>
    <!-- Añadimos jQuery (requerido por DataTables) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Añadimos el CSS del dashboard y el nuevo CSS específico -->
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    {{-- Usamos el CSS de indexRep.css ya que parece contener los estilos para las tarjetas y el buscador --}}
    <link rel="stylesheet" href="{{ asset('css/Orientacion/indexRep.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    <!-- DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.11.5/datatables.min.css"/>
    {{-- Cargamos SweetAlert2 CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
</head>

@section('content')

<body>
    <div class="page-header">
        <h1 class="page-title">Módulo de Orientación / Listado de Adultos Mayores</h1>
        <div>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Inicio</a></li>
                <li class="breadcrumb-item active" aria-current="page">Adultos Mayores</li>
            </ol>
        </div>
    </div>

    <!-- Mensajes de éxito o error (usando SweetAlert2 para consistencia) -->
    {{-- Las alertas de sesión se manejarán con SweetAlert2 --}}
    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
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
            });
        </script>
    @endif
    @if (session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
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
            });
        </script>
    @endif

    <!-- Tarjetas de estadísticas -->
    <div class="row">
        <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
            <div class="card overflow-hidden sales-card bg-primary-gradient">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h6 class="card-text mb-0 text-white">Total Adultos Mayores</h6>
                            <h4 class="mb-0 num-text text-white">{{ $adultos->total() ?? 0 }}</h4>
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

        {{-- NUEVA TARJETA: Total Fichas Registradas --}}
        <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
            <div class="card overflow-hidden sales-card bg-info-gradient"> {{-- Usamos bg-info-gradient para el color azul --}}
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h6 class="card-text mb-0 text-white">Total Fichas Registradas</h6>
                            {{-- Se asume que \App\Models\Orientacion::count() puede obtener el total, o se pasa desde el controlador --}}
                            <h4 class="mb-0 num-text text-white">{{ \App\Models\Orientacion::count() }}</h4>
                        </div>
                        <div class="col col-auto">
                            <div class="counter-icon bg-gradient-info ms-auto box-shadow-info"> {{-- Usamos bg-gradient-info para el icono --}}
                                <i class="fe fe-bar-chart-2 text-white mb-5"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- FIN NUEVA TARJETA --}}

    </div>
    <!-- Tabla de adultos mayores -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Listado de Adultos Mayores</h3>
                </div>
                <!-- Buscador -->
                <div class="buscador row mb-4">
                    <div class="col-md-12">
                        <div class="input-group">
                            <button type="button" class="input-group-text bg-primary text-white border-0" id="buscarButton">
                                <i class="fe fe-search"></i>
                            </button>
                            <input type="text"
                                class="form-control"
                                id="busquedaInput"
                                placeholder="Buscar por CI, nombres o apellidos..."
                                autocomplete="off">
                            <button type="button" class="btn btn-outline-secondary" id="limpiarBusqueda">
                                <i class="fe fe-x"></i> Limpiar
                            </button>
                        </div>
                        <small class="text-muted">Búsqueda en tiempo real por CI, nombres y apellidos.</small>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="adultosTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Nombre completo</th>
                                    <th>CI</th>
                                    <th>Fecha Registro</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($adultos as $adulto)
                                    <tr>
                                        <td>
                                            <strong>{{ $adulto->persona->nombres }} {{ $adulto->persona->primer_apellido }}</strong>
                                            @if($adulto->persona->segundo_apellido)
                                                {{ $adulto->persona->segundo_apellido }}
                                            @endif
                                        </td>
                                        <td>{{ $adulto->persona->ci }}</td>
                                        <td>
                                            @if($adulto->created_at)
                                                {{ $adulto->created_at->format('d/m/Y') }}
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                // Determinar si el adulto mayor tiene al menos una ficha de orientación
                                                $hasOrientationCase = $adulto->latestOrientacion()->exists();
                                            @endphp

                                            <div class="btn-group" role="group">
                                                {{-- Botón para registrar nueva ficha de orientación: Deshabilitado si ya tiene una ficha --}}
                                                <a href="{{ route('legal.orientacion.register', ['id_adulto' => $adulto->id_adulto]) }}"
                                                    class="btn btn-sm {{ $hasOrientationCase ? 'btn-light text-muted disabled' : 'btn-primary' }}"
                                                    data-bs-toggle="tooltip"
                                                    title="{{ $hasOrientationCase ? 'Ya tiene una ficha de orientación registrada' : 'Registrar ficha de orientación' }}"
                                                    aria-disabled="{{ $hasOrientationCase ? 'true' : 'false' }}">
                                                    <i class="fe fe-file-plus"></i>
                                                </a>
                                                {{-- Botón para editar la ÚLTIMA Ficha de Orientación: Habilitado solo si tiene una ficha --}}
                                                <a href="{{ route('legal.orientacion.edit', ['id_adulto' => $adulto->id_adulto]) }}" {{-- NOTA: La ruta de edición es por id_adulto --}}
                                                    class="btn btn-sm {{ !$hasOrientationCase ? 'btn-light text-muted disabled' : 'btn-warning' }}"
                                                    data-bs-toggle="tooltip"
                                                    title="{{ !$hasOrientationCase ? 'No hay ficha de orientación para editar' : 'Editar Ficha' }}"
                                                    aria-disabled="{{ !$hasOrientationCase ? 'true' : 'false' }}">
                                                    <i class="fe fe-edit"></i>
                                                </a>
                                                {{-- Botón para ver los detalles completos de la ÚLTIMA FICHA DE ORIENTACIÓN del Adulto Mayor --}}
                                                <a href="{{ $hasOrientationCase ? route('legal.orientacion.show', ['cod_or' => $adulto->latestOrientacion->cod_or]) : 'javascript:void(0)' }}"
                                                    class="btn btn-sm {{ !$hasOrientationCase ? 'btn-light text-muted disabled' : 'btn-info' }}"
                                                    data-bs-toggle="tooltip"
                                                    title="{{ !$hasOrientationCase ? 'No hay ficha de orientación para ver detalles' : 'Ver Detalles de Ficha' }}"
                                                    aria-disabled="{{ !$hasOrientationCase ? 'true' : 'false' }}">
                                                    <i class="fe fe-eye"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">
                                            <i class="fe fe-inbox"></i>
                                            <br>
                                            No hay adultos mayores registrados.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación si está disponible -->
                    @if(method_exists($adultos, 'links') && $adultos->hasPages())
                        <div class="d-flex justify-content-center mt-3">
                            {{ $adultos->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
{{-- Cargamos SweetAlert2 JS --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof feather !== 'undefined') {
        feather.replace();
    }

    // Inicialización de tooltips (asegurarse de que Bootstrap esté cargado)
    if (typeof bootstrap !== 'undefined' && typeof bootstrap.Tooltip !== 'undefined') {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }

    // DataTables Initialization (manteniendo tu configuración existente)
    if (typeof $().DataTable === 'function') {
        var table = $('#adultosTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json'
            },
            responsive: true,
            paging: false,    // ocultamos paginación (funcionalmente)
            info: false,      // ocultamos texto tipo "Showing X of Y"
            searching: true,  // ⚠️ debe seguir en true para que funcione tu input personalizado
            ordering: true,
            pageLength: 25,
            order: [[2, 'desc']],
            columnDefs: [
                { targets: [3], orderable: false, searchable: false }
            ],
            dom: 'rtip', // Esto muestra solo la tabla y la información de paginación básica
            buttons: [
                { extend: 'excel', text: '<i class="fe fe-download"></i> Excel', className: 'btn btn-success btn-sm' },
                { extend: 'pdf', text: '<i class="fe fe-file-text"></i> PDF', className: 'btn btn-danger btn-sm' },
                { extend: 'print', text: '<i class="fe fe-printer"></i> Imprimir', className: 'btn btn-info btn-sm' }
            ]
        });

        $('#busquedaInput').on('input', function () {
            table.search(this.value).draw();
        });

        $('#buscarButton').on('click', function () {
            table.search($('#busquedaInput').val()).draw();
        });

        $('#limpiarBusqueda').on('click', function () {
            $('#busquedaInput').val('');
            table.search('').draw();
        });
    }

    // Lógica para asegurar accesibilidad de los enlaces deshabilitados.
    document.querySelectorAll('.btn-group a.disabled').forEach(function(disabledLink) {
        disabledLink.setAttribute('tabindex', '-1'); // Hace que no sea enfocable con teclado
    });

    // Re-inicializar tooltips para elementos que puedan haber sido modificados o añadidos dinámicamente
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
</body>
</html>
@endpush
