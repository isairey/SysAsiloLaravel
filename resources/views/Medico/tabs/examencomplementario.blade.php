{{-- TAB 2: EXÁMENES COMPLEMENTARIOS --}}
{{-- El control de visibilidad de la pestaña se maneja desde registrarHistoriaClinica.blade.php --}}

@php
    // El registro de examen general (presión arterial, temperatura, etc.)
    // Ahora debería ser el primer elemento de la colección $examenesComplementarios si existe,
    // o un nuevo objeto si no hay ninguno.
    $generalExamen = $examenesComplementarios->first() ?? new \App\Models\ExamenComplementario();

    // La colección de medicamentos ahora viene de $medicamentosRecetados
    // Si está vacía, añade un objeto MedicamentoRecetado vacío para la fila inicial.
    if ($medicamentosRecetados->isEmpty()) {
        $medicamentosRecetados->push(new \App\Models\MedicamentoRecetado());
    }
@endphp

<div class="examenes-form-section">
    <h4 class="text-center mb-4">EXÁMENES COMPLEMENTARIOS:</h4>
    <div class="examenes-grid">
        <div class="form-group">
            <label for="presion_arterial">PRESIÓN ARTERIAL:</label>
            <input type="text" class="full-width-input" id="presion_arterial" name="presion_arterial" value="{{ old('presion_arterial', $generalExamen->presion_arterial ?? '') }}">
        </div>
        <div class="form-group">
            <label for="temperatura">TEMPERATURA:</label>
            <input type="text" class="full-width-input" id="temperatura" name="temperatura" value="{{ old('temperatura', $generalExamen->temperatura ?? '') }}">
        </div>
        <div class="form-group">
            <label for="peso_corporal">PESO CORPORAL:</label>
            <input type="text" class="full-width-input" id="peso_corporal" name="peso_corporal" value="{{ old('peso_corporal', $generalExamen->peso_corporal ?? '') }}">
        </div>
    </div>

    <div class="form-grid-2-col align-items-center">
        <div class="form-group">
            <label for="resultado_prueba" class="form-label">RESULTADO DE LA PRUEBA (mg/dl):</label>
            <input type="text" class="full-width-input" id="resultado_prueba" name="resultado_prueba" value="{{ old('resultado_prueba', $generalExamen->resultado_prueba ?? '') }}">
        </div>
        <div class="form-group">
            <label for="diagnostico" class="form-label">DIAGNÓSTICO:</label>
            <input type="text" class="full-width-input" id="diagnostico" name="diagnostico" value="{{ old('diagnostico', $generalExamen->diagnostico ?? '') }}">
        </div>
    </div>

    <div class="medicamentos-table-container">
        <h4 class="text-center mt-4 mb-3">MEDICAMENTOS</h4>
        <table class="custom-table" id="medicamentosTable">
            <thead>
                <tr>
                    <th>(Nombre Genérico, Forma Farmacéutica y Concentración)</th>
                    <th>CANTIDAD RECETADA</th>
                    <th>CANTIDAD DISPENSADA</th>
                    <th>VALOR UNITARIO</th>
                    <th>TOTAL</th>
                    <th></th> {{-- Para el botón de eliminar --}}
                </tr>
            </thead>
            <tbody>
                {{-- Iterar sobre la nueva colección $medicamentosRecetados --}}
                @foreach($medicamentosRecetados as $index => $medicamento)
                    <tr class="medicamento-row">
                        <td>
                            {{-- ID oculto para actualizar el registro existente --}}
                            <input type="hidden" name="medicamentos[{{ $index }}][id_medicamento_recetado]" value="{{ $medicamento->id_medicamento_recetado ?? '' }}">
                            <input type="text" name="medicamentos[{{ $index }}][nombre_medicamento]" class="table-input" value="{{ old('medicamentos.' . $index . '.nombre_medicamento', $medicamento->nombre_medicamento ?? '') }}">
                        </td>
                        <td>
                            <input type="number" name="medicamentos[{{ $index }}][cantidad_recetada]" class="table-input" value="{{ old('medicamentos.' . $index . '.cantidad_recetada', $medicamento->cantidad_recetada ?? '') }}">
                        </td>
                        <td>
                            <input type="number" name="medicamentos[{{ $index }}][cantidad_dispensada]" class="table-input" value="{{ old('medicamentos.' . $index . '.cantidad_dispensada', $medicamento->cantidad_dispensada ?? '') }}">
                        </td>
                        <td>
                            <input type="number" step="0.01" name="medicamentos[{{ $index }}][valor_unitario]" class="table-input" value="{{ old('medicamentos.' . $index . '.valor_unitario', $medicamento->valor_unitario ?? '') }}">
                        </td>
                        <td>
                            <input type="number" step="0.01" name="medicamentos[{{ $index }}][total]" class="table-input" value="{{ old('medicamentos.' . $index . '.total', $medicamento->total ?? '') }}">
                        </td>
                        <td>
                            <button type="button" class="btn-remove-row">Eliminar</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <button type="button" id="add-medicamento-button" class="btn-add-row">Añadir Medicamento</button>
    </div>
</div>
