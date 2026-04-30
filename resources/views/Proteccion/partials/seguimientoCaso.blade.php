{{-- Proteccion/partials/seguimientoCaso.blade.php --}}
<div class="section-content">
    {{-- Comprueba si existen seguimientos para el Adulto Mayor --}}
    @if($adulto->seguimientos->isNotEmpty())
        <ul class="item-list">
            {{-- MODIFICACIÓN CLAVE AQUÍ: Ordena por 'id_seg' de forma ascendente --}}
            @foreach($adulto->seguimientos->sortBy('id_seg') as $seguimiento)
                <li class="seguimiento-item">
                    {{-- Título principal del seguimiento --}}
                    <div class="sub-section-title border-bottom pb-2 mb-3">
                        Seguimiento Nro: {{ $seguimiento->nro ?? 'N/A' }}
                    </div>

                    {{-- Detalles del Seguimiento --}}
                    <div class="seguimiento-details mb-3">
                        <div class="detail-row">
                            <span class="detail-label">Fecha:</span> 
                            <span class="detail-value">{{ optional($seguimiento->fecha)->format('d/m/Y') ?? 'N/A' }}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Acción Realizada:</span> 
                            <span class="detail-value">{{ $seguimiento->accion_realizada ?? 'N/A' }}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Resultado Obtenido:</span> 
                            <span class="detail-value">{{ $seguimiento->resultado_obtenido ?? 'N/A' }}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Registrado por:</span> 
                            <span class="detail-value">
                                {{ optional(optional($seguimiento->usuario)->persona)->nombres ?? 'N/A' }}
                                {{ optional(optional($seguimiento->usuario)->persona)->primer_apellido ?? '' }}
                                {{ optional(optional($seguimiento->usuario)->persona)->segundo_apellido ?? '' }}
                            </span>
                        </div>
                    </div>

                    {{-- Sección de Intervención (con toggle) --}}
                    @if(optional($seguimiento->intervencion)->exists)
                        <button type="button" class="btn btn-view-details mt-3 toggle-intervencion-btn" data-bs-toggle="collapse" data-bs-target="#collapseIntervencion-{{ $seguimiento->nro }}">
                            <i data-feather="plus-circle" class="toggle-icon me-2"></i>
                            <span class="button-text">Ver Detalles de Intervención</span>
                        </button>
                        <div class="intervencion-details-collapsible collapse mt-3" id="collapseIntervencion-{{ $seguimiento->nro }}">
                            <div class="sub-section-title border-bottom pb-2 mb-3 mt-3">Detalle de Intervención</div>
                            
                            <div class="detail-row">
                                <span class="detail-label">Fecha Intervención:</span>
                                <span class="detail-value">
                                    @php
                                        $fechaIntervencion = optional($seguimiento->intervencion)->fecha_intervencion;
                                        $formattedDate = 'N/A';

                                        if ($fechaIntervencion) {
                                            try {
                                                if (!($fechaIntervencion instanceof \Carbon\Carbon)) {
                                                    $fechaIntervencion = \Carbon\Carbon::parse($fechaIntervencion);
                                                }
                                                $formattedDate = $fechaIntervencion->format('d/m/Y');
                                            } catch (\Exception $e) {
                                                $formattedDate = 'Fecha inválida';
                                            }
                                        }
                                    @endphp
                                    {{ $formattedDate }}
                                </span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Resuelto (Descripción):</span> 
                                <span class="detail-value">{{ optional($seguimiento->intervencion)->resuelto_descripcion ?? 'N/A' }}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">No Resultado (Motivo):</span> 
                                <span class="detail-value">{{ optional($seguimiento->intervencion)->no_resultado ?? 'N/A' }}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Derivado a Institución:</span> 
                                <span class="detail-value">{{ optional($seguimiento->intervencion)->derivacion_institucion ?? 'N/A' }}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Seguimiento Legal:</span> 
                                <span class="detail-value">{{ optional($seguimiento->intervencion)->der_seguimiento_legal ?? 'N/A' }}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Seguimiento Psicológico:</span> 
                                <span class="detail-value">{{ optional($seguimiento->intervencion)->der_seguimiento_psi ?? 'N/A' }}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Resuelto Externo:</span> 
                                <span class="detail-value">{{ optional($seguimiento->intervencion)->der_resuelto_externo ?? 'N/A' }}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">No Resuelto Externo:</span> 
                                <span class="detail-value">{{ optional($seguimiento->intervencion)->der_noresuelto_externo ?? 'N/A' }}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Abandono Víctima:</span> 
                                <span class="detail-value">{{ optional($seguimiento->intervencion)->abandono_victima ?? 'N/A' }}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Conciliación JIO:</span> 
                                <span class="detail-value">{{ optional($seguimiento->intervencion)->resuelto_conciliacion_jio ?? 'N/A' }}</span>
                            </div>
                        </div>
                    @else
                        <div class="no-data-message mt-3">No hay datos de intervención para este seguimiento.</div>
                    @endif
                </li>
            @endforeach
        </ul>
    @else
        <div class="no-data-message">No se han registrado seguimientos del caso.</div>
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.toggle-intervencion-btn').forEach(button => {
            const icon = button.querySelector('.toggle-icon');
            const buttonTextSpan = button.querySelector('.button-text');
            const targetId = button.getAttribute('data-bs-target');
            const targetElement = document.querySelector(targetId);

            const updateButtonState = () => {
                if ($(targetElement).hasClass('show')) {
                    icon.setAttribute('data-feather', 'minus-circle');
                    buttonTextSpan.textContent = 'Ocultar Detalles de Intervención';
                } else {
                    icon.setAttribute('data-feather', 'plus-circle');
                    buttonTextSpan.textContent = 'Ver Detalles de Intervención';
                }
                if (typeof feather !== 'undefined') {
                    feather.replace({ target: icon });
                }
            };

            button.addEventListener('click', function() {
                setTimeout(updateButtonState, 150);
            });
            
            updateButtonState(); 

            if (typeof feather !== 'undefined') {
                feather.replace({ target: icon });
            }
        });
    });
</script>
