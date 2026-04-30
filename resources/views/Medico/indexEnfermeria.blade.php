@extends('layouts.main')

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Módulo Enfermería - Fichas de Enfermería</title>
    <!-- Añadimos jQuery (si es necesario para algún otro script en dashboard.css o generales) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Añadimos el CSS del dashboard y el nuevo CSS específico para este módulo -->
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/Medico/indexEnfermeria.css') }}"> {{-- Este CSS es ahora el mismo que indexHistoriaClinica.css --}}
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    {{-- SweetAlert2 CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
</head>

@section('content')
<body>
    <div class="page-header">
        <h1 class="page-title">Módulo Enfermería / Listado de Adultos Mayores (Fichas de Enfermería)</h1>
    </div>

    {{-- Las alertas de sesión se manejarán con JS a través de SweetAlert2 --}}
    
    <!-- Tarjetas de estadísticas -->
    <div class="row">
        <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
            {{-- Tarjeta: Total Adultos Mayores --}}
            <div class="card overflow-hidden sales-card bg-primary-gradient">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h6 class="card-text mb-0 text-white">Total Adultos Mayores</h6>
                            <h4 class="mb-0 num-text text-white">{{ $totalAdultosMayores ?? 0 }}</h4>
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
            {{-- Tarjeta: Total Fichas de Enfermería Registradas --}}
            <div class="card overflow-hidden sales-card bg-info-gradient"> {{-- Usamos un gradiente azul claro para diferenciarse --}}
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h6 class="card-text mb-0 text-white">Fichas Enfermería Registradas</h6>
                            <h4 class="mb-0 num-text text-white">{{ $totalFichasEnfermeria ?? 0 }}</h4>
                        </div>
                        <div class="col col-auto">
                            <div class="counter-icon bg-gradient-info ms-auto box-shadow-info"> {{-- Icono y gradiente azul claro --}}
                                <i class="fe fe-heart text-white mb-5"></i> {{-- Puedes cambiar el icono a algo más relacionado con enfermería si tienes uno, ej. fe fe-activity o fe fe-plus-square --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- Puedes añadir más tarjetas si necesitas otras estadísticas --}}
    </div>

    <!-- Tabla de adultos mayores -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Listado de Adultos Mayores para Gestión de Fichas de Enfermería</h3>
                </div>
                <!-- Buscador -->
                <div class="card-body">
                    <form action="{{ route('responsable.enfermeria.enfermeria.index') }}" method="GET" class="buscador row mb-4">
                        <div class="col-md-12">
                            <div class="input-group">
                                <button type="submit" class="input-group-text bg-primary text-white border-0" id="buscarButton">
                                    <i class="fe fe-search"></i>
                                </button>
                                <input type="text"
                                       name="search"
                                       class="form-control"
                                       id="busquedaInput"
                                       placeholder="Buscar por CI, nombres o apellidos..."
                                       autocomplete="off"
                                       value="{{ $search ?? '' }}">
                                @if ($search)
                                    <a href="{{ route('responsable.enfermeria.enfermeria.index') }}" class="btn btn-outline-secondary" id="limpiarBusqueda">
                                        <i class="fe fe-x"></i> Limpiar
                                    </a>
                                @endif
                            </div>
                            <small class="text-muted">Búsqueda por CI, nombres y apellidos.</small>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table id="adultosTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Nombre completo</th>
                                    <th>CI</th>
                                    <th>Fecha Registro Adulto</th>
                                    <th>Última Ficha Enfermería</th>
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
                                            @if($adulto->latestEnfermeria)
                                                {{ $adulto->latestEnfermeria->created_at->format('d/m/Y') }}
                                                <br>
                                                <span class="text-muted">(Cód: {{ $adulto->latestEnfermeria->cod_enf }})</span>
                                            @else
                                                <span class="text-muted">Sin ficha</span>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                // Determinar si el adulto mayor tiene al menos una ficha de enfermería
                                                $hasEnfermeriaFicha = $adulto->latestEnfermeria !== null; // O $adulto->enfermerias->isNotEmpty(); si prefieres esa verificación
                                            @endphp
                                            <div class="btn-group" role="group">
                                                {{-- Botón para registrar una NUEVA FICHA DE ENFERMERÍA para este adulto mayor --}}
                                                <a href="{{ route('responsable.enfermeria.enfermeria.create', ['id_adulto' => $adulto->id_adulto]) }}"
                                                    class="btn btn-sm btn-success"
                                                    data-bs-toggle="tooltip"
                                                    title="Registrar Nueva Ficha de Enfermería">
                                                    <i class="fe fe-file-plus"></i>
                                                </a>

                                                {{-- Botón para EDITAR la ÚLTIMA FICHA DE ENFERMERÍA de este adulto mayor --}}
                                                <a href="{{ $hasEnfermeriaFicha ? route('responsable.enfermeria.enfermeria.edit', ['cod_enf' => $adulto->latestEnfermeria->cod_enf]) : 'javascript:void(0)' }}"
                                                    class="btn btn-sm {{ !$hasEnfermeriaFicha ? 'btn-light text-muted disabled' : 'btn-primary' }}"
                                                    data-bs-toggle="tooltip"
                                                    title="{{ !$hasEnfermeriaFicha ? 'No hay ficha de enfermería para editar.' : 'Editar Última Ficha' }}"
                                                    aria-disabled="{{ !$hasEnfermeriaFicha ? 'true' : 'false' }}">
                                                    <i class="fe fe-edit"></i>
                                                </a>

                                                {{-- Botón para VER DETALLES de la ÚLTIMA FICHA DE ENFERMERÍA del Adulto Mayor --}}
                                                <a href="{{ $hasEnfermeriaFicha ? route('responsable.enfermeria.enfermeria.show', ['id_adulto' => $adulto->id_adulto]) : 'javascript:void(0)' }}" {{-- CORREGIDO: PASA ID_ADULTO --}}
                                                    class="btn btn-sm {{ !$hasEnfermeriaFicha ? 'btn-light text-muted disabled' : 'btn-info' }}"
                                                    data-bs-toggle="tooltip"
                                                    title="{{ !$hasEnfermeriaFicha ? 'No hay detalles de ficha de enfermería para ver.' : 'Ver Historial de Fichas de Enfermería' }}"
                                                    aria-disabled="{{ !$hasEnfermeriaFicha ? 'true' : 'false' }}">
                                                    <i class="fe fe-list"></i> {{-- Cambiado de fe fe-eye a fe fe-list para historial --}}
                                                </a>

                                                {{-- Botón para ELIMINAR la ÚLTIMA FICHA DE ENFERMERÍA del Adulto Mayor --}}
                                                @if($hasEnfermeriaFicha)
                                                    <form action="{{ route('responsable.enfermeria.enfermeria.destroy', ['cod_enf' => $adulto->latestEnfermeria->cod_enf]) }}"
                                                        method="POST"
                                                        class="d-inline form-delete-enfermeria"> {{-- Clase para JS --}}
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                                class="btn btn-sm btn-danger"
                                                                data-bs-toggle="tooltip"
                                                                title="Eliminar Última Ficha">
                                                            <i class="fe fe-trash-2"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <button class="btn btn-sm btn-light text-muted" disabled
                                                            data-bs-toggle="tooltip"
                                                            title="No hay ficha de enfermería para eliminar.">
                                                        <i class="fe fe-trash-2"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">
                                            <i class="fe fe-inbox"></i>
                                            <br>
                                            No hay adultos mayores que coincidan con la búsqueda.
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
{{-- SweetAlert2 JS --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof feather !== 'undefined') {
        feather.replace();
    }

    // Inicializar tooltips de Bootstrap
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })

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

    // Confirmación para eliminar ficha de enfermería (usando la nueva clase .form-delete-enfermeria)
    document.querySelectorAll('.form-delete-enfermeria').forEach(form => {
        form.addEventListener('submit', function(event) {
            event.preventDefault(); // Prevenir el envío inmediato del formulario
            
            Swal.fire({
                title: '¿Está seguro?',
                text: "Se eliminará la última ficha de enfermería. ¡Esta acción no se puede deshacer!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.submit(); // Enviar el formulario si el usuario confirma
                }
            });
        });
    });
});
</script>

@endpush
</body>
</html>