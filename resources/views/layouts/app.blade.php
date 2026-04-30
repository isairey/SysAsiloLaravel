{{-- resources/views/layouts/app.blade.php --}}

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Mi App')</title>

    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- FontAwesome CDN (para que los <i class="fas fa-…"> funcionen) -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css"
          integrity="sha512-KyZXEAg3QhqLMpG8r+…"
          crossorigin="anonymous" />

    <!-- Aquí inyectamos estilos adicionales desde las vistas hijas -->
    @stack('styles')

    <style>
        /* Sidebar estilos básicos */
        .sidebar {
            width: 240px;
            min-height: 100vh;
            position: fixed;
            top: 0; left: 0;
            background: #212529;
            color: #fff;
        }
        .main-content {
            margin-left: 240px;
            padding: 2rem 1rem;
        }
        @media (max-width: 768px) {
            .sidebar {
                position: relative;
                width: 100%;
                min-height: auto;
            }
            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>

    {{-- Si tienes un partial de sidebar, inclúyelo aquí. Por ejemplo: --}}
    {{-- @include('layouts.sidebar') --}}

    <div class="main-content">
        @yield('content')
    </div>

    <!-- Bootstrap JS (opcional para colapsar menú, etc.) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Aquí inyectamos scripts adicionales desde las vistas hijas -->
    @stack('scripts')
</body>
</html>
