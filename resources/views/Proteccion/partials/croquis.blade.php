{{-- Proteccion/partials/croquis.blade.php --}}
{{-- El section-header se gestiona ahora desde el padre (verDetalleCaso.blade.php) --}}
<div class="detail-group">
    @if(optional($adulto->croquis)->exists)
        <div class="detail-row">
            <span class="detail-label">Nombre Denunciante:</span> <span class="detail-value">{{ optional($adulto->croquis)->nombre_denunciante ?? 'N/A' }} {{ optional($adulto->croquis)->apellidos_denunciante ?? '' }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">CI Denunciante:</span> <span class="detail-value">{{ optional($adulto->croquis)->ci_denunciante ?? 'N/A' }}</span>
        </div>

        {{-- Nueva sección para la imagen del croquis --}}
        @if(optional($adulto->croquis)->ruta_imagen)
            <div class="detail-row mt-4">
                <span class="detail-label">Imagen del Croquis:</span>
                <div class="mt-2 border border-gray-300 p-2 rounded-lg flex items-center justify-center overflow-hidden" style="width: 100%; max-width: 600px; max-height: 400px;">
                    {{-- Usar Storage::url() para obtener la URL pública de la imagen --}}
                    <img src="{{ Storage::url($adulto->croquis->ruta_imagen) }}" alt="Croquis del Domicilio" class="max-h-full max-w-full object-contain">
                </div>
            </div>
        @else
            <div class="detail-row mt-2">
                <span class="detail-label">Imagen del Croquis:</span> <span class="detail-value">No hay imagen registrada.</span>
            </div>
        @endif
    @else
        <div class="no-data-message">No se ha registrado croquis.</div>
    @endif
</div>
