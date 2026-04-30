<div class="detail-section-content">
    <h4 class="text-center mb-4">DETALLES DE EXÁMENES COMPLEMENTARIOS:</h4>

    @php
        // Obtener el registro de examen general, si existe
        $generalExamen = $examenesComplementarios->first();
    @endphp

    <div class="detail-group">
        <div class="detail-row">
            <span class="detail-label">PRESIÓN ARTERIAL:</span>
            <span class="detail-value">{{ optional($generalExamen)->presion_arterial ?? 'N/A' }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">TEMPERATURA:</span>
            <span class="detail-value">{{ optional($generalExamen)->temperatura ?? 'N/A' }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">PESO CORPORAL:</span>
            <span class="detail-value">{{ optional($generalExamen)->peso_corporal ?? 'N/A' }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">RESULTADO DE LA PRUEBA (mg/dl):</span>
            <span class="detail-value">{{ optional($generalExamen)->resultado_prueba ?? 'N/A' }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">DIAGNÓSTICO:</span>
            <span class="detail-value">{{ optional($generalExamen)->diagnostico ?? 'N/A' }}</span>
        </div>
    </div>

    <h4 class="text-center mt-5 mb-4">MEDICAMENTOS RECETADOS</h4>
    <div class="medicamentos-table-container">
        @if($medicamentosRecetados->isNotEmpty())
        <table class="details-table">
            <thead>
                <tr>
                    <th>Nombre Medicamento</th>
                    <th>Cantidad Recetada</th>
                    <th>Cantidad Dispensada</th>
                    <th>Valor Unitario</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($medicamentosRecetados as $medicamento)
                <tr>
                    <td>{{ optional($medicamento)->nombre_medicamento ?? 'N/A' }}</td>
                    <td>{{ optional($medicamento)->cantidad_recetada ?? 'N/A' }}</td>
                    <td>{{ optional($medicamento)->cantidad_dispensada ?? 'N/A' }}</td>
                    <td>{{ optional($medicamento)->valor_unitario ? number_format($medicamento->valor_unitario, 2) : 'N/A' }}</td>
                    <td>{{ optional($medicamento)->total ? number_format($medicamento->total, 2) : 'N/A' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
            <p class="text-center text-muted">No se han registrado medicamentos para esta historia clínica.</p>
        @endif
    </div>
</div>
