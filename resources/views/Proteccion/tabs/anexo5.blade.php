{{-- TAB 9: Anexo N°5 --}}
<h4>9. Anexo Al Numeral V</h4>

<div id="anexo5-container">
    {{-- Verifica si hay anexos N5 existentes para precargar --}}
    {{-- *** CAMBIO: Usar $adulto->anexoN5 (singular) para acceder a la colección de la relación *** --}}
    @if(isset($adulto->anexoN5) && optional($adulto->anexoN5)->count() > 0)
        @foreach($adulto->anexoN5 as $index => $anexo5)
            {{-- Contenedor de un Anexo N5 existente --}}
            <div class="anexo5-group" style="border: 1px solid #ccc; padding: 10px; margin-bottom: 10px;">
                {{-- Input oculto para el ID del Anexo N5 existente --}}
                {{-- Usa el nro_an5 para identificar el registro --}}
                <input type="hidden" name="anexos_n5[{{ $index }}][nro_an5]" value="{{ old("anexos_n5.$index.nro_an5", optional($anexo5)->nro_an5 ?? '') }}">
                
                <h5>Anexo N5 Existente #{{ $index + 1 }}</h5>
                <div>
                    <label>Nro</label>
                    <input type="text" name="anexos_n5[{{ $index }}][numero]" value="{{ old("anexos_n5.$index.numero", optional($anexo5)->numero ?? '') }}">
                </div>

                <div>
                    <label>Fecha</label>
                    {{-- *** CAMBIO CRÍTICO AQUÍ: Formatear la fecha para input type="date" a YYYY-MM-DD *** --}}
                    <input type="date" name="anexos_n5[{{ $index }}][fecha]" value="{{ old("anexos_n5.$index.fecha", optional($anexo5->fecha)->format('Y-m-d') ?? '') }}">
                </div>

                <div>
                    <label>Acción Realizada</label>
                    <textarea name="anexos_n5[{{ $index }}][accion_realizada]" rows="3">{{ old("anexos_n5.$index.accion_realizada", optional($anexo5)->accion_realizada ?? '') }}</textarea>
                </div>

                <div>
                    <label>Resultado Obtenido</label>
                    <textarea name="anexos_n5[{{ $index }}][resultado_obtenido]" rows="3">{{ old("anexos_n5.$index.resultado_obtenido", optional($anexo5)->resultado_obtenido ?? '') }}</textarea>
                </div>

                <div>
                    <label>Nombre del/la Funcionario(a) que realizó la acción</label>
                    {{-- Muestra el nombre del funcionario que registró este Anexo N5 --}}
                    <input type="text" disabled value="{{ optional(optional(optional($anexo5)->usuario)->persona)->nombres }} {{ optional(optional(optional($anexo5)->usuario)->persona)->primer_apellido }} {{ optional(optional(optional($anexo5)->usuario)->persona)->segundo_apellido }}">
                </div>
                {{-- Botón para eliminar un Anexo N5 existente --}}
                <button type="button" class="btn btn-danger btn-sm mt-2" onclick="removeAnexo5(this)">Eliminar Anexo N5</button>
                
            </div>
        @endforeach
    @else
        {{-- Bloque inicial si no hay Anexos N5 existentes (para un nuevo caso) --}}
        <div class="anexo5-group" style="border: 1px solid #ccc; padding: 10px; margin-bottom: 10px;">
            <h5>Anexo N5 #1</h5>
            <input type="hidden" name="anexos_n5[0][nro_an5]" value=""> {{-- Nuevo registro, sin nro_an5 aún --}}
            <div>
                <label>Número</label>
                <input type="text" name="anexos_n5[0][numero]" value="{{ old('anexos_n5.0.numero', '') }}">
            </div>

            <div>
                <label>Fecha</label>
                <input type="date" name="anexos_n5[0][fecha]" value="{{ old('anexos_n5.0.fecha', '') }}">
            </div>

            <div>
                <label>Acción Realizada</label>
                <textarea name="anexos_n5[0][accion_realizada]" rows="3">{{ old('anexos_n5.0.accion_realizada', '') }}</textarea>
            </div>

            <div>
                <label>Resultado Obtenido</label>
                <textarea name="anexos_n5[0][resultado_obtenido]" rows="3">{{ old('anexos_n5.0.resultado_obtenido', '') }}</textarea>
            </div>

            <div>
                <label>Nombre del/la Funcionario(a) que Realizo la Accion</label>
                {{-- Muestra el nombre del usuario logueado actualmente --}}
                <input type="text" disabled value="{{ optional(auth()->user()->persona)->nombres }} {{ optional(auth()->user()->persona)->primer_apellido }} {{ optional(auth()->user()->persona)->segundo_apellido }}">
            </div>
        </div>
    @endif
</div>

{{-- Botón para agregar un nuevo bloque de Anexo N5 --}}
<button type="button" onclick="agregarAnexo5()" style="padding: 10px 15px; background-color: #28a745; color: white; border: none; border-radius: 5px; cursor: pointer;">+ Agregar otro Anexo N5</button>
<p><strong>Revisaste todos los pasos anteriores. Si todo está correcto, guarda el caso completo.</strong></p>

<script>
    // Inicializa el contadorAnexo5 con la cantidad de elementos ya existentes
    let contadorAnexo5 = document.querySelectorAll('.anexo5-group').length;

    function agregarAnexo5() {
        const container = document.getElementById('anexo5-container');
        const nuevo = document.createElement('div');
        nuevo.className = 'anexo5-group';
        nuevo.style.border = '1px solid #ccc';
        nuevo.style.padding = '10px';
        nuevo.style.marginBottom = '10px';
        nuevo.innerHTML = `
            <h5>Anexo N5 #${contadorAnexo5 + 1}</h5>
            <input type="hidden" name="anexos_n5[${contadorAnexo5}][nro_an5]" value="">
            <div>
                <label>Número</label>
                <input type="text" name="anexos_n5[${contadorAnexo5}][numero]">
            </div>
            <div>
                <label>Fecha</label>
                <input type="date" name="anexos_n5[${contadorAnexo5}][fecha]">
            </div>
            <div>
                <label>Acción Realizada</label>
                <textarea name="anexos_n5[${contadorAnexo5}][accion_realizada]" rows="3"></textarea>
            </div>
            <div>
                <label>Resultado Obtenido</label>
                <textarea name="anexos_n5[${contadorAnexo5}][resultado_obtenido]" rows="3"></textarea>
            </div>
            <div>
                <label>Funcionario</label>
                <input type="text" disabled value="{{ optional(auth()->user()->persona)->nombres }} {{ optional(auth()->user()->persona)->primer_apellido }} {{ optional(auth()->user()->persona)->segundo_apellido }}">
            </div>
            <button type="button" onclick="removeAnexo5(this)" style="padding: 8px 12px; background-color: #dc3545; color: white; border: none; border-radius: 5px; cursor: pointer; margin-top: 10px;">Eliminar Anexo N5</button>
        `;
        container.appendChild(nuevo);
        contadorAnexo5++;
    }

    function removeAnexo5(button) {
        const confirmDelete = confirm('¿Estás seguro de que quieres eliminar este Anexo N5?');
        if (confirmDelete) {
            const parentDiv = button.parentNode;
            parentDiv.remove();
            // Reindexar los nombres de los inputs después de eliminar un elemento
            reindexAnexo5Inputs();
        }
    }

    // Función para reindexar los nombres de los inputs después de una eliminación
    function reindexAnexo5Inputs() {
        const anexoGroups = document.querySelectorAll('.anexo5-group');
        anexoGroups.forEach((group, index) => {
            // Actualizar el número del título
            const h5 = group.querySelector('h5');
            if (h5) {
                h5.textContent = `Anexo N5 #${index + 1}`;
            }

            // Actualizar los atributos name de los inputs y textareas
            group.querySelectorAll('input, textarea').forEach(input => {
                const name = input.getAttribute('name');
                if (name && name.startsWith('anexos_n5[')) {
                    const newName = name.replace(/anexos_n5\[\d+\]/, `anexos_n5[${index}]`);
                    input.setAttribute('name', newName);
                }
            });
        });
        contadorAnexo5 = anexoGroups.length; // Actualiza el contador global
    }
</script>
