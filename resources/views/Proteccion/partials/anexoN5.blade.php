{{-- Proteccion/partials/anexoN5.blade.php --}}
{{-- El section-header se gestiona ahora desde el padre (verDetalleCaso.blade.php) --}}
<div class="detail-group">
    @if($adulto->anexoN5->isNotEmpty())
        <ul class="item-list">
            @foreach($adulto->anexoN5 as $anexo5Item)
                <li>
                    <div class="sub-section-title">Anexo N5 Nro: {{ optional($anexo5Item)->numero ?? 'N/A' }}</div>
                    <div class="detail-row">
                        <span class="detail-label">Fecha:</span> <span class="detail-value">{{ optional($anexo5Item->fecha)->format('d/m/Y') ?? 'N/A' }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Acción Realizada:</span> <span class="detail-value">{{ optional($anexo5Item)->accion_realizada ?? 'N/A' }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Resultado Obtenido:</span> <span class="detail-value">{{ optional($anexo5Item)->resultado_obtenido ?? 'N/A' }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Registrado por:</span> <span class="detail-value">{{ optional(optional($anexo5Item->usuario)->persona)->nombres }} {{ optional(optional($anexo5Item->usuario)->persona)->primer_apellido }} {{ optional(optional($anexo5Item->usuario)->persona)->segundo_apellido }}</span>
                    </div>
                </li>
            @endforeach
        </ul>
    @else
        <div class="no-data-message">No se han registrado Anexos N5.</div>
    @endif
</div>
