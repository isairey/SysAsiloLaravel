{{--
Ruta: resources/views/partials/menus/responsable_enfermeria.blade.php
Descripción: Menú 100% corregido y completo para 'responsable' con especialidad 'Enfermeria'.
Las rutas son las correctas y funcionales según el archivo web.php original.
--}}


{{-- Menú Desplegable: Enfermería --}}
<li class="slide">
    <a class="side-menu__item" data-bs-toggle="slide" href="javascript:void(0);">
        <i class="side-menu__icon fe fe-heart"></i>
        <span class="side-menu__label">Enfermería</span>
        <i class="angle fe fe-chevron-right"></i>
    </a>
    <ul class="slide-menu">
        <li class="side-menu-label1"><a href="javascript:void(0)">Enfermería</a></li>
        <li><a href="{{ route('responsable.enfermeria.medico.historia_clinica.index') }}" class="slide-item">Historias Clínicas</a></li>
        <li><a href="{{ route('responsable.enfermeria.enfermeria.index') }}" class="slide-item">Atención Enfermería</a></li>
        <li><a href="{{ route('responsable.enfermeria.reportes_enfermeria.index') }}" class="slide-item">Reportes Enfermería</a></li>
    </ul>
</li>
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
