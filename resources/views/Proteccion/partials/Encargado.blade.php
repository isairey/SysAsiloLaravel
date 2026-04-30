{{-- Proteccion/partials/Encargado.blade.php --}}
{{-- El section-header se gestiona ahora desde el padre (verDetalleCaso.blade.php) --}}
<div class="detail-group">
    @if($encargado) {{-- $encargado ya es la primera instancia o null --}}
        <div class="detail-row">
            <span class="detail-label">Tipo de Encargado:</span> <span class="detail-value">{{ $encargado->tipo_encargado == 'natural' ? 'Persona Natural' : 'Persona Jurídica' }}</span>
        </div>
        @if($encargado->tipo_encargado == 'natural')
            <div class="detail-row">
                <span class="detail-label">Nombres:</span> <span class="detail-value">{{ optional($encargado->personaNatural)->nombres ?? 'N/A' }} {{ optional($encargado->personaNatural)->primer_apellido ?? '' }} {{ optional($encargado->personaNatural)->segundo_apellido ?? '' }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Edad:</span> <span class="detail-value">{{ optional($encargado->personaNatural)->edad ?? 'N/A' }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">CI:</span> <span class="detail-value">{{ optional($encargado->personaNatural)->ci ?? 'N/A' }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Teléfono:</span> <span class="detail-value">{{ optional($encargado->personaNatural)->telefono ?? 'N/A' }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Dirección Domicilio:</span> <span class="detail-value">{{ optional($encargado->personaNatural)->direccion_domicilio ?? 'N/A' }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Relación/Parentesco:</span> <span class="detail-value">{{ optional($encargado->personaNatural)->relacion_parentesco ?? 'N/A' }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Dirección de Trabajo:</span> <span class="detail-value">{{ optional($encargado->personaNatural)->direccion_de_trabajo ?? 'N/A' }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Ocupación:</span> <span class="detail-value">{{ optional($encargado->personaNatural)->ocupacion ?? 'N/A' }}</span>
            </div>
        @else {{-- Tipo Jurídica --}}
            <div class="detail-row">
                <span class="detail-label">Nombre de Institución:</span> <span class="detail-value">{{ optional($encargado->personaJuridica)->nombre_institucion ?? 'N/A' }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Dirección:</span> <span class="detail-value">{{ optional($encargado->personaJuridica)->direccion ?? 'N/A' }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Teléfono:</span> <span class="detail-value">{{ optional($encargado->personaJuridica)->telefono_juridica ?? 'N/A' }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Nombre del Funcionario:</span> <span class="detail-value">{{ optional($encargado->personaJuridica)->nombre_funcionario ?? 'N/A' }}</span>
            </div>
        @endif
    @else
        <div class="no-data-message">No se ha registrado ningún informante (encargado).</div>
    @endif
</div>
