{{-- TAB 6: Seguimiento del Caso --}}

<h4>6. Seguimiento del Caso</h4>

<div id="seguimientos-container">
    {{-- Itera sobre los seguimientos existentes del adulto para pre-llenar --}}
    @foreach(old('seguimientos', $adulto->seguimientos ?? []) as $index => $seguimiento)
        <div class="seguimiento-group border p-3 mb-3">
            <h6 style="color:black;">Seguimiento #{{ $index + 1 }}</h6>
            {{-- Campo oculto para el ID del seguimiento (importante para actualizar/eliminar) --}}
            <input type="hidden" name="seguimientos[{{ $index }}][id_seg]" value="{{ $seguimiento->id_seg ?? '' }}">

            <div>
                <label>Seguimiento del Caso Nro:</label>
                <input type="text" name="nro_caso_visual" value="{{ optional($adulto)->nro_caso ?? '' }}" readonly class="form-control">
            </div>
            <div class="mb-3">
                <label>Nro de Seguimiento</label>
                <input type="text" name="seguimientos[{{ $index }}][nro]" class="form-control"
                    value="{{ old('seguimientos.' . $index . '.nro', $seguimiento->nro ?? '') }}">
            </div>

            <div class="mb-3">
                <label>Fecha</label>
                <input type="date" name="seguimientos[{{ $index }}][fecha]" class="form-control"
                    value="{{ old('seguimientos.' . $index . '.fecha', $seguimiento->fecha ? \Carbon\Carbon::parse($seguimiento->fecha)->format('Y-m-d') : '') }}">
            </div>

            <div class="mb-3">
                <label>Acción Realizada</label>
                <textarea name="seguimientos[{{ $index }}][accion_realizada]" class="form-control" rows="3">{{ old('seguimientos.' . $index . '.accion_realizada', $seguimiento->accion_realizada ?? '') }}</textarea>
            </div>

            <div class="mb-3">
                <label>Resultado Obtenido</label>
                <textarea name="seguimientos[{{ $index }}][resultado_obtenido]" class="form-control" rows="3">{{ old('seguimientos.' . $index . '.resultado_obtenido', $seguimiento->resultado_obtenido ?? '') }}</textarea>
            </div>

            <div class="mb-3">
                <label>Nombre del/la Funcionario(a) que realizó la acción</label>
                {{-- Muestra el nombre del funcionario asociado al seguimiento (si existe), o el del usuario autenticado --}}
                <input type="text" disabled class="form-control"
                       value="{{ optional(optional($seguimiento)->usuario)->persona->nombres ?? optional(auth()->user()->persona)->nombres }} {{ optional(optional($seguimiento)->usuario)->persona->primer_apellido ?? optional(auth()->user()->persona)->primer_apellido }} {{ optional(optional($seguimiento)->usuario)->persona->segundo_apellido ?? optional(auth()->user()->persona)->segundo_apellido }}">
            </div>
            {{-- Botón para eliminar un seguimiento existente --}}
            <button type="button" class="botonEliminarSeguimiento" onclick="this.closest('.seguimiento-group').remove()">Eliminar Seguimiento</button>
        </div>
    @endforeach
    <p><strong>El formulario de intervencion se asociará con la fecha del seguimiento mas reciente.</strong></p>
    {{-- Bloque por defecto si no hay seguimientos pre-cargados --}}
    @if(count($adulto->seguimientos ?? []) == 0 && !old('seguimientos'))
        <div class="seguimiento-group border p-3 mb-3">
            <h6 style="color: black;">Seguimiento #1</h6>
            <div>
                <label>Seguimiento del Caso de Adulto Mayor Nro:</label>
                <input type="text" name="nro_caso_visual" value="{{ optional($adulto)->nro_caso ?? '' }}" readonly class="form-control">
            </div>
            <div class="mb-3">
                <label>Nro de Seguimiento</label>
                <input type="text" name="seguimientos[0][nro]" class="form-control" value="{{ old('seguimientos.0.nro') }}">
            </div>

            <div class="mb-3">
                <label>Fecha</label>
                <input type="date" name="seguimientos[0][fecha]" class="form-control" value="{{ old('seguimientos.0.fecha') }}">
            </div>

            <div class="mb-3">
                <label>Acción Realizada</label>
                <textarea name="seguimientos[0][accion_realizada]" class="form-control" rows="3">{{ old('seguimientos.0.accion_realizada') }}</textarea>
            </div>

            <div class="mb-3">
                <label>Resultado Obtenido</label>
                <textarea name="seguimientos[0][resultado_obtenido]" class="form-control" rows="3">{{ old('seguimientos.0.resultado_obtenido') }}</textarea>
            </div>

            <div class="mb-3">
                <label>Nombre del/la Funcionario(a) que realizó la acción</label>
                <input type="text" disabled class="form-control" value="{{ optional(auth()->user()->persona)->nombres }} {{ optional(auth()->user()->persona)->primer_apellido }} {{ optional(auth()->user()->persona)->segundo_apellido }}">
            </div>
            <button type="button" class="btn btn-danger btn-sm mt-2 remove-seguimiento-btn" style="display:none;" onclick="this.closest('.seguimiento-group').remove()">Eliminar</button>
        </div>
    @endif
</div>

{{-- Botón para añadir un nuevo seguimiento --}}
<button type="button" class="btn btn-primary mt-3" id="add-seguimiento-btn">+ Agregar seguimiento</button>

<script>
    // Inicializa el contador con la cantidad de elementos existentes (o 0 si no hay)
    let seguimientoCounter = document.querySelectorAll('#seguimientos-container .seguimiento-group').length;

    document.getElementById('add-seguimiento-btn').addEventListener('click', function() {
        const container = document.getElementById('seguimientos-container');
        const nuevo = document.createElement('div');
        nuevo.className = 'seguimiento-group border p-3 mb-3';
        nuevo.innerHTML = `
            <h6 style="color: black;">Seguimiento #${seguimientoCounter + 1}</h6>
            <input type="hidden" name="seguimientos[${seguimientoCounter}][id_seg]" value=""> {{-- ID vacío para nuevos registros --}}
            <div>
                <label>Seguimiento del Caso Nro:</label>
                <input type="text" name="nro_caso_visual" value="{{ optional($adulto)->nro_caso ?? '' }}" readonly class="form-control">
            </div>
            <div class="mb-3">
                <label>Nro de Seguimiento</label>
                <input type="text" name="seguimientos[${seguimientoCounter}][nro]" class="form-control">
            </div>
            <div class="mb-3">
                <label>Fecha</label>
                <input type="date" name="seguimientos[${seguimientoCounter}][fecha]" class="form-control">
            </div>
            <div class="mb-3">
                <label>Acción Realizada</label>
                <textarea name="seguimientos[${seguimientoCounter}][accion_realizada]" class="form-control" rows="3"></textarea>
            </div>
            <div class="mb-3">
                <label>Resultado Obtenido</label>
                <textarea name="seguimientos[${seguimientoCounter}][resultado_obtenido]" class="form-control" rows="3"></textarea>
            </div>
            <div class="mb-3">
                <label>Funcionario que realizó la acción</label>
                <input type="text" disabled class="form-control" value="{{ optional(auth()->user()->persona)->nombres ?? '' }} {{ optional(auth()->user()->persona)->primer_apellido ?? '' }} {{ optional(auth()->user()->persona)->segundo_apellido ?? '' }}">
            </div>
            <button type="button" class="btn btn-danger btn-sm mt-2" onclick="this.closest('.seguimiento-group').remove()">Eliminar</button>
        `;
        container.appendChild(nuevo);
        seguimientoCounter++;

        // Asegurarse de que el botón "Eliminar" en el primer elemento (si existía) se muestre
        const firstSeguimientoBtn = document.querySelector('#seguimientos-container .seguimiento-group .remove-seguimiento-btn');
        if (firstSeguimientoBtn) {
            firstSeguimientoBtn.style.display = 'block';
        }
    });

    // Muestra el botón de eliminar si hay más de un elemento inicial (en el caso de que la plantilla por defecto se muestre)
    document.addEventListener('DOMContentLoaded', function() {
        const defaultSeguimientoBtn = document.querySelector('#seguimientos-container .seguimiento-group .remove-seguimiento-btn');
        if (defaultSeguimientoBtn && seguimientoCounter > 0) {
            defaultSeguimientoBtn.style.display = 'block';
        }
    });
</script>
