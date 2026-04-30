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
    
    {{-- Hook para estilos adicionales específicos de cada página --}}
    @yield('styles')
    
    <style>
        /* Estilos adicionales para el botón de manual */
        .nav-link-manual {
            display: flex;
            align-items: center;
            color: #495057;
            transition: all 0.3s ease;
            padding: 0.5rem 1rem;
            border-radius: 4px;
        }
        
        .nav-link-manual:hover {
            background-color: rgba(0, 0, 0, 0.05);
            color: #9B2C2C;
            text-decoration: none;
        }
        
        .nav-link-manual .icon-wrapper {
            position: relative;
            margin-right: 8px;
        }
        
        .nav-link-manual .fe-book {
            font-size: 18px;
        }
        
        .nav-link-manual .badge-pdf {
            position: absolute;
            top: -8px;
            right: -8px;
            background-color: #9B2C2C;
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }
        
        .nav-link-manual .text {
            font-size: 14px;
            font-weight: 500;
        }
        
        /* Para pantallas pequeñas, ocultar el texto y mostrar solo el icono */
        @media (max-width: 991.98px) {
            .nav-link-manual .text {
                display: none;
            }
            
            .nav-link-manual .icon-wrapper {
                margin-right: 0;
            }
        }
    </style>
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
                // --- Lógica centralizada para obtener datos del usuario y definir rutas ---
                $user = Auth::user();
                // Usamos el operador de fusión de null para más seguridad y limpieza
                $rol = strtolower($user->rol->nombre_rol ?? 'default'); 
                $especialidad = strtolower($user->persona->area_especialidad ?? '');
                
                // --- Lógica para la ruta del dashboard ---
                $dashboardRoute = route('login'); // Ruta por defecto si algo falla
                
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
                                        
                                        <!-- Botón de Manual de Usuario -->
                                        <div class="dropdown d-flex">
                                            <a href="{{ asset('manual/MANUAL DE USUARIO CENTRO DEL ADULTO MAYOR.pdf') }}" 
                                               target="_blank"
                                               class="nav-link-manual"
                                               title="Manual de Usuario">
                                                <span class="icon-wrapper">
                                                    <i class="fe fe-book"></i>
                                                    <span class="badge-pdf">PDF</span>
                                                </span>
                                                <span class="text">Manual de Usuario</span>
                                            </a>
                                        </div>
                                        
                                        <div class="dropdown d-flex profile-1">
                                            <a href="javascript:void(0)" data-bs-toggle="dropdown" class="nav-link leading-none d-flex" aria-expanded="false">
                                                <img src="{{ asset('assets/images/users/userdefault.svg') }}" alt="profile-user" class="avatar profile-user brround cover-image">
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                                                <div class="drop-heading">
                                                    <div class="text-center">
                                                         {{-- Usamos la relación para obtener el nombre completo desde la persona --}}
                                                        <h5 class="text-dark mb-0 fs-14 fw-semibold">{{ optional($user->persona)->nombres . ' ' . optional($user->persona)->primer_apellido }}</h5>
                                                        <small class="text-muted">
                                                            {{ ucfirst(str_replace('_', ' ', $rol)) }}
                                                            @if($rol == 'responsable' && $especialidad)
                                                                {{-- Se muestra la especialidad con el formato correcto --}}
                                                                ({{ ucfirst(str_replace('-', ' ', $especialidad)) }})
                                                            @endif
                                                        </small>
                                                    </div>
                                                </div>
                                                <div class="dropdown-divider m-0"></div>
                                                <a class="dropdown-item" href="{{-- {{ route('profile.show') }} --}}">
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
                            
                            {{-- ===================== LÓGICA DE MENÚS CORREGIDA Y CENTRALIZADA ===================== --}}

                            {{-- MENÚ PARA ADMIN --}}
                            @if($rol == 'admin')
                                {{-- Asumiendo que tienes un parcial para el menú de admin como 'partials.menus.admin' --}}
                                @include('partials.menus.admin') 
                            @endif

                            {{-- MENÚ PARA LEGAL --}}
                            @if($rol == 'legal')
                                {{-- Asumiendo que tienes un parcial para el menú legal como 'partials.menus.legal' --}}
                                @include('partials.menus.legal')
                            @endif

                            {{-- MENÚ PARA RESPONSABLE DE SALUD --}}
                            @if($rol == 'responsable')
                                <li class="sub-category"><h3>Módulo Médico</h3></li>
                                
                                {{-- LÓGICA CORREGIDA: Compara la especialidad en minúsculas --}}
                                @if($especialidad == 'enfermeria')
                                    @include('partials.menus.responsable_enfermeria')

                                {{-- LÓGICA CORREGIDA: Compara con el valor exacto de la BBDD 'fisioterapia-kinesiologia' --}}
                                @elseif($especialidad == 'fisioterapia-kinesiologia')
                                    @include('partials.menus.responsable_fisioterapia')
                                @endif
                                
                            @endif

                        </ul>

                        <div class="slide-right" id="slide-right"><svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191" width="24" height="24" viewBox="0 0 24 24"><path d="M10.707 17.707 16.414 12l-5.707-5.707-1.414 1.414L13.586 12l-4.293 4.293z" /></svg></div>
                    </div>
                </div>
            </div>
            <!--/APP-SIDEBAR-->

            <!-- Contenido Principal -->
            <div class="main-content app-content mt-0">
                <div class="side-app">
                    <div class="main-container container-fluid">
                        @yield('content')
                    </div>
                </div>
            </div>
        </div>

        <!-- FOOTER -->
        <footer class="footer">
            <div class="container">
                <div class="row align-items-center flex-row-reverse">
                    <div class="col-md-12 col-sm-12 text-center">
                        Copyright © <span id="year"></span> <a href="javascript:void(0)">Centro Adulto Mayor</a>. Todos los derechos reservados.
                    </div>
                </div>
            </div>
        </footer>
        <!-- FOOTER END -->
    </div>

    <!-- BACK-TO-TOP -->
    <a href="#top" id="back-to-top"><i class="fa fa-angle-up"></i></a>

    <!-- JAVASCRIPTS -->
    <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/bootstrap/js/popper.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/bootstrap/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/p-scroll/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('assets/plugins/select2/select2.full.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/sidemenu/sidemenu.js') }}"></script>
    <script src="{{ asset('assets/plugins/sidebar/sidebar.js') }}"></script>
    <script src="{{ asset('assets/js/sticky.js') }}"></script>
    
    <!-- DataTables JS -->
    <script src="{{ asset('assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/dataTables.bootstrap5.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/buttons.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/jszip.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/pdfmake/vfs_fonts.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/buttons.colVis.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/responsive.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/js/table-data.js') }}"></script>

    <!-- Sweet-Alert -->
    <script src="{{ asset('assets/plugins/sweet-alert/sweetalert.min.js') }}"></script>
    <script src="{{ asset('assets/js/sweet-alert.js') }}"></script>

    <!-- THEME-COLOR -->
    <script src="{{ asset('assets/js/themeColors.js') }}"></script>

    <!-- SWITCHER -->
    <script src="{{ asset('assets/switcher/js/switcher.js') }}"></script>

    <!-- CUSTOM JS -->
    <script src="{{ asset('assets/js/custom.js') }}"></script>
    
    {{-- Hook para scripts adicionales que se cargarán por página --}}
    @stack('scripts')

</body>
</html>