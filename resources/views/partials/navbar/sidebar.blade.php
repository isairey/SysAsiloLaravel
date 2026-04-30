<!-- {{--
Ruta: resources/views/partials/navbar/sidebar.blade.php
Este archivo es el responsable de cargar el menú lateral correcto según el rol del usuario.
--}} -->
<div class="app-sidebar__overlay" data-bs-toggle="sidebar"></div>
<div class="app-sidebar">
    <div class="side-header">
        <a class="header-brand1" href="{{ url('/') }}">
            <img src="{{ asset('assets/images/brand/logo.png') }}" class="header-brand-img desktop-logo" alt="logo">
            <img src="{{ asset('assets/images/brand/logo-1.png') }}" class="header-brand-img toggle-logo" alt="logo">
            <img src="{{ asset('assets/images/brand/logo-2.png') }}" class="header-brand-img light-logo" alt="logo">
            <img src="{{ asset('assets/images/brand/logo-3.png') }}" class="header-brand-img light-logo1" alt="logo">
        </a>
    </div>
    <div class="main-sidemenu">
        <div class="slide-left" id="slide-left"><svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191" width="24" height="24" viewBox="0 0 24 24"><path d="M13.293 6.293 7.586 12l5.707 5.707 1.414-1.414L10.414 12l4.293-4.293z"/></svg></div>
        
        <ul class="side-menu">
            
            @if (Auth::check())
                @php
                    $roleName = strtolower(Auth::user()->rol->nombre_rol ?? 'default');
                @endphp

                @switch($roleName)
                    @case('admin')
                        @include('partials.menus.admin')
                        @break

                    @case('legal')
                        @include('partials.menus.legal')
                        @break

                    @case('responsable')
                        @php
                            $especialidad = Auth::user()->persona->area_especialidad ?? '';
                        @endphp

                        @if($especialidad === 'Enfermeria')
                            @include('partials.menus.responsable_enfermeria')
                        
                        {{-- CORRECCIÓN: Si es Fisioterapia O Kinesiología, carga el mismo menú --}}
                        @elseif($especialidad === 'Fisioterapia' || $especialidad === 'Kinesiologia')
                            @include('partials.menus.responsable_fisioterapia')
                        @endif
                        
                        @break

                    @default
                        @include('partials.menus.default')
                @endswitch
            @endif

        </ul>

        <div class="slide-right" id="slide-right"><svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191" width="24" height="24" viewBox="0 0 24 24"><path d="M10.707 17.707 16.414 12l-5.707-5.707-1.414 1.414L13.586 12l-4.293 4.293z"/></svg></div>
    </div>
</div>