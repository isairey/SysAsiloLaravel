{{-- Proteccion/partials/denunciado.blade.php --}}
{{-- El section-header se gestiona ahora desde el padre (verDetalleCaso.blade.php) --}}
<div class="detail-group">
    @if(optional($adulto->denunciado)->exists && optional($adulto->denunciado)->personaNatural)
        <div class="detail-row">
            <span class="detail-label">Nombres:</span> <span class="detail-value">{{ optional($adulto->denunciado->personaNatural)->nombres ?? 'N/A' }} {{ optional($adulto->denunciado->personaNatural)->primer_apellido ?? '' }} {{ optional($adulto->denunciado->personaNatural)->segundo_apellido ?? '' }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">CI:</span> <span class="detail-value">{{ optional($adulto->denunciado->personaNatural)->ci ?? 'N/A' }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Sexo:</span> <span class="detail-value">{{ optional($adulto->denunciado)->sexo == 'M' ? 'Masculino' : 'Femenino' }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Teléfono:</span> <span class="detail-value">{{ optional($adulto->denunciado->personaNatural)->telefono ?? 'N/A' }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Relación de Parentesco:</span> <span class="detail-value">{{ optional($adulto->denunciado->personaNatural)->relacion_parentesco ?? 'N/A' }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Dirección de Domicilio:</span> <span class="detail-value">{{ optional($adulto->denunciado->personaNatural)->direccion_domicilio ?? 'N/A' }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Ocupación:</span> <span class="detail-value">{{ optional($adulto->denunciado->personaNatural)->ocupacion ?? 'N/A' }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Descripción de Hechos:</span> <span class="detail-value">{{ optional($adulto->denunciado)->descripcion_hechos ?? 'N/A' }}</span>
        </div>
    @else
        <div class="no-data-message">No se ha registrado denunciado.</div>
    @endif
</div>
