{{-- TAB 8: Anexo al Numeral III --}}
<h4>8. Anexo Al Numeral III</h4>
<div id="anexos3-container">
    {{-- Itera sobre los Anexos N3 existentes del adulto o datos antiguos para pre-llenar --}}
    {{-- Prioriza old() input, luego los datos del modelo, si no, un array vacío para que el bucle no falle --}}
    @foreach(old('anexos_n3', $adulto->anexoN3 ?? []) as $index => $anexo)
        <div class="anexo3-group border p-3 mb-3">
            <h5>Persona Natural #{{ $index + 1 }}</h5>
            {{-- Campo oculto para el ID de la Persona Natural asociada al Anexo N3. --}}
            {{-- Usamos data_get para obtener propiedades de objetos o arrays de forma segura --}}
            <input type="hidden" name="anexos_n3[{{ $index }}][id_natural]" value="{{ data_get($anexo, 'personaNatural.id_natural') }}">

            <div>
                <label>Primer Apellido</label>
                <input type="text" name="anexos_n3[{{ $index }}][primer_apellido]" class="form-control"
                    value="{{ old('anexos_n3.' . $index . '.primer_apellido', data_get($anexo, 'personaNatural.primer_apellido')) }}">
            </div>

            <div>
                <label>Segundo Apellido</label>
                <input type="text" name="anexos_n3[{{ $index }}][segundo_apellido]" class="form-control"
                    value="{{ old('anexos_n3.' . $index . '.segundo_apellido', data_get($anexo, 'personaNatural.segundo_apellido')) }}">
            </div>

            <div>
                <label>Nombres</label>
                <input type="text" name="anexos_n3[{{ $index }}][nombres]" class="form-control"
                    value="{{ old('anexos_n3.' . $index . '.nombres', data_get($anexo, 'personaNatural.nombres')) }}">
            </div>

            <div>
                <label>Sexo</label>
                <select name="anexos_n3[{{ $index }}][sexo]" class="form-control">
                    <option value="">Seleccione</option>
                    {{-- LEER SEXO DIRECTAMENTE DEL ANEXO N3, LUEGO DE PERSONA NATURAL --}}
                    {{-- data_get($anexo, 'sexo') primero intenta AnexoN3->sexo, luego si no, AnexoN3->personaNatural->sexo --}}
                    <option value="M" {{ (old('anexos_n3.' . $index . '.sexo', data_get($anexo, 'sexo') ?? data_get($anexo, 'personaNatural.sexo')) == 'M') ? 'selected' : '' }}>Masculino</option>
                    <option value="F" {{ (old('anexos_n3.' . $index . '.sexo', data_get($anexo, 'sexo') ?? data_get($anexo, 'personaNatural.sexo')) == 'F') ? 'selected' : '' }}>Femenino</option>
                </select>
            </div>

            <div>
                <label>Edad</label>
                <input type="number" name="anexos_n3[{{ $index }}][edad]" class="form-control"
                    value="{{ old('anexos_n3.' . $index . '.edad', data_get($anexo, 'personaNatural.edad')) }}">
            </div>

            <div>
                <label>CI</label>
                <input type="text" name="anexos_n3[{{ $index }}][ci]" class="form-control"
                    value="{{ old('anexos_n3.' . $index . '.ci', data_get($anexo, 'personaNatural.ci')) }}">
            </div>

            <div>
                <label>Teléfono</label>
                <input type="text" name="anexos_n3[{{ $index }}][telefono]" class="form-control"
                    value="{{ old('anexos_n3.' . $index . '.telefono', data_get($anexo, 'personaNatural.telefono')) }}">
            </div>

            <div>
                <label>Dirección Domicilio</label>
                <input type="text" name="anexos_n3[{{ $index }}][direccion_domicilio]" class="form-control"
                    value="{{ old('anexos_n3.' . $index . '.direccion_domicilio', data_get($anexo, 'personaNatural.direccion_domicilio')) }}">
            </div>

            <div>
                <label>Relación/Parentesco</label>
                <input type="text" name="anexos_n3[{{ $index }}][relacion_parentesco]" class="form-control"
                    value="{{ old('anexos_n3.' . $index . '.relacion_parentesco', data_get($anexo, 'personaNatural.relacion_parentesco')) }}">
            </div>

            <div>
                <label>Dirección de Trabajo</label>
                <input type="text" name="anexos_n3[{{ $index }}][direccion_de_trabajo]" class="form-control"
                    value="{{ old('anexos_n3.' . $index . '.direccion_de_trabajo', data_get($anexo, 'personaNatural.direccion_de_trabajo')) }}">
            </div>

            <div>
                <label>Ocupación</label>
                <input type="text" name="anexos_n3[{{ $index }}][ocupacion]" class="form-control"
                    value="{{ old('anexos_n3.' . $index . '.ocupacion', data_get($anexo, 'personaNatural.ocupacion')) }}">
            </div>
            <button type="button" class="botonEliminarAnexo3" onclick="this.closest('.anexo3-group').remove()">Eliminar Anexo 3</button>
        </div>
    @endforeach

    {{-- Bloque por defecto si no hay anexos para prellenar y no hay old input --}}
    @if(count($adulto->anexoN3 ?? []) == 0 && !old('anexos_n3'))
        <div class="anexo3-group border p-3 mb-3">
            <h5>Persona Natural #1</h5>
            <input type="hidden" name="anexos_n3[0][id_natural]" value="">
            <div>
                <label>Primer Apellido</label>
                <input type="text" name="anexos_n3[0][primer_apellido]" class="form-control" value="{{ old('anexos_n3.0.primer_apellido') }}">
            </div>

            <div>
                <label>Segundo Apellido</label>
                <input type="text" name="anexos_n3[0][segundo_apellido]" class="form-control" value="{{ old('anexos_n3.0.segundo_apellido') }}">
            </div>

            <div>
                <label>Nombres</label>
                <input type="text" name="anexos_n3[0][nombres]" class="form-control" value="{{ old('anexos_n3.0.nombres') }}">
            </div>

            <div>
                <label>Sexo</label>
                <select name="anexos_n3[0][sexo]" class="form-control">
                    <option value="">Seleccione</option>
                    <option value="M" {{ old('anexos_n3.0.sexo') == 'M' ? 'selected' : '' }}>Masculino</option>
                    <option value="F" {{ old('anexos_n3.0.sexo') == 'F' ? 'selected' : '' }}>Femenino</option>
                </select>
            </div>

            <div>
                <label>Edad</label>
                <input type="number" name="anexos_n3[0][edad]" class="form-control" value="{{ old('anexos_n3.0.edad') }}">
            </div>

            <div>
                <label>CI</label>
                <input type="text" name="anexos_n3[0][ci]" class="form-control" value="{{ old('anexos_n3.0.ci') }}">
            </div>

            <div>
                <label>Teléfono</label>
                <input type="text" name="anexos_n3[0][telefono]" class="form-control" value="{{ old('anexos_n3.0.telefono') }}">
            </div>

            <div>
                <label>Dirección Domicilio</label>
                <input type="text" name="anexos_n3[0][direccion_domicilio]" class="form-control" value="{{ old('anexos_n3.0.direccion_domicilio') }}">
            </div>

            <div>
                <label>Relación/Parentesco</label>
                <input type="text" name="anexos_n3[0][relacion_parentesco]" class="form-control" value="{{ old('anexos_n3.0.relacion_parentesco') }}">
            </div>

            <div>
                <label>Dirección de Trabajo</label>
                <input type="text" name="anexos_n3[0][direccion_de_trabajo]" class="form-control" value="{{ old('anexos_n3.0.direccion_de_trabajo') }}">
            </div>

            <div>
                <label>Ocupación</label>
                <input type="text" name="anexos_n3[0][ocupacion]" class="form-control" value="{{ old('anexos_n3.0.ocupacion') }}">
            </div>
        </div>
    @endif
</div>

<button type="button" class="btn btn-primary mt-3" id="add-anexo3-btn">+ Agregar persona</button>

<script>
    // Inicializa el contador con la cantidad de elementos existentes (o 0 si no hay)
    let anexo3Counter = document.querySelectorAll('#anexos3-container .anexo3-group').length;

    document.getElementById('add-anexo3-btn').addEventListener('click', function() {
        const container = document.getElementById('anexos3-container');
        const nuevo = document.createElement('div');
        nuevo.className = 'anexo3-group border p-3 mb-3';
        nuevo.innerHTML = `
            <h5>Persona Natural #${anexo3Counter + 1}</h5>
            <input type="hidden" name="anexos_n3[${anexo3Counter}][id_natural]" value="">
            <div>
                <label>Primer Apellido</label>
                <input type="text" name="anexos_n3[${anexo3Counter}][primer_apellido]" class="form-control">
            </div>
            <div>
                <label>Segundo Apellido</label>
                <input type="text" name="anexos_n3[${anexo3Counter}][segundo_apellido]" class="form-control">
            </div>
            <div>
                <label>Nombres</label>
                <input type="text" name="anexos_n3[${anexo3Counter}][nombres]" class="form-control">
            </div>
            <div>
                <label>Sexo</label>
                <select name="anexos_n3[${anexo3Counter}][sexo]" class="form-control">
                    <option value="">Seleccione</option>
                    <option value="M">Masculino</option>
                    <option value="F">Femenino</option>
                </select>
            </div>
            <div>
                <label>Edad</label>
                <input type="number" name="anexos_n3[${anexo3Counter}][edad]" class="form-control">
            </div>
            <div>
                <label>CI</label>
                <input type="text" name="anexos_n3[${anexo3Counter}][ci]" class="form-control">
            </div>
            <div>
                <label>Teléfono</label>
                <input type="text" name="anexos_n3[${anexo3Counter}][telefono]" class="form-control">
            </div>
            <div>
                <label>Dirección Domicilio</label>
                <input type="text" name="anexos_n3[${anexo3Counter}][direccion_domicilio]" class="form-control">
            </div>
            <div>
                <label>Relación/Parentesco</label>
                <input type="text" name="anexos_n3[${anexo3Counter}][relacion_parentesco]" class="form-control">
            </div>
            <div>
                <label>Dirección de Trabajo</label>
                <input type="text" name="anexos_n3[${anexo3Counter}][direccion_de_trabajo]" class="form-control">
            </div>
            <div>
                <label>Ocupación</label>
                <input type="text" name="anexos_n3[${anexo3Counter}][ocupacion]" class="form-control">
            </div>
            <button type="button" class="btn btn-danger btn-sm mt-2" onclick="this.closest('.anexo3-group').remove()">Eliminar</button>
        `;
        container.appendChild(nuevo);
        anexo3Counter++;
    });

    // Muestra el botón de eliminar si hay más de un elemento inicial (en el caso de que la plantilla por defecto se muestre)
    // Se ha corregido el selector para que coincida con .btn-danger
    document.addEventListener('DOMContentLoaded', function() {
        const defaultAnexo3Btn = document.querySelector('#anexos3-container .anexo3-group .btn-danger');
        if (defaultAnexo3Btn) { // Ya no es necesario anexo3Counter > 0 aquí, si existe el elemento ya es relevante
            defaultAnexo3Btn.style.display = 'block';
        }
    });
</script>
