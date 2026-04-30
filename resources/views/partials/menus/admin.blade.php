{{--
Ruta: resources/views/partials/menus/admin.blade.php
Descripción: Menú completo y corregido para el rol de Administrador.
--}}

{{-- ===================== SECCIÓN ADMINISTRACIÓN DEL SISTEMA ===================== --}}
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

{{-- ===================== SECCIÓN GESTIÓN DE AdultoS ===================== --}}
<li class="sub-category"><h3>Gestión de Adultos</h3></li>
<li class="slide">
    <a class="side-menu__item" data-bs-toggle="slide" href="javascript:void(0);">
        <i class="side-menu__icon fe fe-user-check"></i><span class="side-menu__label">Adulto Mayor</span><i class="angle fe fe-chevron-right"></i>
    </a>
    <ul class="slide-menu">
        {{-- ** CORRECCIÓN IMPORTANTE **: El nombre de la ruta no lleva el prefijo 'admin.' --}}
        <li><a href="{{ route('gestionar-adultomayor.index') }}" class="slide-item">Gestionar Adultos</a></li>
        <li><a href="{{ route('gestionar-adultomayor.create') }}" class="slide-item">Registrar Adulto</a></li>
    </ul>
</li>

{{-- ===================== SECCIÓN MÓDULO PROTECCIÓN ===================== --}}
<li class="sub-category"><h3>Módulo Protección</h3></li>
<li class="slide">
    <a class="side-menu__item" href="{{ route('admin.caso.index') }}"><i class="side-menu__icon fe fe-file-plus"></i><span class="side-menu__label">Registrar Caso</span></a>
</li>
<li class="slide">
    <a class="side-menu__item" href="{{ route('admin.reportes_proteccion.index') }}"><i class="side-menu__icon fe fe-file-text"></i><span class="side-menu__label">Reportes Protección</span></a>
</li>
{{-- ===================== SECCIÓN MÓDULO ORIENTACION ===================== --}}
<li class="sub-category"><h3>Módulo Orientación</h3></li>
<li class="slide">
    <a class="side-menu__item" href="{{ route('admin.orientacion.index') }}"><i class="side-menu__icon fe fe-clipboard"></i><span class="side-menu__label">Registrar Ficha</span></a>
</li>
<li class="slide">
    <a class="side-menu__item" href="{{ route('admin.reportes_orientacion.index') }}"><i class="side-menu__icon fe fe fe-shield"></i><span class="side-menu__label">Reportes Orientacion</span></a>
</li>

{{-- ===================== SECCIÓN MÓDULO MÉDICO (VISTA COMPLETA PARA ADMIN) ===================== --}}
<li class="sub-category"><h3>Módulo Médico</h3></li>
<li class="slide">
    <a class="side-menu__item" data-bs-toggle="slide" href="javascript:void(0);"><i class="side-menu__icon fe fe-heart"></i><span class="side-menu__label">Enfermería</span><i class="angle fe fe-chevron-right"></i></a>
    <ul class="slide-menu">
        <li><a href="{{ route('admin.medico.historia_clinica.index') }}" class="slide-item">Historias Clínicas</a></li>
        <li><a href="{{ route('admin.enfermeria.index') }}" class="slide-item">Enfermería</a></li>
        <li><a href="{{ route('admin.reportes_enfermeria.index') }}" class="slide-item">Reportes Enfermería</a></li>
    </ul>
</li>
<li class="slide">
    <a class="side-menu__item" data-bs-toggle="slide" href="javascript:void(0);"><i class="side-menu__icon fe fe-activity"></i><span class="side-menu__label">Fisioterapia-Kinesiologia</span><i class="angle fe fe-chevron-right"></i></a>
    <ul class="slide-menu">
        <li><a href="{{ route('admin.fisiokine.indexFisio') }}" class="slide-item">Registrar Ficha</a></li>
        <li><a href="{{ route('admin.reportefisio.index') }}" class="slide-item">Reportes Fisio-Kine</a></li>
    </ul>
</li>
