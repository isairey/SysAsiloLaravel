{{-- TAB 4: Grupo Familiar --}}

<h4>4. Grupo Familiar De La Persona Adulta Mayor</h4>

<div id="familiares-container">
    {{-- Itera sobre los familiares existentes del adulto para pre-llenar --}}
    @foreach(old('familiares', $adulto->grupoFamiliar ?? []) as $index => $familiar)
        <div class="familiar-group border p-3 mb-3">
            <h6>Familiar #{{ $index + 1 }}</h6> {{-- Añade un título para cada familiar --}}
            {{-- Campo oculto para el ID del familiar (importante para actualizar/eliminar) --}}
            <input type="hidden" name="familiares[{{ $index }}][id_familiar]" value="{{ $familiar->id_familiar ?? '' }}">

            <div class="row">
                <div class="col-md-6 mb-2">
                    <label>Apellido Paterno</label>
                    <input type="text" name="familiares[{{ $index }}][apellido_paterno]" class="form-control"
                        value="{{ old("familiares.$index.apellido_paterno", $familiar->apellido_paterno ?? '') }}">
                </div>

                <div class="col-md-6 mb-2">
                    <label>Apellido Materno</label>
                    <input type="text" name="familiares[{{ $index }}][apellido_materno]" class="form-control"
                        value="{{ old("familiares.$index.apellido_materno", $familiar->apellido_materno ?? '') }}">
                </div>

                <div class="col-md-6 mb-2">
                    <label>Nombres</label>
                    <input type="text" name="familiares[{{ $index }}][nombres]" class="form-control"
                        value="{{ old("familiares.$index.nombres", $familiar->nombres ?? '') }}">
                </div>

                <div class="col-md-6 mb-2">
                    <label>Parentesco</label>
                    <input type="text" name="familiares[{{ $index }}][parentesco]" class="form-control"
                        value="{{ old("familiares.$index.parentesco", $familiar->parentesco ?? '') }}">
                </div>

                <div class="col-md-4 mb-2">
                    <label>Edad</label>
                    <input type="number" name="familiares[{{ $index }}][edad]" class="form-control"
                        value="{{ old("familiares.$index.edad", $familiar->edad ?? '') }}">
                </div>

                <div class="col-md-4 mb-2">
                    <label>Ocupación</label>
                    <input type="text" name="familiares[{{ $index }}][ocupacion]" class="form-control"
                        value="{{ old("familiares.$index.ocupacion", $familiar->ocupacion ?? '') }}">
                </div>

                <div class="col-md-4 mb-2">
                    <label>Teléfono</label>
                    <input type="text" name="familiares[{{ $index }}][telefono]" class="form-control"
                        value="{{ old("familiares.$index.telefono", $familiar->telefono ?? '') }}">
                </div>

                <div class="col-md-12 mb-2">
                    <label>Dirección</label>
                    <input type="text" name="familiares[{{ $index }}][direccion]" class="form-control"
                        value="{{ old("familiares.$index.direccion", $familiar->direccion ?? '') }}">
                </div>
            </div>
            {{-- Botón para eliminar un familiar existente --}}
            <button type="button" class="btn btn-danger btn-sm mt-2" onclick="this.closest('.familiar-group').remove()">Eliminar</button>
        </div>
    @endforeach

    {{-- Bloque por defecto si no hay familiares pre-cargados (se muestra solo si no hay data de old ni de relaciones) --}}
    @if(count($adulto->grupoFamiliar ?? []) == 0 && !old('familiares'))
        <div class="familiar-group border p-3 mb-3">
            <h6>Familiar #1</h6>
            <div class="row">
                <div class="col-md-6 mb-2">
                    <label>Apellido Paterno</label>
                    <input type="text" name="familiares[0][apellido_paterno]" class="form-control" value="{{ old('familiares.0.apellido_paterno') }}">
                </div>
                <div class="col-md-6 mb-2">
                    <label>Apellido Materno</label>
                    <input type="text" name="familiares[0][apellido_materno]" class="form-control" value="{{ old('familiares.0.apellido_materno') }}">
                </div>
                <div class="col-md-6 mb-2">
                    <label>Nombres</label>
                    <input type="text" name="familiares[0][nombres]" class="form-control" value="{{ old('familiares.0.nombres') }}">
                </div>
                <div class="col-md-6 mb-2">
                    <label>Parentesco</label>
                    <input type="text" name="familiares[0][parentesco]" class="form-control" value="{{ old('familiares.0.parentesco') }}">
                </div>
                <div class="col-md-4 mb-2">
                    <label>Edad</label>
                    <input type="number" name="familiares[0][edad]" class="form-control" value="{{ old('familiares.0.edad') }}">
                </div>
                <div class="col-md-4 mb-2">
                    <label>Ocupación</label>
                    <input type="text" name="familiares[0][ocupacion]" class="form-control" value="{{ old('familiares.0.ocupacion') }}">
                </div>
                <div class="col-md-4 mb-2">
                    <label>Teléfono</label>
                    <input type="text" name="familiares[0][telefono]" class="form-control" value="{{ old('familiares.0.telefono') }}">
                </div>
                <div class="col-md-12 mb-2">
                    <label>Dirección</label>
                    <input type="text" name="familiares[0][direccion]" class="form-control" value="{{ old('familiares.0.direccion') }}">
                </div>
            </div>
            {{-- El primer familiar por defecto no debe tener un botón de eliminar si es el único --}}
            <button type="button" class="btn btn-danger btn-sm mt-2 remove-familiar-btn" style="display:none;" onclick="this.closest('.familiar-group').remove()">Eliminar</button>
        </div>
    @endif
</div>

{{-- Botón para añadir un nuevo familiar --}}
<button type="button" class="btn btn-primary mt-3" id="add-familiar-btn">+ Agregar familiar</button>

<script>
    // Inicializa el contador con la cantidad de elementos existentes (o 0 si no hay)
    let familiarCounter = document.querySelectorAll('#familiares-container .familiar-group').length;

    document.getElementById('add-familiar-btn').addEventListener('click', function() {
        const container = document.getElementById('familiares-container');
        const nuevo = document.createElement('div');
        nuevo.className = 'familiar-group border p-3 mb-3';
        nuevo.innerHTML = `
            <h6>Familiar #${familiarCounter + 1}</h6>
            <input type="hidden" name="familiares[${familiarCounter}][id_familiar]" value=""> {{-- ID vacío para nuevos registros --}}
            <div class="row">
                <div class="col-md-6 mb-2">
                    <label>Apellido Paterno</label>
                    <input type="text" name="familiares[${familiarCounter}][apellido_paterno]" class="form-control">
                </div>
                <div class="col-md-6 mb-2">
                    <label>Apellido Materno</label>
                    <input type="text" name="familiares[${familiarCounter}][apellido_materno]" class="form-control">
                </div>
                <div class="col-md-6 mb-2">
                    <label>Nombres</label>
                    <input type="text" name="familiares[${familiarCounter}][nombres]" class="form-control">
                </div>
                <div class="col-md-6 mb-2">
                    <label>Parentesco</label>
                    <input type="text" name="familiares[${familiarCounter}][parentesco]" class="form-control">
                </div>
                <div class="col-md-4 mb-2">
                    <label>Edad</label>
                    <input type="number" name="familiares[${familiarCounter}][edad]" class="form-control">
                </div>
                <div class="col-md-4 mb-2">
                    <label>Ocupación</label>
                    <input type="text" name="familiares[${familiarCounter}][ocupacion]" class="form-control">
                </div>
                <div class="col-md-4 mb-2">
                    <label>Teléfono</label>
                    <input type="text" name="familiares[${familiarCounter}][telefono]" class="form-control">
                </div>
                <div class="col-md-12 mb-2">
                    <label>Dirección</label>
                    <input type="text" name="familiares[${familiarCounter}][direccion]" class="form-control">
                </div>
            </div>
            <button type="button" class="btn btn-danger btn-sm mt-2" onclick="this.closest('.familiar-group').remove()">Eliminar</button>
        `;
        container.appendChild(nuevo);
        familiarCounter++;

        // Asegurarse de que el botón "Eliminar" en el primer elemento (si existía) se muestre
        const firstFamiliarBtn = document.querySelector('#familiares-container .familiar-group .remove-familiar-btn');
        if (firstFamiliarBtn) {
            firstFamiliarBtn.style.display = 'block';
        }
    });

    // Muestra el botón de eliminar si hay más de un elemento inicial (en el caso de que la plantilla por defecto se muestre)
    document.addEventListener('DOMContentLoaded', function() {
        const defaultFamiliarGroup = document.querySelector('#familiares-container .familiar-group .remove-familiar-btn');
        if (defaultFamiliarGroup && familiarCounter > 0) { // Si existe la plantilla por defecto y hay al menos un familiar
            defaultFamiliarGroup.style.display = 'block';
        }
    });
</script>
