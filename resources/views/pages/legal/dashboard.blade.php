@extends('layouts.main')

@section('content')
    <!-- PAGE-HEADER -->
    <div class="page-header">
        <h1 class="page-title">Dashboard Legal</h1>
        <div>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
            </ol>
        </div>
    </div>
    <!-- PAGE-HEADER END -->

    <!-- SALUDO Y RELOJ -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0">¡Bienvenido de nuevo, {{ Auth::user()->name }}!</h4>
                            <p class="text-muted mb-0">Aquí tienes un resumen de la actividad reciente.</p>
                        </div>
                        <div class="text-end">
                            <h4 id="fecha-actual" class="mb-0"></h4>
                            <h3 id="hora-actual" class="mb-0"></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END SALUDO Y RELOJ -->

    <!-- TARJETAS DE ESTADÍSTICAS -->
    <div class="row">
        <div class="col-sm-6 col-md-6 col-lg-6 col-xl-4">
            <div class="card bg-info img-card box-info-shadow">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="text-white">
                            <h2 class="mb-0 number-font">{{ $totalPacientes ?? 0 }}</h2>
                            <p class="text-white mb-0">Total Pacientes Registrados</p>
                        </div>
                        <div class="ms-auto"> <i class="fa fa-users text-white fs-30 me-2 mt-2"></i> </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-6 col-lg-6 col-xl-4">
            <div class="card bg-success img-card box-success-shadow">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="text-white">
                            <h2 class="mb-0 number-font">{{ $casosProteccion ?? 0 }}</h2>
                            <p class="text-white mb-0">Casos de Protección</p>
                        </div>
                        <div class="ms-auto"> <i class="fe fe-shield text-white fs-30 me-2 mt-2"></i> </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-6 col-lg-6 col-xl-4">
            <div class="card bg-warning img-card box-warning-shadow">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="text-white">
                            <h2 class="mb-0 number-font">{{ $fichasOrientacion ?? 0 }}</h2>
                            <p class="text-white mb-0">Fichas de Orientación</p>
                        </div>
                        <div class="ms-auto"> <i class="fe fe-file-text text-white fs-30 me-2 mt-2"></i> </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END TARJETAS DE ESTADÍSTICAS -->

    <!-- ACCESOS RÁPIDOS -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Accesos Rápidos</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-4 col-md-6 mb-4">
                            <a href="{{ route('gestionar-adultomayor.create') }}" class="card text-center shadow-sm h-100 card-hover">
                                <div class="card-body">
                                    <div class="feature-icon-1 bg-primary-transparent mb-4">
                                        <i class="fe fe-user-plus"></i>
                                    </div>
                                    <h5 class="card-title">Registrar Nuevo Paciente</h5>
                                    <p class="card-text text-muted">Añadir un nuevo adulto mayor al sistema.</p>
                                </div>
                            </a>
                        </div>
                        <div class="col-lg-4 col-md-6 mb-4">
                            <a href="{{ route('legal.caso.index') }}" class="card text-center shadow-sm h-100 card-hover">
                                <div class="card-body">
                                    <div class="feature-icon-1 bg-success-transparent mb-4">
                                        <i class="fe fe-file-plus"></i>
                                    </div>
                                    <h5 class="card-title">Registrar Caso de Protección</h5>
                                    <p class="card-text text-muted">Iniciar un nuevo registro de caso de protección.</p>
                                </div>
                            </a>
                        </div>
                         <div class="col-lg-4 col-md-6 mb-4">
                            <a href="{{ route('legal.orientacion.index') }}" class="card text-center shadow-sm h-100 card-hover">
                                <div class="card-body">
                                    <div class="feature-icon-1 bg-warning-transparent mb-4">
                                        <i class="fe fe-clipboard"></i>
                                    </div>
                                    <h5 class="card-title">Registrar Ficha de Orientación</h5>
                                    <p class="card-text text-muted">Crear una nueva ficha del módulo de orientación.</p>
                                </div>
                            </a>
                        </div>
                        <div class="col-lg-4 col-md-6 mb-4">
                            <a href="{{ route('gestionar-adultomayor.index') }}" class="card text-center shadow-sm h-100 card-hover">
                                <div class="card-body">
                                    <div class="feature-icon-1 bg-info-transparent mb-4">
                                        <i class="fe fe-users"></i>
                                    </div>
                                    <h5 class="card-title">Ver Adultos Mayores</h5>
                                    <p class="card-text text-muted">Consultar y gestionar la lista de pacientes.</p>
                                </div>
                            </a>
                        </div>
                        <div class="col-lg-4 col-md-6 mb-4">
                            <a href="{{ route('legal.reportes_proteccion.index') }}" class="card text-center shadow-sm h-100 card-hover">
                                <div class="card-body">
                                    <div class="feature-icon-1 bg-danger-transparent mb-4">
                                        <i class="fe fe-file-text"></i>
                                    </div>
                                    <h5 class="card-title">Reportes de Protección</h5>
                                    <p class="card-text text-muted">Generar y visualizar reportes de protección.</p>
                                </div>
                            </a>
                        </div>
                        <div class="col-lg-4 col-md-6 mb-4">
                            <a href="{{ route('legal.reportes_orientacion.index') }}" class="card text-center shadow-sm h-100 card-hover">
                                <div class="card-body">
                                    <div class="feature-icon-1 bg-secondary-transparent mb-4">
                                        <i class="fe fe fe-shield"></i>
                                    </div>
                                    <h5 class="card-title">Reportes de Orientación</h5>
                                    <p class="card-text text-muted">Generar y visualizar reportes de orientación.</p>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END ACCESOS RÁPIDOS -->
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- Función para actualizar el reloj ---
        function actualizarReloj() {
            const ahora = new Date();

            // Formatear la fecha
            const dias = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
            const meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
            const diaSemana = dias[ahora.getDay()];
            const dia = ahora.getDate();
            const mes = meses[ahora.getMonth()];
            const anio = ahora.getFullYear();
            const fechaFormateada = `${diaSemana}, ${dia} de ${mes} de ${anio}`;

            // Formatear la hora
            let horas = ahora.getHours();
            let minutos = ahora.getMinutes();
            let segundos = ahora.getSeconds();
            const ampm = horas >= 12 ? 'PM' : 'AM';
            horas = horas % 12;
            horas = horas ? horas : 12; // La hora '0' debe ser '12'
            minutos = minutos < 10 ? '0' + minutos : minutos;
            segundos = segundos < 10 ? '0' + segundos : segundos;
            const horaFormateada = `${horas}:${minutos}:${segundos} ${ampm}`;
            
            // Actualizar el DOM
            document.getElementById('fecha-actual').textContent = fechaFormateada;
            document.getElementById('hora-actual').textContent = horaFormateada;
        }

        // Actualizar el reloj cada segundo
        setInterval(actualizarReloj, 1000);
        
        // Llamar a la función una vez al cargar la página para evitar el retraso de 1 segundo
        actualizarReloj();
    });
</script>

<style>
    .card-hover {
        transition: transform .2s ease-in-out, box-shadow .2s ease-in-out;
    }
    .card-hover:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,.12), 0 4px 8px rgba(0,0,0,.06) !important;
    }
    .feature-icon-1 {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 60px;
        height: 60px;
        border-radius: 50%;
    }
    .feature-icon-1 i {
        font-size: 28px;
    }
</style>
@endpush
