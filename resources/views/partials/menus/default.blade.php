{{--
Ruta del archivo: resources/views/partials/menus/default.blade.php
--}}
<li class="sub-category">
    <h3>Menú</h3>
</li>
<li class="slide">
    <a class="side-menu__item" href="{{ route('dashboard') }}">
        <i class="side-menu__icon fe fe-home"></i>
        <span class="side-menu__label">Panel Principal</span>
    </a>
</li>
<li class="slide">
    <a class="side-menu__item" href="{{ route('profile.show') }}">
        <i class="side-menu__icon fe fe-user"></i>
        <span class="side-menu__label">Mi Perfil</span>
    </a>
</li>

<!-- {{-- Puedes agregar más enlaces aquí que sean comunes para todos los usuarios --}} -->
