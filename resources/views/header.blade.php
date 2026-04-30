<!doctype html>
<html lang="es" dir="ltr">

<head>
    <meta charset="UTF-8">
    <meta name='viewport' content='width=device-width, initial-scale=1.0, user-scalable=0'>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="Centro Hospitalario del Adulto Mayor">
    <meta name="author" content="Helmer Fellman Mendoza Jurado">
    <meta name="keywords" content="admin, dashboard, bootstrap, laravel, panel de control, centro de salud">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- FAVICON -->
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/images/brand/alcaldiaicon.png') }}">

    <!-- TITULO -->
    <title>Centro Hospitalario del Adulto Mayor</title>

    <!-- BOOTSTRAP CSS -->
    <link id="style" href="{{ asset('assets/plugins/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">

    <!-- ESTILOS CSS -->
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/plugins.css') }}" rel="stylesheet">

    <!-- ICONOS CSS -->
    <link href="{{ asset('assets/css/icons.css') }}" rel="stylesheet">
    
    <!-- DataTables -->
    <link href="{{ asset('assets/plugins/DataTables/datatables.min.css') }}" rel="stylesheet">
    
    <!-- SWITCHER CSS -->
    <link href="{{ asset('assets/switcher/css/switcher.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/switcher/demo.css') }}" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body class="app sidebar-mini ltr light-mode">

    <!-- GLOBAL-LOADER -->
    <div id="global-loader">
        <img src="{{ asset('assets/images/loader.svg') }}" class="loader-img" alt="Loader">
    </div>
    <!-- /GLOBAL-LOADER -->

    <!-- PAGE -->
    <div class="page">
        <div class="page-main">

            @php
                $user = Auth::user();
                // Obtener el rol del usuario
                $rol = strtolower(optional($user->rol)->nombre_rol ?? 'default');
                
                // CORRECCIÓN IMPORTANTE: Obtener la especialidad exactamente como está en la BD
                $especialidad = optional($user->persona)->area_especialidad ?? '';
                
                // DEBUG: Mostrar valores para verificar
                // dd("Rol: $rol, Especialidad: $especialidad");
                
                $dashboardRoute = route('login'); // Fallback
                
                if (in_array($rol, ['admin', 'legal', 'responsable'])) {
                    $dashboardRouteName = $rol . '.dashboard';
                    if (Route::has($dashboardRouteName)) {
                        $dashboardRoute = route($dashboardRouteName);
                    }
                }
            @endphp

            <!-- app-Header -->
            <div class="app-header header sticky">
                <div class="container-fluid main-container">
                    <div class="d-flex">
                        <a aria-label="Hide Sidebar" class="app-sidebar__toggle" data-bs-toggle="sidebar" href="javascript:void(0)"></a>
                        
                        <a class="logo-horizontal" href="{{ $dashboardRoute }}">
                            <img src="{{ asset('assets/images/brand/alcaldiaicon.png') }}" class="header-brand-img light-logo" alt="logo">
                            <img src="{{ asset('assets/images/brand/logo-alcaldia.png') }}" class="header-brand-img light-logo1" alt="logo">
                        </a>

                        <div class="d-flex order-lg-2 ms-auto header-right-icons">
                            <button class="navbar-toggler navresponsive-toggler d-lg-none ms-auto" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent-4" aria-controls="navbarSupportedContent-4" aria-expanded="false" aria-label="Toggle navigation">
                                <span class="navbar-toggler-icon fe fe-more-vertical"></span>
                            </button>
                            <div class="navbar navbar-collapse responsive-navbar p-0">
                                <div class="collapse navbar-collapse" id="navbarSupportedContent-4">
                                    <div class="d-flex order-lg-2">
                                        <div class="d-flex">
                                            <a class="nav-link icon theme-layout nav-link-bg layout-setting">
                                                <span class="dark-layout"><i class="fe fe-moon"></i></span>
                                                <span class="light-layout"><i class="fe fe-sun"></i></span>
                                            </a>
                                        </div>
                                        <div class="dropdown d-flex">
                                            <a class="nav-link icon full-screen-link nav-link-bg">
                                                <i class="fe fe-minimize fullscreen-button"></i>
                                            </a>
                                        </div>
                                        <div class="dropdown d-flex profile-1">
                                            <a href="javascript:void(0)" data-bs-toggle="dropdown" class="nav-link leading-none d-flex" aria-expanded="false">
                                                <img src="{{ asset('assets/images/users/userdefault.svg') }}" alt="profile-user" class="avatar profile-user brround cover-image">
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                                                <div class="drop-heading">
                                                    <div class="text-center">
                                                        <h5 class="text-dark mb-0 fs-14 fw-semibold">{{ $user->full_name ?? $user->name }}</h5>
                                                        <small class="text-muted">
                                                            {{ ucfirst(str_replace('_', ' ', $rol)) }}
                                                            @if($rol == 'responsable' && $especialidad)
                                                                ({{ $especialidad }})
                                                            @endif
                                                        </small>
                                                    </div>
                                                </div>
                                                <div class="dropdown-divider m-0"></div>
                                                <a class="dropdown-item" href="{{ route('profile.show') }}">
                                                    <i class="dropdown-icon fe fe-user"></i> Perfil
                                                </a>
                                                <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                                    <i class="dropdown-icon fe fe-alert-circle"></i> Cerrar Sesión
                                                </a>
                                                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                                    @csrf
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /app-Header -->

            <!--APP-SIDEBAR-->
            <div class="sticky">
                <div class="app-sidebar__overlay" data-bs-toggle="sidebar"></div>
                <div class="app-sidebar">
                    <div class="side-header">
                        <a class="header-brand1" href="{{ $dashboardRoute }}">
                            <img src="{{ asset('assets/images/brand/logo-alcaldia.png') }}" class="header-brand-img desktop-logo" alt="logo">
                            <img src="{{ asset('assets/images/brand/alcaldiaicon.png') }}" class="header-brand-img toggle-logo" alt="logo">
                            <img src="{{ asset('assets/images/brand/alcaldiaicon.png') }}" class="header-brand-img light-logo" alt="logo">
                            <img src="{{ asset('assets/images/brand/logo-alcaldia.png') }}" class="header-brand-img light-logo1" alt="logo">
                        </a>
                    </div>
                    <div class="main-sidemenu">
                        <div class="slide-left disabled" id="slide-left"><svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191" width="24" height="24" viewBox="0 0 24 24"><path d="M13.293 6.293 7.586 12l5.707 5.707 1.414-1.414L10.414 12l4.293-4.293z" /></svg></div>
                        
                        <ul class="side-menu">
                            <li class="sub-category"><h3>MENÚ PRINCIPAL</h3></li>
                            <li class="slide">
                                <a class="side-menu__item" href="{{ $dashboardRoute }}">
                                    <i class="side-menu__icon fe fe-home"></i><span class="side-menu__label">Inicio</span>
                                </a>
                            </li>
                            
                            {{-- ===================== MENÚS POR ROL ===================== --}}

                            {{-- MENÚ EXCLUSIVO PARA ADMIN --}}
                            @if($rol == 'admin')
                                <li class="sub-category"><h3>Administración del Sistema</h3></li>
                                <li class="slide">
                                    <a class="side-menu__item" data-bs-toggle="slide" href="javascript:void(0);">
                                        <i class="side-menu__icon fe fe-user-plus"></i><span class="side-menu__label">Registrar Personal</span><i class="angle fe fe-chevron-right"></i>
                                    </a>
                                    <ul class="slide-menu">
                                        <li><a href="{{ route('admin.registrar-usuario-legal') }}" class="slide-item">Personal Legal</a></li>
                                        <li><a href="{{ route('admin.registrar-responsable-salud') }}" class="slide-item">Responsable de Salud</a></li>
                                    </ul>
                                </li>
                                <li class="slide">
                                    <a class="side-menu__item" href="{{ route('admin.gestionar-usuarios.index') }}"><i class="side-menu__icon fe fe-users"></i><span class="side-menu__label">Gestionar Usuarios</span></a>
                                </li>
                                <li class="slide">
                                    <a class="side-menu__item" href="{{ route('admin.gestionar-roles.index') }}"><i class="side-menu__icon fe fe-shield"></i><span class="side-menu__label">Gestionar Roles</span></a>
                                </li>
                                
                                {{-- El admin también ve todo lo demás --}}
                                <li class="sub-category"><h3>Gestión de Pacientes</h3></li>
                                <li class="slide">
                                    <a class="side-menu__item" data-bs-toggle="slide" href="javascript:void(0);">
                                        <i class="side-menu__icon fe fe-user-check"></i><span class="side-menu__label">Adulto Mayor</span><i class="angle fe fe-chevron-right"></i>
                                    </a>
                                    <ul class="slide-menu">
                                        <li><a href="{{ route('gestionar-adultomayor.index') }}" class="slide-item">Gestionar Pacientes</a></li>
                                        <li><a href="{{ route('gestionar-adultomayor.create') }}" class="slide-item">Registrar Paciente</a></li>
                                    </ul>
                                </li>
                                 
                                <li class="sub-category"><h3>Módulo Protección</h3></li>
                                <li class="slide">
                                    <a class="side-menu__item" href="{{ route('admin.caso.index') }}"><i class="side-menu__icon fe fe-file-plus"></i><span class="side-menu__label">Registrar Caso</span></a>
                                </li>
                                <li class="slide">
                                    <a class="side-menu__item" href="{{ route('admin.reportes_proteccion.index') }}"><i class="side-menu__icon fe fe-file-text"></i><span class="side-menu__label">Reportes Protección</span></a>
                                </li>
                                
                                <li class="sub-category"><h3>Módulo Médico</h3></li>
                                <li class="slide">
                                    <a class="side-menu__item" data-bs-toggle="slide" href="javascript:void(0);"><i class="side-menu__icon fe fe-activity"></i><span class="side-menu__label">Servicios</span><i class="angle fe fe-chevron-right"></i></a>
                                    <ul class="slide-menu">
                                        <li><a href="{{ route('responsable.servicios.index') }}" class="slide-item">Ver Servicios</a></li>
                                    </ul>
                                </li>
                                <li class="slide">
                                    <a class="side-menu__item" data-bs-toggle="slide" href="javascript:void(0);"><i class="side-menu__icon fe fe-file-text"></i><span class="side-menu__label">Historias Clínicas</span><i class="angle fe fe-chevron-right"></i></a>
                                    <ul class="slide-menu">
                                        <li><a href="{{ route('responsable.historias-clinicas.index') }}" class="slide-item">Ver Historias</a></li>
                                    </ul>
                                </li>
                                <li class="slide">
                                    <a class="side-menu__item" data-bs-toggle="slide" href="javascript:void(0);"><i class="side-menu__icon fe fe-heart"></i><span class="side-menu__label">Enfermería</span><i class="angle fe fe-chevron-right"></i></a>
                                    <ul class="slide-menu">
                                        <li><a href="{{ route('responsable.enfermeria.index') }}" class="slide-item">Gestionar Enfermería</a></li>
                                        <li><a href="{{ route('responsable.enfermeria.reportes') }}" class="slide-item">Reportes Enfermería</a></li>
                                    </ul>
                                </li>
                                <li class="slide">
                                    <a class="side-menu__item" data-bs-toggle="slide" href="javascript:void(0);"><i class="side-menu__icon fe fe-activity"></i><span class="side-menu__label">Fisioterapia</span><i class="angle fe fe-chevron-right"></i></a>
                                    <ul class="slide-menu">
                                        <li><a href="{{ route('responsable.fisioterapia.atencion') }}" class="slide-item">Atención Fisioterapia</a></li>
                                        <li><a href="{{ route('responsable.fisioterapia.reportes') }}" class="slide-item">Reportes Fisioterapia</a></li>
                                    </ul>
                                </li>
                                <li class="slide">
                                    <a class="side-menu__item" data-bs-toggle="slide" href="javascript:void(0);"><i class="side-menu__icon fe fe-wind"></i><span class="side-menu__label">Kinesiología</span><i class="angle fe fe-chevron-right"></i></a>
                                    <ul class="slide-menu">
                                        <li><a href="{{ route('responsable.kinesiologia.atencion') }}" class="slide-item">Atención Kinesiología</a></li>
                                        <li><a href="{{ route('responsable.kinesiologia.reportes') }}" class="slide-item">Reportes Kinesiología</a></li>
                                    </ul>
                                </li>
                            @endif

                            {{-- MENÚ PARA LEGAL --}}
                            @if($rol == 'legal')
                                <li class="sub-category"><h3>Gestión de Pacientes</h3></li>
                                <li class="slide">
                                    <a class="side-menu__item" data-bs-toggle="slide" href="javascript:void(0);">
                                        <i class="side-menu__icon fe fe-user-check"></i><span class="side-menu__label">Adulto Mayor</span><i class="angle fe fe-chevron-right"></i>
                                    </a>
                                    <ul class="slide-menu">
                                        <li><a href="{{ route('gestionar-adultomayor.index') }}" class="slide-item">Gestionar Pacientes</a></li>
                                        <li><a href="{{ route('gestionar-adultomayor.create') }}" class="slide-item">Registrar Paciente</a></li>
                                    </ul>
                                </li>
                                
                                <li class="sub-category"><h3>Módulo Protección</h3></li>
                                <li class="slide">
                                    <a class="side-menu__item" href="{{ route('admin.caso.index') }}"><i class="side-menu__icon fe fe-file-plus"></i><span class="side-menu__label">Registrar Caso</span></a>
                                </li>
                                <li class="slide">
                                    <a class="side-menu__item" href="{{ route('admin.reportes_proteccion.index') }}"><i class="side-menu__icon fe fe-file-text"></i><span class="side-menu__label">Reportes Protección</span></a>
                                </li>
                            @endif

                            {{-- MENÚ PARA RESPONSABLE --}}
                            @if($rol == 'responsable')
                                <li class="sub-category"><h3>Módulo Médico</h3></li>
                                
                                {{-- CORRECCIÓN: Usar comparación exacta con la especialidad de la BD --}}
                                @if($especialidad == 'Enfermeria')
                                    {{-- Menú para Responsable de Enfermería --}}
                                    <li class="slide">
                                        <a class="side-menu__item" data-bs-toggle="slide" href="javascript:void(0);"><i class="side-menu__icon fe fe-activity"></i><span class="side-menu__label">Servicios</span><i class="angle fe fe-chevron-right"></i></a>
                                        <ul class="slide-menu">
                                            <li><a href="{{ route('responsable.servicios.index') }}" class="slide-item">Ver Servicios</a></li>
                                        </ul>
                                    </li>
                                    <li class="slide">
                                        <a class="side-menu__item" data-bs-toggle="slide" href="javascript:void(0);"><i class="side-menu__icon fe fe-file-text"></i><span class="side-menu__label">Historias Clínicas</span><i class="angle fe fe-chevron-right"></i></a>
                                        <ul class="slide-menu">
                                            <li><a href="{{ route('responsable.historias-clinicas.index') }}" class="slide-item">Ver Historias</a></li>
                                        </ul>
                                    </li>
                                    <li class="slide">
                                        <a class="side-menu__item" data-bs-toggle="slide" href="javascript:void(0);"><i class="side-menu__icon fe fe-heart"></i><span class="side-menu__label">Enfermería</span><i class="angle fe fe-chevron-right"></i></a>
                                        <ul class="slide-menu">
                                            <li><a href="{{ route('responsable.enfermeria.index') }}" class="slide-item">Gestionar Enfermería</a></li>
                                            <li><a href="{{ route('responsable.enfermeria.reportes') }}" class="slide-item">Reportes Enfermería</a></li>
                                        </ul>
                                    </li>
                                @endif

                                {{-- CORRECCIÓN: Usar comparación exacta con la especialidad de la BD --}}
                                @if($especialidad == 'Fisioterapia-Kinesiologia')
                                    {{-- Menú para Responsable de Fisioterapia-Kinesiología --}}
                                    <li class="slide">
                                        <a class="side-menu__item" data-bs-toggle="slide" href="javascript:void(0);"><i class="side-menu__icon fe fe-activity"></i><span class="side-menu__label">Fisioterapia</span><i class="angle fe fe-chevron-right"></i></a>
                                        <ul class="slide-menu">
                                            <li><a href="{{ route('responsable.fisioterapia.atencion') }}" class="slide-item">Atención Fisioterapia</a></li>
                                            <li><a href="{{ route('responsable.fisioterapia.reportes') }}" class="slide-item">Reportes Fisioterapia</a></li>
                                        </ul>
                                    </li>
                                    <li class="slide">
                                        <a class="side-menu__item" data-bs-toggle="slide" href="javascript:void(0);"><i class="side-menu__icon fe fe-wind"></i><span class="side-menu__label">Kinesiología</span><i class="angle fe fe-chevron-right"></i></a>
                                        <ul class="slide-menu">
                                            <li><a href="{{ route('responsable.kinesiologia.atencion') }}" class="slide-item">Atención Kinesiología</a></li>
                                            <li><a href="{{ route('responsable.kinesiologia.reportes') }}" class="slide-item">Reportes Kinesiología</a></li>
                                        </ul>
                                    </li>
                                @endif
                            @endif

                        </ul>

                        <div class="slide-right" id="slide-right"><svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191" width="24" height="24" viewBox="0 0 24 24"><path d="M10.707 17.707 16.414 12l-5.707-5.707-1.414 1.414L13.586 12l-4.293 4.293z" /></svg></div>
                    </div>
                </div>
            </div>
            <!--/APP-SIDEBAR-->
        </div>