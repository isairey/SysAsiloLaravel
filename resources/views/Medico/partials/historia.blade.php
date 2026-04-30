<div class="detail-section-content">
    <h4 class="text-center mb-4">DATOS DE LA HISTORIA CLÍNICA</h4>

    <div class="detail-group">
        <div class="detail-row">
            <span class="detail-label">MUNICIPIO:</span>
            <span class="detail-value">{{ optional($historiaClinica)->municipio_nombre ?? 'N/A' }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">ESTABLECIMIENTO DE SALUD:</span>
            <span class="detail-value">{{ optional($historiaClinica)->establecimiento ?? 'N/A' }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">ANTECEDENTES PERSONALES:</span>
            <span class="detail-value">{{ optional($historiaClinica)->antecedentes_personales ?? 'N/A' }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">ANTECEDENTES FAMILIARES:</span>
            <span class="detail-value">{{ optional($historiaClinica)->antecedentes_familiares ?? 'N/A' }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">ESTADO ACTUAL:</span>
            <span class="detail-value">{{ optional($historiaClinica)->estado_actual ?? 'N/A' }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">TIPO DE CONSULTA:</span>
            <span class="detail-value">{{ optional($historiaClinica)->tipo_consulta == 'N' ? 'NUEVA' : (optional($historiaClinica)->tipo_consulta == 'R' ? 'RECONSULTA' : 'N/A') }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">OCUPACIÓN:</span>
            <span class="detail-value">{{ optional($historiaClinica)->ocupacion ?? 'N/A' }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">GRADO DE INSTRUCCIÓN:</span>
            <span class="detail-value">{{ optional($historiaClinica)->grado_instruccion ?? 'N/A' }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">LUGAR DE NACIMIENTO (PROVINCIA):</span>
            <span class="detail-value">{{ optional($historiaClinica)->lugar_nacimiento_provincia ?? 'N/A' }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">LUGAR DE NACIMIENTO (DEPARTAMENTO):</span>
            <span class="detail-value">{{ optional($historiaClinica)->lugar_nacimiento_departamento ?? 'N/A' }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">DOMICILIO ACTUAL:</span>
            <span class="detail-value">{{ optional($historiaClinica)->domicilio_actual ?? 'N/A' }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">REGISTRADO POR:</span>
            <span class="detail-value">
                {{ optional($historiaClinica->usuario->persona)->nombres }}
                {{ optional($historiaClinica->usuario->persona)->primer_apellido }}
                {{ optional($historiaClinica->usuario->persona)->segundo_apellido ?? 'N/A' }}
            </span>
        </div>
        <div class="detail-row">
            <span class="detail-label">FECHA DE REGISTRO:</span>
            <span class="detail-value">{{ optional($historiaClinica)->created_at ? $historiaClinica->created_at->format('d/m/Y H:i') : 'N/A' }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">ÚLTIMA ACTUALIZACIÓN:</span>
            <span class="detail-value">{{ optional($historiaClinica)->updated_at ? $historiaClinica->updated_at->format('d/m/Y H:i') : 'N/A' }}</span>
        </div>
    </div>
</div>
