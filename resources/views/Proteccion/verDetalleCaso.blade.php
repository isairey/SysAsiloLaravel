@extends('layouts.main')

<head>
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/Proteccion/verDetalleCaso.css') }}">
    {{-- Asegúrate de que Feather Icons esté enlazado antes de tu script --}}
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    {{-- Agregamos un script para jQuery si Bootstrap lo necesita --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    {{-- Cargamos SweetAlert2 CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
</head>

@section('content')

<body>

                      <div class="proteccion-header">
                            <h1 class="page-title" style="color: white;">Detalle del Caso</h1>
                      </div>

                      <div class="card proteccion-card">
                            <div class="card-header bg-empresa-primary text-white">
                                <h3 class="card-title text-white mb-0">
                                    Caso de: {{ optional($adulto->persona)->nombres }} {{ optional($adulto->persona)->primer_apellido }} {{ optional($adulto->persona)->segundo_apellido }}
                                </h3>
                                <div class="card-options">
                                    <span class="badge bg-empresa-secondary">
                                        <i class="fe fe-user"></i> ID: {{ $adulto->id_adulto ?? 'N/A' }}
                                    </span>
                                </div>
                            </div>
                            
                            <div class="card-body">
                                <div class="caso-detail-container">
                                    <div class="accordion" id="caseDetailsAccordion">
                                        <div class="detail-section" id="datos-adulto">
                                            <div class="accordion-header" id="headingDatosAdulto">
                                                <h2 class="mb-0">
                                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDatosAdulto" aria-expanded="true" aria-controls="collapseDatosAdulto">
                                                        <div class="section-icon"><i data-feather="user"></i></div>
                                                        <h3 class="section-title">Datos Personales del Adulto Mayor</h3>
                                                        <i data-feather="chevron-down" class="accordion-icon"></i>
                                                    </button>
                                                </h2>
                                            </div>
                                            <div id="collapseDatosAdulto" class="accordion-collapse collapse show" aria-labelledby="headingDatosAdulto" data-bs-parent="#caseDetailsAccordion">
                                                <div class="accordion-body">
                                                    @include('Proteccion.partials.datosAdulto')
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="detail-section" id="actividad-laboral">
                                            <div class="accordion-header" id="headingActividadLaboral">
                                                <h2 class="mb-0">
                                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseActividadLaboral" aria-expanded="false" aria-controls="collapseActividadLaboral">
                                                        <div class="section-icon"><i data-feather="briefcase"></i></div>
                                                        <h3 class="section-title">Actividad Laboral</h3>
                                                        <i data-feather="chevron-down" class="accordion-icon"></i>
                                                    </button>
                                                </h2>
                                            </div>
                                            <div id="collapseActividadLaboral" class="accordion-collapse collapse" aria-labelledby="headingActividadLaboral" data-bs-parent="#caseDetailsAccordion">
                                                <div class="accordion-body">
                                                    @include('Proteccion.partials.actividadLaboral')
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="detail-section" id="encargado">
                                            <div class="accordion-header" id="headingEncargado">
                                                <h2 class="mb-0">
                                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseEncargado" aria-expanded="false" aria-controls="collapseEncargado">
                                                        <div class="section-icon"><i data-feather="user-check"></i></div>
                                                        <h3 class="section-title">Datos del Informante (Encargado)</h3>
                                                        <i data-feather="chevron-down" class="accordion-icon"></i>
                                                    </button>
                                                </h2>
                                            </div>
                                            <div id="collapseEncargado" class="accordion-collapse collapse" aria-labelledby="headingEncargado" data-bs-parent="#caseDetailsAccordion">
                                                <div class="accordion-body">
                                                    @include('Proteccion.partials.Encargado')
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="detail-section" id="denunciado">
                                            <div class="accordion-header" id="headingDenunciado">
                                                <h2 class="mb-0">
                                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDenunciado" aria-expanded="false" aria-controls="collapseDenunciado">
                                                        <div class="section-icon"><i data-feather="user-x"></i></div>
                                                        <h3 class="section-title">Datos del Ofensor</h3>
                                                        <i data-feather="chevron-down" class="accordion-icon"></i>
                                                    </button>
                                                </h2>
                                            </div>
                                            <div id="collapseDenunciado" class="accordion-collapse collapse" aria-labelledby="headingDenunciado" data-bs-parent="#caseDetailsAccordion">
                                                <div class="accordion-body">
                                                    @include('Proteccion.partials.denunciado')
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="detail-section" id="grupo-familiar">
                                            <div class="accordion-header" id="headingGrupoFamiliar">
                                                <h2 class="mb-0">
                                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseGrupoFamiliar" aria-expanded="false" aria-controls="collapseGrupoFamiliar">
                                                        <div class="section-icon"><i data-feather="users"></i></div>
                                                        <h3 class="section-title">Grupo Familiar</h3>
                                                        <i data-feather="chevron-down" class="accordion-icon"></i>
                                                    </button>
                                                </h2>
                                            </div>
                                            <div id="collapseGrupoFamiliar" class="accordion-collapse collapse" aria-labelledby="headingGrupoFamiliar" data-bs-parent="#caseDetailsAccordion">
                                                <div class="accordion-body">
                                                    @include('Proteccion.partials.grupoFamiliar')
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="detail-section" id="croquis">
                                            <div class="accordion-header" id="headingCroquis">
                                                <h2 class="mb-0">
                                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCroquis" aria-expanded="false" aria-controls="collapseCroquis">
                                                        <div class="section-icon"><i data-feather="map-pin"></i></div>
                                                        <h3 class="section-title">Croquis</h3>
                                                        <i data-feather="chevron-down" class="accordion-icon"></i>
                                                    </button>
                                                </h2>
                                            </div>
                                            <div id="collapseCroquis" class="accordion-collapse collapse" aria-labelledby="headingCroquis" data-bs-parent="#caseDetailsAccordion">
                                                <div class="accordion-body">
                                                    @include('Proteccion.partials.croquis')
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="detail-section" id="seguimiento-caso">
                                            <div class="accordion-header" id="headingSeguimiento">
                                                <h2 class="mb-0">
                                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSeguimiento" aria-expanded="false" aria-controls="collapseSeguimiento">
                                                        <div class="section-icon"><i data-feather="clipboard"></i></div>
                                                        <h3 class="section-title">Seguimientos del Caso</h3>
                                                        <i data-feather="chevron-down" class="accordion-icon"></i>
                                                    </button>
                                                </h2>
                                            </div>
                                            <div id="collapseSeguimiento" class="accordion-collapse collapse" aria-labelledby="headingSeguimiento" data-bs-parent="#caseDetailsAccordion">
                                                <div class="accordion-body">
                                                    @include('Proteccion.partials.seguimientoCaso')
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="detail-section" id="anexo-n3">
                                            <div class="accordion-header" id="headingAnexo3">
                                                <h2 class="mb-0">
                                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseAnexo3" aria-expanded="false" aria-controls="collapseAnexo3">
                                                        <div class="section-icon"><i data-feather="file-text"></i></div>
                                                        <h3 class="section-title">Anexo N3 (Personas Asistidas/Relacionadas)</h3>
                                                        <i data-feather="chevron-down" class="accordion-icon"></i>
                                                    </button>
                                                </h2>
                                            </div>
                                            <div id="collapseAnexo3" class="accordion-collapse collapse" aria-labelledby="headingAnexo3" data-bs-parent="#caseDetailsAccordion">
                                                <div class="accordion-body">
                                                    @include('Proteccion.partials.anexoN3')
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="detail-section" id="anexo-n5">
                                            <div class="accordion-header" id="headingAnexo5">
                                                <h2 class="mb-0">
                                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseAnexo5" aria-expanded="false" aria-controls="collapseAnexo5">
                                                        <div class="section-icon"><i data-feather="file-text"></i></div>
                                                        <h3 class="section-title">Anexo N5 (Actividades de Seguimiento y Prevención)</h3>
                                                        <i data-feather="chevron-down" class="accordion-icon"></i>
                                                    </button>
                                                </h2>
                                            </div>
                                            <div id="collapseAnexo5" class="accordion-collapse collapse" aria-labelledby="headingAnexo5" data-bs-parent="#caseDetailsAccordion">
                                                <div class="accordion-body">
                                                    @include('Proteccion.partials.anexoN5')
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="proteccion-actions">
                                    <a href="{{ route('legal.caso.index') }}" class="btn btn-empresa-secondary" style="background-color:gray; color: white;">
                                        <i class="fe fe-arrow-left"></i> Volver al listado
                                    </a>
                                </div>
                            </div>
                      </div>

                  
@endsection

            <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-empresa-primary text-white">
                            <h5 class="modal-title" id="detailModalLabel">Detalles del Registro</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
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
        // Alertas de sesión (si las hubiera)
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
        $('#caseDetailsAccordion').on('show.bs.collapse', function (e) {
            const button = $(e.target).prev('.accordion-header').find('.accordion-button');
            const icon = button.find('.accordion-icon');
            icon.attr('data-feather', 'chevron-up');
            feather.replace({ 'width': 24, 'height': 24, 'stroke-width': 2 });
        });

        $('#caseDetailsAccordion').on('hide.bs.collapse', function (e) {
            const button = $(e.target).prev('.accordion-header').find('.accordion-button');
            const icon = button.find('.accordion-icon');
            icon.attr('data-feather', 'chevron-down');
            feather.replace({ 'width': 24, 'height': 24, 'stroke-width': 2 });
        });

        // Script para cargar dinámicamente el contenido del modal y reemplazar iconos
        var detailModal = document.getElementById('detailModal');
        if (detailModal) {
            detailModal.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget; // Botón que activó el modal
                var title = button.getAttribute('data-title');
                var content = JSON.parse(button.getAttribute('data-content'));

                var modalTitle = detailModal.querySelector('.modal-title');
                var modalBody = detailModal.querySelector('.modal-body');

                modalTitle.textContent = title;
                
                // Construir el contenido del modal
                let htmlContent = '<div class="detail-group">';
                for (const [label, value] of Object.entries(content)) {
                    htmlContent += `
                        <div class="detail-row">
                            <span class="detail-label">${label}:</span> 
                            <span class="detail-value">${value || 'No especificado'}</span>
                        </div>
                    `;
                }
                htmlContent += '</div>';
                modalBody.innerHTML = htmlContent;

                // Importante: Reemplazar iconos dentro del modal después de que el contenido se ha insertado
                if (typeof feather !== 'undefined') {
                    feather.replace({ parent: modalBody });
                }
            });
        }
    });
</script>
@endpush