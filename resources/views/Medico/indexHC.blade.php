@extends('layouts.main')

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Módulo Médico - Historias Clínicas</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/Medico/indexHC.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.11.5/datatables.min.css"/>
    {{-- Cargamos SweetAlert2 CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
</head>

@section('content')
<body>

                    <div class="page-header">
                            <h1 class="page-title">Módulo Médico / Listado de Adultos Mayores (Historia Clínica)</h1>
                            <div>
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="javascript:void(0)">Inicio</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Historia Clínica</li>
                                </ol>
                            </div>
                    </div>

                    {{-- Los mensajes de sesión ahora se manejarán con SweetAlert2 --}}

                    <div class="row">
                            <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
                                {{-- Tarjeta: Total Adultos Mayores --}}
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

                            <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
                                {{-- Tarjeta: Total Historias Clínicas Registradas --}}
                                <div class="card overflow-hidden sales-card bg-success-gradient">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col">
                                                <h6 class="card-text mb-0 text-white">Historias Registradas</h6>
                                                <h4 class="mb-0 num-text text-white">{{ \App\Models\HistoriaClinica::count() }}</h4>
                                            </div>
                                            <div class="col col-auto">
                                                <div class="counter-icon bg-gradient-success ms-auto box-shadow-success">
                                                    <i class="fe fe-heart text-white mb-5"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    </div>

                    <div class="row mt-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">Listado de Adultos Mayores para Gestión de Historia Clínica</h3>
                                    </div>
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
                                                        <th>Fecha Registro Adulto</th>
                                                        <th>Acciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($adultos as $adulto)
                                                    <tr>
                                                        <td>
                                                            <strong>{{ optional($adulto->persona)->nombres }} {{ optional($adulto->persona)->primer_apellido }}</strong>
                                                            @if(optional($adulto->persona)->segundo_apellido)
                                                                {{ optional($adulto->persona)->segundo_apellido }}
                                                            @endif
                                                        </td>
                                                        <td>{{ optional($adulto->persona)->ci }}</td>
                                                        <td>
                                                            @if($adulto->created_at)
                                                                {{ $adulto->created_at->format('d/m/Y') }}
                                                            @else
                                                                <span class="text-muted">N/A</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <div class="btn-group" role="group">
                                                                {{-- Lógica para el botón Registrar Historia Clínica --}}
                                                                @if(!$adulto->latestHistoriaClinica) {{-- Si NO existe una historia clínica --}}
                                                                    <a href="{{ route('responsable.enfermeria.medico.historia_clinica.register', ['id_adulto' => $adulto->id_adulto]) }}"
                                                                        class="btn btn-sm btn-primary"
                                                                        data-bs-toggle="tooltip"
                                                                        title="Registrar Historia Clínica">
                                                                        <i class="fe fe-file-plus"></i>
                                                                    </a>
                                                                @else {{-- Si SÍ existe una historia clínica --}}
                                                                    <button class="btn btn-sm btn-light text-muted" disabled
                                                                            data-bs-toggle="tooltip"
                                                                            title="Ya tiene una historia clínica registrada.">
                                                                        <i class="fe fe-file-plus"></i>
                                                                    </button>
                                                                @endif

                                                                @if($adulto->latestHistoriaClinica)
                                                                    <a href="{{ route('responsable.enfermeria.medico.historia_clinica.edit', ['id_historia' => $adulto->latestHistoriaClinica->id_historia]) }}"
                                                                        class="btn btn-sm btn-warning"
                                                                        data-bs-toggle="tooltip"
                                                                        title="Editar Última Historia">
                                                                        <i class="fe fe-edit"></i>
                                                                    </a>
                                                                @else
                                                                    <button class="btn btn-sm btn-light text-muted" disabled
                                                                            data-bs-toggle="tooltip"
                                                                            title="No hay historia clínica para editar.">
                                                                        <i class="fe fe-edit"></i>
                                                                    </button>
                                                                @endif

                                                                @if($adulto->latestHistoriaClinica)
                                                                    <a href="{{ route('responsable.enfermeria.medico.historia_clinica.show_detalle', ['id_historia' => $adulto->latestHistoriaClinica->id_historia]) }}"
                                                                        class="btn btn-sm btn-info"
                                                                        data-bs-toggle="tooltip"
                                                                        title="Ver Detalles de Historia Clínica">
                                                                        <i class="fe fe-eye"></i>
                                                                    </a>
                                                                @else
                                                                    <button class="btn btn-sm btn-light text-muted" disabled
                                                                            data-bs-toggle="tooltip"
                                                                            title="No hay detalles de historia clínica para ver.">
                                                                        <i class="fe fe-eye-off"></i>
                                                                    </button>
                                                                @endif
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
<script src="https://cdn.datatables.net/v/dt/dt-1.11.5/datatables.min.js"></script>
{{-- Cargamos SweetAlert2 JS --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof feather !== 'undefined') {
        feather.replace();
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

    // --- LÓGICA DE DATATABLES Y BÚSQUEDA ---
    if (typeof $().DataTable === 'function') {
        var table = $('#adultosTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json'
            },
            responsive: true,
            paging: false,
            info: false,
            searching: true,
            ordering: true,
            pageLength: 25,
            order: [[2, 'desc']],
            columnDefs: [
                { targets: [3], orderable: false, searchable: false }
            ],
            dom: 'rtip',
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

    // --- CONFIRMACIÓN DE ELIMINACIÓN CON SWEETALERT2 ---
    document.querySelectorAll('.form-delete-hc').forEach(form => {
        form.addEventListener('submit', function(event) {
            event.preventDefault();
            Swal.fire({
                title: '¿Está seguro?',
                text: "Se eliminará la última historia clínica. ¡Esta acción no se puede deshacer!",
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

    // --- Inicialización de Tooltips ---
    if (typeof bootstrap !== 'undefined' && typeof bootstrap.Tooltip !== 'undefined') {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
});
</script>

@endpush
</body>
</html>