{{-- Proteccion/partials/grupoFamiliar.blade.php --}}
<div class="detail-group">
    @if($adulto->grupoFamiliar->isNotEmpty())
        <ul class="item-list">
            @foreach($adulto->grupoFamiliar as $familiar)
                <li>
                    <div class="sub-section-title">Familiar: {{ $familiar->nombres }} {{ $familiar->apellido_paterno }} {{ $familiar->apellido_materno }}</div>
                    <div class="detail-row">
                        <span class="detail-label">Parentesco:</span> <span class="detail-value">{{ $familiar->parentesco ?? 'N/A' }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Edad:</span> <span class="detail-value">{{ $familiar->edad ?? 'N/A' }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Ocupación:</span> <span class="detail-value">{{ $familiar->ocupacion ?? 'N/A' }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Dirección:</span> <span class="detail-value">{{ $familiar->direccion ?? 'N/A' }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Teléfono:</span> <span class="detail-value">{{ $familiar->telefono ?? 'N/A' }}</span>
                    </div>
                </li>
            @endforeach
        </ul>
    @else
        <div class="no-data-message">No se ha registrado grupo familiar.</div>
    @endif
</div>
