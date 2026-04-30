@extends('layouts.main')
@php
    // --- LÓGICA DE DATOS INTEGRADA EN LA VISTA ---
    // Esto asegura que las variables siempre estén disponibles,
    // independientemente de cómo se llame a la vista.

    use Illuminate\Support\Facades\Auth;
    use App\Models\AdultoMayor;
    use App\Models\Enfermeria;
    use App\Models\Fisioterapia;
    use App\Models\Kinesiologia;
    use App\Models\HistoriaClinica;

    $user = Auth::user();

    // 1. Se inicializan todas las variables con valores por defecto.
    $data = [
        'totalPacientes'         => AdultoMayor::count(),
        'totalHistoriasClinicas' => HistoriaClinica::count(),
        'atencionesEnfermeria'   => 0,
        'fichasFisioKine'        => 0,
        'shortcuts'              => [],
        'chartData'              => ['labels' => [], 'data' => []],
    ];

    // 2. Se llenan los datos y atajos según la especialidad del usuario.
    if ($user && $user->rol && $user->rol->nombre_rol === 'responsable') {
        if ($user->area_especialidad == 'Enfermeria') {
            $data['atencionesEnfermeria'] = Enfermeria::count();
            $data['shortcuts'] = [
                [
                    'route' => 'responsable.enfermeria.medico.historia_clinica.index',
                    'icon' => 'fa fa-book', 'title' => 'Historias Clínicas',
                    'text' => 'Gestionar las historias clínicas de los pacientes.', 'color' => 'primary'
                ],
                [
                    'route' => 'responsable.enfermeria.enfermeria.index',
                    'icon' => 'fe fe-heart', 'title' => 'Atención de Enfermería',
                    'text' => 'Registrar y consultar atenciones de enfermería.', 'color' => 'success'
                ],
                [
                    'route' => 'responsable.enfermeria.reportes_enfermeria.index',
                    'icon' => 'fe fe-file-text', 'title' => 'Reportes de Enfermería',
                    'text' => 'Generar y visualizar reportes del área.', 'color' => 'info'
                ],
            ];
            $data['chartData']['labels'] = ['Historias Clínicas', 'Atenciones Enfermería'];
            $data['chartData']['data'] = [$data['totalHistoriasClinicas'], $data['atencionesEnfermeria']];
        }
        elseif ($user->area_especialidad == 'Fisioterapia-Kinesiologia') {
            $fichasFisio = Fisioterapia::count();
            $fichasKine = Kinesiologia::count();
            $data['fichasFisioKine'] = $fichasFisio + $fichasKine;
            $data['shortcuts'] = [
                [
                    'route' => 'responsable.fisioterapia.fisiokine.indexFisio',
                    'icon' => 'fe fe-activity', 'title' => 'Registrar Ficha Fisio/Kine',
                    'text' => 'Crear nuevas fichas de Fisioterapia y Kinesiología.', 'color' => 'warning'
                ],
                [
                    'route' => 'responsable.fisioterapia.reportefisio.index',
                    'icon' => 'fe fe-file-text', 'title' => 'Reportes Fisio-Kine',
                    'text' => 'Generar y visualizar reportes del área.', 'color' => 'danger'
                ],
            ];
            $data['chartData']['labels'] = ['Fichas Fisioterapia', 'Fichas Kinesiología'];
            $data['chartData']['data'] = [$fichasFisio, $fichasKine];
        }
    }
    // Se extraen las variables del array $data para que estén disponibles en la vista.
    extract($data);
@endphp



{{-- Inyectar Chart.js y FontAwesome en la sección de cabecera --}}
@push('styles')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endpush

@section('content')
    <!-- PAGE-HEADER -->
    <div class="page-header">
        <h1 class="page-title">Dashboard Responsable</h1>
        <div>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
            </ol>
        </div>
    </div>
    <!-- PAGE-HEADER END -->

    <!-- SALUDO, PERFIL, RELOJ Y CLIMA -->
    <div class="row">
        {{-- Columna del Perfil de Usuario --}}
        <div class="col-xl-7 col-lg-12">
            <div class="card">
                <div class="card-header">
                     <h3 class="card-title">Información del Usuario</h3>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            {{-- CORRECCIÓN: Se usa asset() para la ruta de la imagen --}}
                            <span class="avatar avatar-xxl rounded-circle" style="background-image: url({{ asset('assets/images/users/userdefault.svg') }})"></span>
                        </div>
                        <div>
                            <h4 class="mb-0">¡Bienvenido de nuevo, {{ Auth::user()->name }}!</h4>
                             <div class="d-flex">
                                 <span class="badge bg-primary">CI: {{ Auth::user()->ci }}</span>
                                 <span class="badge bg-secondary">Rol: {{ Auth::user()->rol ? Str::ucfirst(Auth::user()->rol->nombre_rol) : 'No definido' }}</span>
                             </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Columna del Reloj y Clima --}}
        <div class="col-xl-5 col-lg-12">
            <div class="card">
                 <div class="card-header">
                    <h3 class="card-title">Hora y Clima Local</h3>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-around align-items-center" style="min-height: 80px;">
                        <div id="weather-widget" class="text-center">
                            <i class="fas fa-spinner fa-spin fs-2"></i>
                            <p class="mb-0 mt-1 text-muted small">Cargando clima...</p>
                        </div>
                        <div class="vr mx-3"></div>
                        <div class="text-center">
                            <h5 id="fecha-actual" class="mb-1"></h5>
                            <h4 id="hora-actual" class="mb-0 text-primary fw-bold"></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END SALUDO, PERFIL, RELOJ Y CLIMA -->

    <div class="row">
        

        <!-- Columna de Estadísticas -->
        <div class="col-lg-5 col-md-12">
            <div class="card">
                 <div class="card-header">
                    <h3 class="card-title">Estadísticas Clave</h3>
                </div>
                <div class="card-body">
                      <div class="d-flex align-items-center mb-4">
                           <div class="me-3">
                               <span class="avatar avatar-md rounded-circle bg-primary-transparent"><i class="fa fa-book text-primary"></i></span>
                           </div>
                           <div class="flex-grow-1">
                               <h6 class="mb-1">Historias Clínicas</h6>
                               <h2 class="mb-0 number-font">{{ $totalHistoriasClinicas ?? 0 }}</h2>
                           </div>
                       </div>
                       
                      {{-- CAMBIO: Se eliminó el @if para que siempre sea visible --}}
                      <div class="d-flex align-items-center mb-4">
                           <div class="me-3">
                               <span class="avatar avatar-md rounded-circle bg-success-transparent"><i class="fe fe-heart text-success"></i></span>
                           </div>
                           <div class="flex-grow-1">
                               <h6 class="mb-1">Atenciones de Enfermería</h6>
                               <h2 class="mb-0 number-font">{{ $atencionesEnfermeria ?? 0 }}</h2>
                           </div>
                       </div>
                       
                       {{-- CAMBIO: Se eliminó el @if para que siempre sea visible --}}
                       <div class="d-flex align-items-center mb-4">
                           <div class="me-3">
                               <span class="avatar avatar-md rounded-circle bg-warning-transparent"><i class="fe fe-activity text-warning"></i></span>
                           </div>
                           <div class="flex-grow-1">
                               <h6 class="mb-1">Fichas Fisio/Kine</h6>
                               <h2 class="mb-0 number-font">{{ $fichasFisioKine ?? 0 }}</h2>
                           </div>
                       </div>
                       
                      <div class="d-flex align-items-center">
                           <div class="me-3">
                               <span class="avatar avatar-md rounded-circle bg-info-transparent"><i class="fa fa-users text-info"></i></span>
                           </div>
                           <div class="flex-grow-1">
                               <h6 class="mb-1">Total Pacientes</h6>
                               <h2 class="mb-0 number-font">{{ $totalPacientes ?? 0 }}</h2>
                           </div>
                       </div>
                </div>
            </div>
        </div>
    </div>
@endsection

{{-- Inyectar scripts adicionales --}}

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // =======================================================
        // RELOJ EN TIEMPO REAL
        // =======================================================
        function actualizarReloj() {
            const ahora = new Date();
            const dias = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
            const meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
            const fechaFormateada = `${dias[ahora.getDay()]}, ${ahora.getDate()} de ${meses[ahora.getMonth()]}`;
            
            let horas = ahora.getHours();
            let minutos = ahora.getMinutes();
            let segundos = ahora.getSeconds();
            const ampm = horas >= 12 ? 'PM' : 'AM';
            horas = horas % 12;
            horas = horas ? horas : 12; 
            minutos = minutos < 10 ? '0' + minutos : minutos;
            segundos = segundos < 10 ? '0' + segundos : segundos;
            const horaFormateada = `${horas}:${minutos}:${segundos} ${ampm}`;
            
            document.getElementById('fecha-actual').textContent = fechaFormateada;
            document.getElementById('hora-actual').textContent = horaFormateada;
        }
        setInterval(actualizarReloj, 1000);
        actualizarReloj();

        // =======================================================
        // WIDGET DEL CLIMA
        // =======================================================
        function obtenerClima() {
            const lat = -21.5355; // Tarija, Bolivia
            const lon = -64.7296;
            const url = `https://api.open-meteo.com/v1/forecast?latitude=${lat}&longitude=${lon}&current_weather=true`;

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    if (data && data.current_weather) {
                        const clima = data.current_weather;
                        const temperatura = Math.round(clima.temperature);
                        const { icon, description } = getWeatherDetails(clima.weathercode);

                        document.getElementById('weather-widget').innerHTML = `
                            <i class="${icon} fs-2 text-primary"></i>
                            <h3 class="mb-0 d-inline-block ms-2">${temperatura}°C</h3>
                            <p class="mb-0 mt-1 text-muted">${description}</p>
                        `;
                    }
                })
                .catch(error => {
                     document.getElementById('weather-widget').innerHTML = `<i class="fas fa-exclamation-circle text-danger fs-2"></i><p class="mb-0 mt-1 text-muted small">Error clima</p>`;
                       console.error('Error fetching weather:', error);
                });
        }
        
        function getWeatherDetails(code) {
            if (code === 0) return { icon: "fas fa-sun", description: "Despejado" };
            if (code >= 1 && code <= 3) return { icon: "fas fa-cloud-sun", description: "Parcialmente Nublado" };
            if (code === 45 || code === 48) return { icon: "fas fa-smog", description: "Niebla" };
            if (code >= 51 && code <= 57) return { icon: "fas fa-cloud-rain", description: "Llovizna" };
            if (code >= 61 && code <= 67) return { icon: "fas fa-cloud-showers-heavy", description: "Lluvia" };
            if (code >= 80 && code <= 82) return { icon: "fas fa-cloud-showers-heavy", description: "Lluvia Fuerte" };
            if (code === 95 || code === 96 || code === 99) return { icon: "fas fa-bolt", description: "Tormenta" };
            return { icon: "fas fa-question-circle", description: "Desconocido" };
        }
        
        obtenerClima();
        setInterval(obtenerClima, 900000); // Actualizar cada 15 minutos

        // =======================================================
        // GRÁFICO DE CASOS
        // =======================================================
        const chartData = @json($chartData);
        const ctx = document.getElementById('caseChart');

        if (chartData.labels.length > 0 && ctx) {
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: chartData.labels,
                    datasets: [{
                        label: 'Número de Registros',
                        data: chartData.data,
                        backgroundColor: ['rgba(52, 152, 219, 0.6)', 'rgba(46, 204, 113, 0.6)'],
                        borderColor: ['rgba(52, 152, 219, 1)', 'rgba(46, 204, 113, 1)'],
                        borderWidth: 1,
                        borderRadius: 5,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: { y: { beginAtZero: true, ticks: { precision: 0 } } },
                    plugins: { legend: { display: false }, title: { display: false } }
                }
            });
        } else if (ctx) {
            ctx.parentElement.innerHTML = '<div class="alert alert-light text-center p-5"><i class="fe fe-info fs-24 d-block mb-2"></i>No hay datos de casos para mostrar en el gráfico.</div>';
        }
    });
</script>
<style>
    .vr { display: inline-block; align-self: stretch; width: 1px; min-height: 1em; background-color: currentColor; opacity: .25; }
</style>
@endpush
