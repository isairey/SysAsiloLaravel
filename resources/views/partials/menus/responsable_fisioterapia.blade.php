{{--
Ruta: resources/views/partials/menus/responsable_fisioterapia.blade.php
Menú para usuarios con rol 'responsable' y especialidad 'Fisioterapia-Kinesiología'.
--}}


<li class="slide">
    <a class="side-menu__item" data-bs-toggle="slide" href="javascript:void(0);"><i class="side-menu__icon fe fe-activity"></i><span class="side-menu__label">Fisioterapia-Kinesiologia</span><i class="angle fe fe-chevron-right"></i></a>
    <ul class="slide-menu">
        <li><a href="{{ route('responsable.fisioterapia.fisiokine.indexFisio') }}" class="slide-item">Registrar Ficha</a></li>
        <li><a href="{{ route('responsable.fisioterapia.reportefisio.index') }}" class="slide-item">Reportes Fisio-Kine</a></li>
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
