@extends('layouts.main')

<head>
    <!-- Añadimos el CSS del dashboard y el nuevo CSS específico -->
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/Proteccion/indexPro.css') }}">
    <!-- DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.11.5/datatables.min.css"/>
    {{-- Cargamos SweetAlert2 CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
</head>

@push('scripts')
    <!-- Añadimos jQuery (requerido por DataTables) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    {{-- Cargamos DataTables y SweetAlert2 JS --}}
    <script src="https://cdn.datatables.net/v/dt/dt-1.11.5/datatables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@section('content')

    <div class="page-header">
        <h1 class="page-title">Modulo de Proteccion / Adultos Mayores Registrados</h1>
        <div>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Inicio</a></li>
                <li class="breadcrumb-item active" aria-current="page">Adultos Mayores</li>
            </ol>
        </div>
    </div>

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
    
    <!-- Tarjetas de estadísticas (opcional) -->
    <div class="row">
        <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
            <div class="card overflow-hidden sales-card bg-primary-gradient">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h6 class="card-text mb-0 text-white">Total Registrados</h6>
                            <h4 class="mb-0 num-text text-white">{{ $adultos->total() ?? 0 }}</h4> {{-- Usar total() para paginación --}}
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
                                                // Determinar si el adulto mayor tiene un caso de protección "activo"
                                                // La lógica debe ser consistente con lo que define un "caso existente" en tu aplicación.
                                                // Si el adulto tiene *cualquiera* de estas relaciones, se considera que tiene un caso.
                                                $hasProtectionCase = $adulto->actividadLaboral()->exists() ||
                                                                     $adulto->encargados()->exists() ||
                                                                     $adulto->denunciado()->exists() ||
                                                                     $adulto->grupoFamiliar()->exists() ||
                                                                     $adulto->croquis()->exists() ||
                                                                     $adulto->seguimientos()->exists() ||
                                                                     $adulto->anexoN3()->exists() ||
                                                                     $adulto->anexoN5()->exists();
                                            @endphp

                                            <div class="btn-group" role="group">
                                                {{-- Botón para registrar nuevo caso: Deshabilitado si ya tiene un caso --}}
                                                <a href="{{ route('legal.caso.register', ['id_adulto' => $adulto->id_adulto]) }}" 
                                                    class="btn btn-sm {{ $hasProtectionCase ? 'btn-light text-muted disabled' : 'btn-primary' }}"
                                                    data-bs-toggle="tooltip"
                                                    title="{{ $hasProtectionCase ? 'Ya tiene un caso registrado' : 'Registrar nuevo caso' }}"
                                                    aria-disabled="{{ $hasProtectionCase ? 'true' : 'false' }}">
                                                    <i class="fe fe-plus"></i>
                                                </a>
                                                {{-- Botón para editar información: Habilitado solo si tiene un caso, con estilo deshabilitado si no lo tiene --}}
                                                <a href="{{ route('legal.caso.edit', ['id_adulto' => $adulto->id_adulto]) }}" 
                                                    class="btn btn-sm {{ !$hasProtectionCase ? 'btn-light text-muted disabled' : 'btn-warning' }}"
                                                    data-bs-toggle="tooltip"
                                                    title="{{ !$hasProtectionCase ? 'No hay caso registrado para editar' : 'Editar información' }}"
                                                    aria-disabled="{{ !$hasProtectionCase ? 'true' : 'false' }}">
                                                    <i class="fe fe-edit"></i>
                                                </a>
                                                {{-- Botón para ver detalles: Habilitado solo si tiene un caso, con estilo deshabilitado si no lo tiene --}}
                                                <a href="{{ route('legal.caso.detalle', ['id_adulto' => $adulto->id_adulto]) }}"
                                                    class="btn btn-sm {{ !$hasProtectionCase ? 'btn-light text-muted disabled' : 'btn-info' }}" 
                                                    data-bs-toggle="tooltip"
                                                    title="{{ !$hasProtectionCase ? 'No hay caso registrado para ver detalles' : 'Ver detalles completos' }}"
                                                    aria-disabled="{{ !$hasProtectionCase ? 'true' : 'false' }}">
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
                                            No hay adultos mayores registrados
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
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof feather !== 'undefined') {
        feather.replace();
    }

    // Inicialización de tooltips (asegurarse de que Bootstrap esté cargado)
    // Se inicializa dos veces para asegurar que los elementos agregados/modificados por la lógica de Blade también tengan tooltips.
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
            paging: false, // Deshabilitar paginación de DataTables ya que Laravel la maneja
            info: false,  // Deshabilitar info de DataTables
            searching: true,
            ordering: true,
            pageLength: 25,
            order: [[2, 'desc']],
            columnDefs: [
                { targets: [3], orderable: false, searchable: false }
            ],
            dom: 'Bfrtip',
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
    // Los tooltips ya deberían funcionar por la inicialización general.
    document.querySelectorAll('.btn-group a.disabled').forEach(function(disabledLink) {
        disabledLink.setAttribute('tabindex', '-1'); // Hace que no sea enfocable con teclado
    });

    // Se inicializan los tooltips al final de todos los ajustes del DOM para asegurar que se apliquen correctamente.
    // Aunque ya existe una inicialización arriba, duplicarla aquí para mayor seguridad no hace daño
    // en este contexto, ya que los tooltips son Bootstrap.Tooltip.
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

});
</script>
@endpush
