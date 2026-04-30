{{-- TAB 7: Intervención --}}

<h4>7. Intervención</h4>

{{-- No es necesario el bloque @php aquí si el controlador ya prepara intervencion_data --}}

<div class="mb-3">
    <label>Resuelto: ¿Cómo?</label><br>
    <textarea name="intervencion[resuelto_descripcion]" class="form-control" rows="2">{{ old('intervencion.resuelto_descripcion', $adulto->intervencion_data['resuelto_descripcion'] ?? '') }}</textarea>
</div>

<div class="mb-3">
    <label>No Resultado: ¿Por qué?</label><br>
    <input type="text" name="intervencion[no_resultado]" class="form-control" value="{{ old('intervencion.no_resultado', $adulto->intervencion_data['no_resultado'] ?? '') }}">
</div>

<div class="mb-3">
    <label>Derivado a otra institución: ¿Por qué?</label><br>
    <input type="text" name="intervencion[derivacion_institucion]" class="form-control" value="{{ old('intervencion.derivacion_institucion', $adulto->intervencion_data['derivacion_institucion'] ?? '') }}">
</div>

<div class="mb-3">
    <label>Derivaciones y Resultados</label><br>
    <input type="text" name="intervencion[der_seguimiento_legal]" class="form-control mb-3" placeholder="Derivado y en seguimiento legal" value="{{ old('intervencion.der_seguimiento_legal', $adulto->intervencion_data['der_seguimiento_legal'] ?? '') }}">

    <input type="text" name="intervencion[der_seguimiento_psi]" class="form-control mb-3" placeholder="Derivado y en seguimiento psicológico" value="{{ old('intervencion.der_seguimiento_psi', $adulto->intervencion_data['der_seguimiento_psi'] ?? '') }}">

    <input type="text" name="intervencion[der_resuelto_externo]" class="form-control mb-3" placeholder="Derivado y resuelto en otra institución" value="{{ old('intervencion.der_resuelto_externo', $adulto->intervencion_data['der_resuelto_externo'] ?? '') }}">

    <input type="text" name="intervencion[der_noresuelto_externo]" class="form-control mb-3" placeholder="Derivado a otra institución y no resuelto" value="{{ old('intervencion.der_noresuelto_externo', $adulto->intervencion_data['der_noresuelto_externo'] ?? '') }}">

    <input type="text" name="intervencion[abandono_victima]" class="form-control mb-3" placeholder="Abandonado por la víctima ¿Qué pasó?" value="{{ old('intervencion.abandono_victima', $adulto->intervencion_data['abandono_victima'] ?? '') }}">

    <input type="text" name="intervencion[resuelto_conciliacion_jio]" class="form-control" placeholder="Resuelto mediante conciliación según Justicia Indígena Originaria" value="{{ old('intervencion.resuelto_conciliacion_jio', $adulto->intervencion_data['resuelto_conciliacion_jio'] ?? '') }}">
</div>

<div class="mb-3">
    <label>Fecha de Intervención</label><br>
    {{-- AHORA accedemos a 'fecha_intervencion' desde el array 'intervencion_data' --}}
    <input type="date" name="intervencion[fecha_intervencion]" class="form-control"
           value="{{ old('intervencion.fecha_intervencion', $adulto->intervencion_data['fecha_intervencion'] ?? '') }}">
</div>
