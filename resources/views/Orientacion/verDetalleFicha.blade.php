@extends('layouts.main')

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle de Orientación</title>
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/Orientacion/verDetalleFicha.css') }}">
    {{-- Asegúrate de que Feather Icons esté enlazado antes de tu script --}}
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    {{-- Agregamos un script para jQuery si Bootstrap lo necesita --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    {{-- Cargamos SweetAlert2 CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
</head>

@section('content')
{{-- Aquí empieza el contenido de la vista --}}
<body>

                      <div class="orientacion-header">
                            <h1 class="page-title">Detalle de Orientación</h1>
                      </div>

                      <div class="card orientacion-card">
                            <div class="card-header bg-empresa-primary text-white">
                                <h3 class="card-title text-white mb-0">
                                    Ficha de Orientación de: {{ optional($orientacion->adulto->persona)->nombres }} {{ optional($orientacion->adulto->persona)->primer_apellido }} {{ optional($orientacion->adulto->persona)->segundo_apellido }}
                                </h3>
                                <div class="card-options">
                                    <span class="badge bg-empresa-secondary">
                                        <i class="fe fe-file-text"></i> Nº Ficha: {{ $orientacion->cod_or ?? 'N/A' }}
                                    </span>
                                </div>
                            </div>

                            <div class="card-body">
                                <div class="orientacion-detail-container">
                                    <div class="accordion" id="orientacionDetailsAccordion">

                                        {{-- Sección 1: Datos Personales del Adulto Mayor --}}
                                        <div class="accordion-item detail-section">
                                            <div class="accordion-header" id="headingDatosAdulto">
                                                <h2 class="mb-0">
                                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDatosAdulto" aria-expanded="true" aria-controls="collapseDatosAdulto">
                                                        <div class="section-icon"><i data-feather="user"></i></div>
                                                        <h3 class="section-title">Datos Personales del Adulto Mayor</h3>
                                                        <i data-feather="chevron-down" class="accordion-icon"></i>
                                                    </button>
                                                </h2>
                                            </div>
                                            <div id="collapseDatosAdulto" class="accordion-collapse collapse show" aria-labelledby="headingDatosAdulto" data-bs-parent="#orientacionDetailsAccordion">
                                                <div class="accordion-body">
                                                    <div class="detail-group">
                                                        <div class="detail-row">
                                                            <span class="detail-label">Número de Caso:</span>
                                                            <span class="detail-value">{{ optional($orientacion->adulto)->nro_caso ?? 'N/A' }}</span>
                                                        </div>
                                                        <div class="detail-row">
                                                            <span class="detail-label">Nombre Completo:</span>
                                                            <span class="detail-value">{{ optional($orientacion->adulto->persona)->nombres }} {{ optional($orientacion->adulto->persona)->primer_apellido }} {{ optional($orientacion->adulto->persona)->segundo_apellido }}</span>
                                                        </div>
                                                        <div class="detail-row">
                                                            <span class="detail-label">Edad:</span>
                                                            <span class="detail-value">{{ optional($orientacion->adulto->persona)->edad ?? 'N/A' }}</span>
                                                        </div>
                                                        <div class="detail-row">
                                                            <span class="detail-label">Domicilio/Comunidad:</span>
                                                            <span class="detail-value">Domicilio: {{ optional($orientacion->adulto->persona)->domicilio ?? 'N/A' }}</span>
                                                            <span class="detail-value">Zona Comunidad: {{ optional($orientacion->adulto->persona)->zona_comunidad ?? 'N/A' }}</span>
                                                        </div>
                                                        <div class="detail-row">
                                                            <span class="detail-label">Teléfono:</span>
                                                            <span class="detail-value">{{ optional($orientacion->adulto->persona)->telefono ?? 'N/A' }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Sección 2: Detalles de la Ficha de Orientación --}}
                                        <div class="accordion-item detail-section">
                                            <div class="accordion-header" id="headingDetallesOrientacion">
                                                <h2 class="mb-0">
                                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDetallesOrientacion" aria-expanded="false" aria-controls="collapseDetallesOrientacion">
                                                        <div class="section-icon"><i data-feather="clipboard"></i></div>
                                                        <h3 class="section-title">Detalles de la Ficha de Orientación</h3>
                                                        <i data-feather="chevron-down" class="accordion-icon"></i>
                                                    </button>
                                                </h2>
                                            </div>
                                            <div id="collapseDetallesOrientacion" class="accordion-collapse collapse" aria-labelledby="headingDetallesOrientacion" data-bs-parent="#orientacionDetailsAccordion">
                                                <div class="accordion-body">
                                                    <div class="detail-group">
                                                        <div class="detail-row">
                                                            <span class="detail-label">Fecha de Ingreso:</span>
                                                            <span class="detail-value">{{ optional($orientacion)->fecha_ingreso ? \Carbon\Carbon::parse($orientacion->fecha_ingreso)->format('d/m/Y') : 'N/A' }}</span>
                                                        </div>
                                                        <div class="detail-row">
                                                            <span class="detail-label">Tipo de Orientación:</span>
                                                            <span class="detail-value">
                                                                @php
                                                                    $tipoOrientacion = optional($orientacion)->tipo_orientacion;
                                                                    if ($tipoOrientacion == 'psicologica') echo 'PSICOLÓGICA';
                                                                    else if ($tipoOrientacion == 'social') echo 'SOCIAL';
                                                                    else if ($tipoOrientacion == 'legal') echo 'LEGAL';
                                                                    else echo 'N/A';
                                                                @endphp
                                                            </span>
                                                        </div>
                                                        <div class="detail-row">
                                                            <span class="detail-label">Motivos de Orientación:</span>
                                                            <span class="detail-value">{{ optional($orientacion)->motivo_orientacion ?? 'N/A' }}</span>
                                                        </div>
                                                        <div class="detail-row">
                                                            <span class="detail-label">Resultados Obtenidos:</span>
                                                            <span class="detail-value">{{ optional($orientacion)->resultado_obtenido ?? 'N/A' }}</span>
                                                        </div>
                                                        <div class="detail-row">
                                                            <span class="detail-label">Registrado por (ID Usuario):</span>
                                                            <span class="detail-value">{{ optional($orientacion)->id_usuario ?? 'N/A' }}</span>
                                                            {{-- Si tienes la relación con el modelo User y quieres mostrar el nombre: --}}
                                                            {{-- <span class="detail-value">{{ optional($orientacion->usuario)->name ?? 'N/A' }}</span> --}}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div> {{-- Fin acordeon principal --}}
                                </div> {{-- Fin caso-detail-container --}}

                                <div class="orientacion-actions">
                                    <a href="{{ route('legal.orientacion.index') }}" class="btn btn-empresa-secondary" style="background-color:gray; color:white;">
                                        <i class="fe fe-arrow-left"></i> Volver al listado
                                    </a>
                                </div>
                            </div> {{-- Fin card-body --}}
                      </div> {{-- Fin card proteccion-card --}}

                  
@endsection

{{-- Modal para confirmación de eliminación (si es necesario) --}}

{{-- Modal para detalles (se mantiene si se usa para otros fines) --}}
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-empresa-primary text-white">
                <h5 class="modal-title" id="detailModalLabel">Detalles del Registro</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{-- Contenido se carga dinámicamente --}}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-empresa-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
{{-- Cargamos SweetAlert2 JS --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar Feather Icons al cargar la página
        if (typeof feather !== 'undefined') {
            feather.replace();
        }

        // --- MANEJO DE ALERTAS CON SWEETALERT2 ---
        @if(session('success'))
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: '¡Éxito!',
                text: '{{ session('success') }}',
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: true
            });
        @endif
        
        @if(session('error'))
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'error',
                title: '¡Error!',
                text: '{{ session('error') }}',
                showConfirmButton: false,
                timer: 5000,
                timerProgressBar: true
            });
        @endif
        // --- FIN DEL MANEJO DE ALERTAS ---

        // Script para manejar los íconos de los acordeones al abrir/cerrar
        $('#orientacionDetailsAccordion').on('show.bs.collapse', function (e) {
            const button = $(e.target).prev('.accordion-header').find('.accordion-button');
            const icon = button.find('.accordion-icon');
            icon.attr('data-feather', 'chevron-up');
            feather.replace({ 'width': 24, 'height': 24, 'stroke-width': 2 });
        });

        $('#orientacionDetailsAccordion').on('hide.bs.collapse', function (e) {
            const button = $(e.target).prev('.accordion-header').find('.accordion-button');
            const icon = button.find('.accordion-icon');
            icon.attr('data-feather', 'chevron-down');
            feather.replace({ 'width': 24, 'height': 24, 'stroke-width': 2 });
        });

        // Script para cargar dinámicamente el contenido del modal
        var detailModal = document.getElementById('detailModal');
        if (detailModal) {
            detailModal.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget;
                var title = button.getAttribute('data-title');
                var content = JSON.parse(button.getAttribute('data-content'));

                var modalTitle = detailModal.querySelector('.modal-title');
                var modalBody = detailModal.querySelector('.modal-body');

                modalTitle.textContent = title;

                let htmlContent = '<div class="detail-group">';
                for (const [label, value] of Object.entries(content)) {
                    htmlContent += `
                        <div class="detail-row">
                            <span class="detail-label">${label}:</span>
                            <span class="detail-value">${value}</span>
                        </div>
                    `;
                }
                htmlContent += '</div>';
                modalBody.innerHTML = htmlContent;

                if (typeof feather !== 'undefined') {
                    feather.replace({ parent: modalBody });
                }
            });
        }
    });
</script>
@endpush
</body>
</html>
