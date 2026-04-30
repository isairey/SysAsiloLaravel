@extends('layouts.main')

{{-- Contenido de la cabecera (HEAD) --}}
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Módulo Médico / Kinesiología</title>
    
    {{-- jQuery es requerido por DataTables, por ejemplo --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    {{-- Hojas de estilo --}}
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/Medico/indexFisioKine.css') }}"> 
    
    {{-- Feather Icons para los íconos --}}
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    
    {{-- SweetAlert2 CSS para alertas y confirmaciones --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
</head>

{{-- Contenido principal de la vista (BODY) --}}
@section('content')

    <div class="page-header">
        <h1 class="page-title">Módulo Médico / Kinesiología</h1>
        <div>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Inicio</a></li>
                <li class="breadcrumb-item active" aria-current="page">Kinesiología</li>
            </ol>
        </div>
    </div>

    {{-- Las alertas de sesión se manejarán con JS a través de SweetAlert2. --}}
    {{-- No se necesitan bloques @if(session('success'))/@if(session('error')) aquí. --}}
    
    <!-- Tarjetas de estadísticas -->
    <div class="row">
        <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
            <div class="card overflow-hidden sales-card bg-primary-gradient">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h6 class="card-text mb-0 text-white">Total Adultos Mayores Registrados</h6>
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
    </div>

    <!-- Botones para Navegación entre Módulos de Fisioterapia/Kinesiología -->
    <div class="mb-3 d-flex justify-content-start gap-2">
        {{-- Botón para ir al listado de Kinesiología (la vista actual) --}}
        <a href="{{ route('responsable.kinesiologia.fisiokine.indexKine') }}" class="btn btn-primary">
            <i class="fe fe-file-plus"></i> Kinesiología
        </a>
        {{-- Botón para ir al listado de Fisioterapia --}}
        <a href="{{ route('responsable.fisioterapia.fisiokine.indexFisio') }}" class="btn btn-secondary">
            <i class="fe fe-heart"></i> Fisioterapia
        </a>
    </div>

    <!-- Tabla de Adultos Mayores para Kinesiología -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Listado de Adultos Mayores para Fichas de Kinesiología</h3>
                </div>
                <div class="card-body">
                    {{-- Formulario de Filtros y Búsqueda --}}
                    <form id="filterFormKine" action="{{ route('responsable.kinesiologia.fisiokine.indexKine') }}" method="GET" class="form-filter mb-4">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-6">
                                <label for="search_kine" class="form-label">Buscar Adulto Mayor:</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="search_kine" name="search" placeholder="Buscar por CI, nombres, apellidos..." value="{{ $search ?? '' }}">
                                    <button type="submit" class="btn btn-primary"><i class="fe fe-search"></i> Buscar</button>
                                    {{-- El botón Restablecer debe apuntar a la ruta de índice sin parámetros de búsqueda --}}
                                    <a href="{{ route('responsable.kinesiologia.fisiokine.indexKine') }}" class="btn btn-outline-secondary"><i class="fe fe-x"></i> Restablecer</a>
                                </div>
                                <small class="text-muted">Busca por CI, nombres o apellidos del adulto mayor.</small>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table id="adultosTableKine" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID Adulto</th>
                                    <th>CI</th>
                                    <th>Nombres y Apellidos</th>
                                    <th>Fecha Nacimiento</th>
                                    <th>Teléfono</th>
                                    <th>Acciones Kinesiología</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($adultos as $adulto)
                                    <tr>
                                        <td>{{ $adulto->id_adulto }}</td>
                                        <td>{{ optional($adulto->persona)->ci ?? 'N/A' }}</td>
                                        <td>
                                            <strong>{{ optional($adulto->persona)->nombres }}</strong>
                                            {{ optional($adulto->persona)->primer_apellido }}
                                            {{ optional($adulto->persona)->segundo_apellido }}
                                        </td>
                                        <td>{{ optional(optional($adulto->persona)->fecha_nacimiento)->format('d/m/Y') ?? 'N/A' }}</td>
                                        <td>{{ optional($adulto->persona)->telefono ?? 'N/A' }}</td>
                                        <td>
                                            @php
                                                // Verificar si hay fichas de kinesiología para este adulto
                                                // Asumiendo que $adulto->kinesiologias está precargado en el controlador indexKine
                                                $hasKinesiologiaFicha = $adulto->kinesiologias->isNotEmpty();
                                                $latestKinesiologia = $adulto->latestKinesiologia; // Obtener la última ficha
                                            @endphp
                                            <div class="btn-group" role="group">
                                                {{-- Botón para registrar una NUEVA FICHA DE KINESIOLOGÍA para este adulto mayor --}}
                                                <a href="{{ route('responsable.kinesiologia.fisiokine.createKine', ['id_adulto' => $adulto->id_adulto]) }}"
                                                    class="btn btn-sm btn-success"
                                                    data-bs-toggle="tooltip"
                                                    title="Registrar Nueva Ficha de Kinesiología">
                                                    <i class="fe fe-file-plus"></i>
                                                </a>

                                                {{-- Botón para EDITAR la ÚLTIMA FICHA DE KINESIOLOGÍA de este adulto mayor --}}
                                                <a href="{{ $hasKinesiologiaFicha ? route('responsable.kinesiologia.fisiokine.editKine', ['cod_kine' => $latestKinesiologia->cod_kine]) : 'javascript:void(0)' }}"
                                                    class="btn btn-sm {{ !$hasKinesiologiaFicha ? 'btn-light text-muted disabled' : 'btn-primary' }}"
                                                    data-bs-toggle="tooltip"
                                                    title="{{ !$hasKinesiologiaFicha ? 'No hay ficha de kinesiología para editar.' : 'Editar Última Ficha de Kinesiología' }}"
                                                    aria-disabled="{{ !$hasKinesiologiaFicha ? 'true' : 'false' }}">
                                                    <i class="fe fe-edit"></i>
                                                </a>

                                                {{-- Botón para VER el HISTORIAL de Fichas de Kinesiología del Adulto Mayor --}}
                                                <a href="{{ $hasKinesiologiaFicha ? route('responsable.kinesiologia.fisiokine.showKine', ['id_adulto' => $adulto->id_adulto]) : 'javascript:void(0)' }}"
                                                    class="btn btn-sm {{ !$hasKinesiologiaFicha ? 'btn-light text-muted disabled' : 'btn-info' }}"
                                                    data-bs-toggle="tooltip"
                                                    title="{{ !$hasKinesiologiaFicha ? 'No hay historial de fichas de kinesiología para ver.' : 'Ver Historial de Fichas de Kinesiología' }}"
                                                    aria-disabled="{{ !$hasKinesiologiaFicha ? 'true' : 'false' }}">
                                                    <i class="fe fe-list"></i> {{-- Icono de lista para historial --}}
                                                </a>

                                                {{-- Botón para ELIMINAR la ÚLTIMA FICHA DE KINESIOLOGÍA del Adulto Mayor (con SweetAlert2) --}}
                                                @if($hasKinesiologiaFicha)
                                                    <form action="{{ route('responsable.kinesiologia.fisiokine.destroyKine', ['cod_kine' => $latestKinesiologia->cod_kine]) }}"
                                                        method="POST"
                                                        class="d-inline form-delete-kine"> {{-- Clase para identificar el formulario para SweetAlert2 --}}
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                                class="btn btn-sm btn-danger"
                                                                data-bs-toggle="tooltip"
                                                                title="Eliminar Última Ficha de Kinesiología">
                                                            <i class="fe fe-trash-2"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <button class="btn btn-sm btn-light text-muted" disabled
                                                            data-bs-toggle="tooltip"
                                                            title="No hay ficha de kinesiología para eliminar.">
                                                        <i class="fe fe-trash-2"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">
                                            <i class="fe fe-inbox"></i>
                                            <br>
                                            No se encontraron adultos mayores.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
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

{{-- El modal de confirmación personalizado se elimina ya que SweetAlert2 lo reemplaza --}}

@push('scripts')
{{-- Cargamos SweetAlert2 JS --}}
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

        // Confirmación para eliminar ficha de kinesiología
        document.querySelectorAll('.form-delete-kine').forEach(form => {
            form.addEventListener('submit', function(event) {
                event.preventDefault(); // Prevenir el envío inmediato del formulario
                
                Swal.fire({
                    title: '¿Está seguro?',
                    text: "Se eliminará la última ficha de kinesiología. ¡Esta acción no se puede deshacer!",
                    icon: 'warning', // Cambiado a warning
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