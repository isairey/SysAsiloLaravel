@extends('layouts.main')


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle de Historia Clínica</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    
    <!-- CSS principal para la estructura general de esta vista de detalles (ahora con estilos de acordeón) -->
    <link rel="stylesheet" href="{{ asset('css/Medico/verDetallesHistoria.css') }}">
    
    <!-- CSS específicos para los partials si contienen estilos únicos, o se pueden consolidar -->
    <link rel="stylesheet" href="{{ asset('css/Medico/partials/historiaDetalle.css') }}">
    <link rel="stylesheet" href="{{ asset('css/Medico/partials/examenDetalle.css') }}">
    
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    {{-- No necesitamos estilos inline aquí para los tabs/accordions, se moverán a CSS externo --}}
</head>

@section('content')
<body>

                    <h1 class="page-title">Detalle de Historia Clínica para: {{ optional($historiaClinica->adulto->persona)->nombres }} {{ optional($historiaClinica->adulto->persona)->primer_apellido }} {{ optional($historiaClinica->adulto->persona)->segundo_apellido }}</h1>
                    
                    <div>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">Inicio</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('responsable.enfermeria.medico.historia_clinica.index') }}">Historia Clínica</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Detalle</li>
                        </ol>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Datos del Adulto Mayor</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <strong>Nombre Completo:</strong> {{ optional($historiaClinica->adulto->persona)->nombres }} {{ optional($historiaClinica->adulto->persona)->primer_apellido }} {{ optional($historiaClinica->adulto->persona)->segundo_apellido }}
                                        </div>
                                        <div class="col-md-4">
                                            <strong>CI:</strong> {{ optional($historiaClinica->adulto->persona)->ci }}
                                        </div>
                                        <div class="col-md-4">
                                            <strong>Edad:</strong> {{ optional($historiaClinica->adulto->persona)->edad }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card tab-section-card"> {{-- Mantener esta clase si los estilos de card-body la utilizan --}}
                                <div class="card-body">
                                    {{-- Inicio de la estructura de acordeones --}}
                                    <div class="accordion" id="historiaClinicaDetailsAccordion">

                                        {{-- Sección 1: Historia Clínica --}}
                                        <div class="accordion-item detail-section">
                                            <div class="accordion-header" id="headingHistoriaClinica">
                                                <h2 class="mb-0">
                                                    <button class="accordion-button {{ ($activeTab ?? 'historia') == 'historia' ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#collapseHistoriaClinica" aria-expanded="{{ ($activeTab ?? 'historia') == 'historia' ? 'true' : 'false' }}" aria-controls="collapseHistoriaClinica">
                                                        <div class="section-icon"><i data-feather="book-open"></i></div>
                                                        <h3 class="section-title">1. Historia Clínica</h3>
                                                        <i data-feather="chevron-down" class="accordion-icon"></i>
                                                    </button>
                                                </h2>
                                            </div>
                                            <div id="collapseHistoriaClinica" class="accordion-collapse collapse {{ ($activeTab ?? 'historia') == 'historia' ? 'show' : '' }}" aria-labelledby="headingHistoriaClinica" data-bs-parent="#historiaClinicaDetailsAccordion">
                                                <div class="accordion-body">
                                                    @include('Medico.partials.historia', compact('historiaClinica'))
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Sección 2: Exámenes Complementarios y Medicamentos --}}
                                        <div class="accordion-item detail-section">
                                            <div class="accordion-header" id="headingExamenes">
                                                <h2 class="mb-0">
                                                    <button class="accordion-button {{ ($activeTab ?? 'historia') == 'examenes' ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#collapseExamenes" aria-expanded="{{ ($activeTab ?? 'historia') == 'examenes' ? 'true' : 'false' }}" aria-controls="collapseExamenes">
                                                        <div class="section-icon"><i data-feather="activity"></i></div>
                                                        <h3 class="section-title">2. Exámenes Complementarios y Medicamentos</h3>
                                                        <i data-feather="chevron-down" class="accordion-icon"></i>
                                                    </button>
                                                </h2>
                                            </div>
                                            <div id="collapseExamenes" class="accordion-collapse collapse {{ ($activeTab ?? 'historia') == 'examenes' ? 'show' : '' }}" aria-labelledby="headingExamenes" data-bs-parent="#historiaClinicaDetailsAccordion">
                                                <div class="accordion-body">
                                                    @include('Medico.partials.examen', compact('examenesComplementarios', 'medicamentosRecetados'))
                                                </div>
                                            </div>
                                        </div>

                                    </div> {{-- Fin del acordeón principal --}}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 text-center">
                        <a href="{{ route('responsable.enfermeria.medico.historia_clinica.index') }}" class="btn btn-secondary">
                            <i class="fe fe-arrow-left"></i> Volver al listado
                        </a>
                        <a href="{{ route('responsable.enfermeria.medico.historia_clinica.edit', ['id_historia' => $historiaClinica->id_historia]) }}" class="btn btn-primary ms-2">
                            <i class="fe fe-edit"></i> Editar Historia
                        </a>
                    </div>

 

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof feather !== 'undefined') {
            feather.replace();
        }

        // Script para manejar los íconos de los acordeones al abrir/cerrar
        const accordionElement = document.getElementById('historiaClinicaDetailsAccordion');
        if (accordionElement) {
            accordionElement.addEventListener('show.bs.collapse', function (e) {
                const button = e.target.closest('.accordion-item').querySelector('.accordion-button');
                const icon = button.querySelector('.accordion-icon');
                if (icon) {
                    icon.setAttribute('data-feather', 'chevron-up');
                    feather.replace({ target: icon });
                }
            });

            accordionElement.addEventListener('hide.bs.collapse', function (e) {
                const button = e.target.closest('.accordion-item').querySelector('.accordion-button');
                const icon = button.querySelector('.accordion-icon');
                if (icon) {
                    icon.setAttribute('data-feather', 'chevron-down');
                    feather.replace({ target: icon });
                }
            });
        }
    });
</script>
@endpush

</body>
</html>
