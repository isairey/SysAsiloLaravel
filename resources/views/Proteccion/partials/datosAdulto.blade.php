{{-- Proteccion/partials/datosAdulto.blade.php --}}
{{-- El section-header se gestiona ahora desde el padre (verDetalleCaso.blade.php) --}}
<div class="detail-group">
    <div class="detail-row">
        <span class="detail-label">Nro. Caso:</span> <span class="detail-value">{{ $adulto->nro_caso ?? 'N/A' }}</span>
    </div>
    <div class="detail-row">
        <span class="detail-label">Fecha de Registro:</span> <span class="detail-value">{{ optional($adulto->fecha)->format('d/m/Y') ?? 'N/A' }}</span>
    </div>
    <div class="detail-row">
        <span class="detail-label">Nombres:</span> <span class="detail-value">{{ optional($adulto->persona)->nombres ?? 'N/A' }}</span>
    </div>
    <div class="detail-row">
        <span class="detail-label">Primer Apellido:</span> <span class="detail-value">{{ optional($adulto->persona)->primer_apellido ?? 'N/A' }}</span>
    </div>
    <div class="detail-row">
        <span class="detail-label">Segundo Apellido:</span> <span class="detail-value">{{ optional($adulto->persona)->segundo_apellido ?? 'N/A' }}</span>
    </div>
    <div class="detail-row">
        <span class="detail-label">CI:</span> <span class="detail-value">{{ optional($adulto->persona)->ci ?? 'N/A' }}</span>
    </div>
    <div class="detail-row">
        <span class="detail-label">Fecha de Nacimiento:</span> <span class="detail-value">{{ optional(optional($adulto->persona)->fecha_nacimiento)->format('d/m/Y') ?? 'N/A' }}</span>
    </div>
    <div class="detail-row">
        <span class="detail-label">Edad:</span> <span class="detail-value">{{ optional($adulto->persona)->edad ?? 'N/A' }}</span>
    </div>
    <div class="detail-row">
        <span class="detail-label">Sexo:</span>
        <span class="detail-value">
            @php
                $sexo = optional($adulto->persona)->sexo;
                if ($sexo == 'M') {
                    echo 'Masculino';
                } elseif ($sexo == 'F') {
                    echo 'Femenino';
                } elseif ($sexo == 'O') { // ¡Nueva condición para 'O'!
                    echo 'Otro';
                } else {
                    echo 'N/A';
                }
            @endphp
        </span>
    </div>
    <div class="detail-row">
        <span class="detail-label">Estado Civil:</span> <span class="detail-value">{{ optional($adulto->persona)->estado_civil ?? 'N/A' }}</span>
    </div>
    <div class="detail-row">
        <span class="detail-label">Teléfono:</span> <span class="detail-value">{{ optional($adulto->persona)->telefono ?? 'N/A' }}</span>
    </div>
    <div class="detail-row">
        <span class="detail-label">Dirección Domicilio:</span> <span class="detail-value">{{ optional($adulto->persona)->domicilio ?? 'N/A' }}</span>
    </div>
    <div class="detail-row">
        <span class="detail-label">Discapacidad:</span> <span class="detail-value">{{ $adulto->discapacidad ?? 'N/A' }}</span>
    </div>
    <div class="detail-row">
        <span class="detail-label">Vive con:</span> <span class="detail-value">{{ $adulto->vive_con ?? 'N/A' }}</span>
    </div>
    <div class="detail-row">
        <span class="detail-label">Migrante:</span> <span class="detail-value">{{ ($adulto->migrante === true ? 'Sí' : ($adulto->migrante === false ? 'No' : 'N/A')) }}</span>
    </div>
</div>
