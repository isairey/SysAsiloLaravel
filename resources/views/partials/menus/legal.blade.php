<!-- {{--
Ruta: resources/views/partials/menus/legal.blade.php
Menú específico para usuarios con rol 'legal'.
Este menú debe ser incluido en el layout principal (ej. sidebar.blade.php)
cuando el rol del usuario autenticado sea 'legal'.
--}} -->

{{-- Dashboard --}}
<li class="slide">
    <a class="side-menu__item" href="{{ route('legal.dashboard') }}">
        <i class="side-menu__icon fe fe-home"></i>
        <span class="side-menu__label">Dashboard</span>
    </a>
</li>

{{-- Título de la Categoría: Gestión --}}
<li class="sub-category">
    <h3>Gestión de Adultos Mayores</h3>
</li>

{{-- Menú Desplegable: Gestionar Adulto Mayor --}}
<li class="slide">
    <a class="side-menu__item" data-bs-toggle="slide" href="javascript:void(0)">
        <i class="side-menu__icon fe fe-users"></i>
        <span class="side-menu__label">Gestionar Adulto Mayor</span>
        <i class="angle fe fe-chevron-right"></i>
    </a>
    <ul class="slide-menu">
        <li class="side-menu-label1"><a href="javascript:void(0)">Adultos Mayores</a></li>
        <li>
            <a href="{{ route('gestionar-adultomayor.index') }}" class="slide-item">Ver Adultos Mayores</a>
        </li>
        {{-- ENLACE CORREGIDO Y AÑADIDO --}}
        <li>
            <a href="{{ route('gestionar-adultomayor.create') }}" class="slide-item">Registrar Adulto</a>
        </li>
    </ul>
</li>
{{-- ===================== SECCIÓN MÓDULO PROTECCIÓN ===================== --}}
<li class="sub-category"><h3>Módulo Protección</h3></li>
<li class="slide">
    <a class="side-menu__item" href="{{ route('legal.caso.index') }}"><i class="side-menu__icon fe fe-file-plus"></i><span class="side-menu__label">Registrar Caso</span></a>
</li>
<li class="slide">
    <a class="side-menu__item" href="{{ route('legal.reportes_proteccion.index') }}"><i class="side-menu__icon fe fe-file-text"></i><span class="side-menu__label">Reportes Protección</span></a>
</li>
{{-- ===================== SECCIÓN MÓDULO ORIENTACION ===================== --}}
<li class="sub-category"><h3>Módulo Orientación</h3></li>
<li class="slide">
    <a class="side-menu__item" href="{{ route('legal.orientacion.index') }}"><i class="side-menu__icon fe fe-clipboard"></i><span class="side-menu__label">Registrar Ficha</span></a>
</li>
<li class="slide">
    <a class="side-menu__item" href="{{ route('legal.reportes_orientacion.index') }}"><i class="side-menu__icon fe fe fe-shield"></i><span class="side-menu__label">Reportes Orientacion</span></a>
</li>
