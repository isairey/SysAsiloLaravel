{{-- Proteccion/partials/actividadLaboral.blade.php --}}
{{-- El section-header se gestiona ahora desde el padre (verDetalleCaso.blade.php) --}}
<div class="detail-group">
    @if(optional($adulto->actividadLaboral)->exists)
        <div class="detail-row">
            <span class="detail-label">Nombre de Actividad:</span> <span class="detail-value">{{ optional($adulto->actividadLaboral)->nombre_actividad ?? 'N/A' }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Dirección de Trabajo:</span> <span class="detail-value">{{ optional($adulto->actividadLaboral)->direccion_trabajo ?? 'N/A' }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Teléfono Laboral:</span> <span class="detail-value">{{ optional($adulto->actividadLaboral)->telefono_laboral ?? 'N/A' }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Horario:</span> <span class="detail-value">{{ optional($adulto->actividadLaboral)->horario ?? 'N/A' }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Horas por Día:</span> <span class="detail-value">{{ optional($adulto->actividadLaboral)->horas_x_dia ?? 'N/A' }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Remuneración Mensual Aprox.:</span> <span class="detail-value">{{ optional($adulto->actividadLaboral)->rem_men_aprox ?? 'N/A' }}</span>
        </div>
    @else
        <div class="no-data-message">No se ha registrado actividad laboral.</div>
    @endif
</div>
