@extends('layouts.main')

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Atenciones de Enfermería</title>
    {{-- Enlazar tus CSS --}}
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/Medico/verDetallesEnfermeria.css') }}"> {{-- CSS específico con los colores unificados --}}
    {{-- Enlazar Feather Icons y jQuery (necesario para Bootstrap JS) --}}
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    {{-- Asegúrate de que Bootstrap JS esté enlazado si no lo hace dashboard.css para los acordeones --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>

@section('content')

<body>
    {{-- Título de la página --}}
    <div class="enfermeria-header">
        <h1 class="page-title">Historial de Atenciones de Enfermería</h1>
    </div>

    {{-- Tarjeta principal de detalles --}}
    <div class="card enfermeria-card">
        <div class="card-header bg-empresa-primary"> {{-- Usamos bg-empresa-primary aquí --}}
            <h3 class="card-title text-white mb-0"> {{-- Título de la tarjeta, forzar texto blanco --}}
                Adulto Mayor: {{ optional($adulto->persona)->nombres }} {{ optional($adulto->persona)->primer_apellido }} {{ optional($adulto->persona)->segundo_apellido }}
            </h3>
            <div class="card-options">
                <span class="badge bg-empresa-secondary"> {{-- Usamos bg-empresa-secondary sin text-white --}}
                    <i class="fe fe-user"></i> CI: {{ optional($adulto->persona)->ci ?? 'N/A' }}
                </span>
            </div>
        </div>

        <div class="card-body">
            @forelse($fichasEnfermeria as $index => $fichaEnfermeria)
                <div class="accordion mb-3" id="enfermeriaFichaAccordion-{{ $fichaEnfermeria->cod_enf }}"> {{-- Acordeón por cada ficha --}}
                    <div class="accordion-item detail-section">
                        <div class="accordion-header" id="headingFicha-{{ $fichaEnfermeria->cod_enf }}">
                            <h2 class="mb-0">
                                <button class="accordion-button {{ $index == 0 ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFicha-{{ $fichaEnfermeria->cod_enf }}" aria-expanded="{{ $index == 0 ? 'true' : 'false' }}" aria-controls="collapseFicha-{{ $fichaEnfermeria->cod_enf }}">
                                    <div class="section-icon"><i data-feather="file-text"></i></div>
                                    <h3 class="section-title">Ficha N°: {{ $fichaEnfermeria->cod_enf ?? 'N/A' }} (Fecha: {{ optional($fichaEnfermeria->created_at)->format('d/m/Y H:i') ?? 'N/A' }})</h3>
                                    <i data-feather="{{ $index == 0 ? 'chevron-up' : 'chevron-down' }}" class="accordion-icon"></i>
                                </button>
                            </h2>
                        </div>
                        <div id="collapseFicha-{{ $fichaEnfermeria->cod_enf }}" class="accordion-collapse collapse {{ $index == 0 ? 'show' : '' }}" aria-labelledby="headingFicha-{{ $fichaEnfermeria->cod_enf }}" data-bs-parent="#enfermeriaFichaAccordion-{{ $fichaEnfermeria->cod_enf }}">
                            <div class="accordion-body">
                                {{-- Sección 1: Datos Personales del Adulto Mayor (reducida o eliminada aquí si ya se muestra arriba) --}}
                                {{-- Si ya se muestra el nombre del adulto mayor en el header, esta sección podría ser redundante o eliminada.
                                    La dejaré como una "sub-sección" de la ficha por si hay datos adicionales específicos del AM
                                    que se quieran mostrar por ficha (aunque normalmente no sería el caso para datos personales estáticos). --}}
                                <div class="sub-detail-group mt-3">
                                    <h4>Datos de la atención:</h4>
                                    <div class="detail-group">
                                        <div class="detail-row">
                                            <span class="detail-label">Atendido por:</span>
                                            <span class="detail-value">
                                                {{ optional($fichaEnfermeria->usuario->persona)->nombres }}
                                                {{ optional($fichaEnfermeria->usuario->persona)->primer_apellido }}
                                                {{ optional($fichaEnfermeria->usuario->persona)->segundo_apellido }}
                                                ({{ optional($fichaEnfermeria->usuario)->email }})
                                                @if(!optional($fichaEnfermeria->usuario->persona)->nombres)
                                                    N/A
                                                @endif
                                            </span>
                                        </div>
                                        <div class="detail-row">
                                            <span class="detail-label">Fecha de Registro:</span>
                                            <span class="detail-value">{{ optional($fichaEnfermeria)->created_at ? \Carbon\Carbon::parse($fichaEnfermeria->created_at)->format('d/m/Y H:i') : 'N/A' }}</span>
                                        </div>
                                        <div class="detail-row">
                                            <span class="detail-label">Última Actualización:</span>
                                            <span class="detail-value">{{ optional($fichaEnfermeria)->updated_at ? \Carbon\Carbon::parse($fichaEnfermeria->updated_at)->format('d/m/Y H:i') : 'N/A' }}</span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Sección 2: Control de Signos Vitales --}}
                                <div class="sub-detail-group mt-3">
                                    <h4>Control de Signos Vitales:</h4>
                                    <div class="detail-group">
                                        <div class="detail-row">
                                            <span class="detail-label">Presión Arterial:</span>
                                            <span class="detail-value">{{ optional($fichaEnfermeria)->presion_arterial ?? 'N/A' }}</span>
                                        </div>
                                        <div class="detail-row">
                                            <span class="detail-label">Frecuencia Cardíaca:</span>
                                            <span class="detail-value">{{ optional($fichaEnfermeria)->frecuencia_cardiaca ?? 'N/A' }}</span>
                                        </div>
                                        <div class="detail-row">
                                            <span class="detail-label">Frecuencia Respiratoria:</span>
                                            <span class="detail-value">{{ optional($fichaEnfermeria)->frecuencia_respiratoria ?? 'N/A' }}</span>
                                        </div>
                                        <div class="detail-row">
                                            <span class="detail-label">Pulso:</span>
                                            <span class="detail-value">{{ optional($fichaEnfermeria)->pulso ?? 'N/A' }}</span>
                                        </div>
                                        <div class="detail-row">
                                            <span class="detail-label">Temperatura:</span>
                                            <span class="detail-value">{{ optional($fichaEnfermeria)->temperatura ?? 'N/A' }}</span>
                                        </div>
                                        <div class="detail-row">
                                            <span class="detail-label">Control Oximetría:</span>
                                            <span class="detail-value">{{ optional($fichaEnfermeria)->control_oximetria ?? 'N/A' }}</span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Sección 3: Atenciones de Enfermería --}}
                                <div class="sub-detail-group mt-3">
                                    <h4>Atenciones de Enfermería:</h4>
                                    <div class="detail-group">
                                        <div class="detail-row">
                                            <span class="detail-label">Inyectables:</span>
                                            <span class="detail-value">{{ optional($fichaEnfermeria)->inyectables ?? 'N/A' }}</span>
                                        </div>
                                        <div class="detail-row">
                                            <span class="detail-label">Peso y Talla:</span>
                                            <span class="detail-value">{{ optional($fichaEnfermeria)->peso_talla ?? 'N/A' }}</span>
                                        </div>
                                        <div class="detail-row">
                                            <span class="detail-label">Orientación Alimentación:</span>
                                            <span class="detail-value">{{ optional($fichaEnfermeria)->orientacion_alimentacion ?? 'N/A' }}</span>
                                        </div>
                                        <div class="detail-row">
                                            <span class="detail-label">Lavado de Oídos:</span>
                                            <span class="detail-value">{{ optional($fichaEnfermeria)->lavado_oidos ?? 'N/A' }}</span>
                                        </div>
                                        <div class="detail-row">
                                            <span class="detail-label">Orientación Tratamiento:</span>
                                            <span class="detail-value">{{ optional($fichaEnfermeria)->orientacion_tratamiento ?? 'N/A' }}</span>
                                        </div>
                                        <div class="detail-row">
                                            <span class="detail-label">Curación:</span>
                                            <span class="detail-value">{{ optional($fichaEnfermeria)->curacion ?? 'N/A' }}</span>
                                        </div>
                                        <div class="detail-row">
                                            <span class="detail-label">Administración Medicamentos:</span>
                                            <span class="detail-value">{{ optional($fichaEnfermeria)->adm_medicamentos ?? 'N/A' }}</span>
                                        </div>
                                        <div class="detail-row">
                                            <span class="detail-label">Derivación:</span>
                                            <span class="detail-value">{{ optional($fichaEnfermeria)->derivacion ?? 'N/A' }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="ficha-actions mt-4 text-end">
                                    <a href="{{ route('responsable.enfermeria.enfermeria.edit', ['cod_enf' => $fichaEnfermeria->cod_enf]) }}" class="btn btn-warning btn-sm" title="Editar esta ficha">
                                        <i class="fe fe-edit"></i> Editar Ficha
                                    </a>
                                    <form action="{{ route('responsable.enfermeria.enfermeria.destroy', ['cod_enf' => $fichaEnfermeria->cod_enf]) }}" method="POST" class="d-inline form-delete-ficha-enfermeria">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Eliminar esta ficha">
                                            <i class="fe fe-trash-2"></i> Eliminar Ficha
                                        </button>
                                    </form>
                                </div>

                            </div> {{-- Fin accordion-body --}}
                        </div> {{-- Fin accordion-collapse --}}
                    </div> {{-- Fin accordion-item --}}
                </div> {{-- Fin acordeon por cada ficha --}}
            @empty
                <div class="text-center text-muted">
                    <i class="fe fe-inbox"></i>
                    <br>
                    No hay fichas de atención de enfermería registradas para este adulto mayor.
                </div>
            @endforelse

            <div class="enfermeria-actions mt-4 d-flex justify-content-between">
                <a href="{{ route('responsable.enfermeria.enfermeria.index') }}" class="btn btn-empresa-secondary" style="background-color: gray; color:white;">
                    <i class="fe fe-arrow-left"></i>Volver al listado
                </a>
            </div>
        </div> {{-- Fin card-body --}}
    </div> {{-- Fin card enfermeria-card --}}

@endsection

{{-- Scripts específicos para la página --}}
@push('scripts')
{{-- SweetAlert2 JS --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar Feather Icons al cargar la página
        if (typeof feather !== 'undefined') {
            feather.replace();
        }

        // Script para manejar los íconos de los acordeones al abrir/cerrar
        // Se adjunta a todo el documento y delega a los botones del acordeón
        $(document).on('show.bs.collapse', '.accordion-collapse', function (e) {
            const button = $(e.target).prev('.accordion-header').find('.accordion-button');
            const icon = button.find('.accordion-icon');
            icon.attr('data-feather', 'chevron-up');
            feather.replace({ target: icon[0] });
        });

        $(document).on('hide.bs.collapse', '.accordion-collapse', function (e) {
            const button = $(e.target).prev('.accordion-header').find('.accordion-button');
            const icon = button.find('.accordion-icon');
            icon.attr('data-feather', 'chevron-down');
            feather.replace({ target: icon[0] });
        });

        // Confirmación para eliminar ficha de enfermería
        document.querySelectorAll('.form-delete-ficha-enfermeria').forEach(form => {
            form.addEventListener('submit', function(event) {
                event.preventDefault(); // Prevenir el envío inmediato del formulario
                
                Swal.fire({
                    title: '¿Está seguro?',
                    text: "Se eliminará esta ficha de atención de enfermería. ¡Esta acción no se puede deshacer!",
                    icon: 'warning', // Cambiado a 'warning' para algo menos destructivo visualmente que 'error'
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit(); // Enviar el formulario si el usuario confirma
                    }
                });
            });
        });
    });
</script>
</body>
</html>