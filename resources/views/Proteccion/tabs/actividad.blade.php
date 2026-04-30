{{-- Proteccion/tabs/actividad.blade.php --}}
<h4>1. Actividad Laboral Renumerada De La Persona Adulta Mayor</h4>

<div class="mb-4">
    <label for="nombre_actividad" class="block text-sm font-medium text-gray-700">Nombre de la Actividad Laboral</label>
    <input type="text" id="nombre_actividad" name="nombre_actividad"
        value="{{ old('nombre_actividad', optional($adulto->actividadLaboral)->nombre_actividad ?? '') }}"
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
    @error('nombre_actividad')
        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
    @enderror
</div>

<div class="mb-4">
    <label for="direccion_trabajo" class="block text-sm font-medium text-gray-700">Dirección Habitual del Trabajo</label>
    <input type="text" id="direccion_trabajo" name="direccion_trabajo"
        value="{{ old('direccion_trabajo', optional($adulto->actividadLaboral)->direccion_trabajo ?? '') }}"
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
    @error('direccion_trabajo')
        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
    @enderror
</div>

<div class="mb-4">
    <label for="horario" class="block text-sm font-medium text-gray-700">Horario</label>
    <input type="text" id="horario" name="horario"
        value="{{ old('horario', optional($adulto->actividadLaboral)->horario ?? '') }}"
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
    @error('horario')
        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
    @enderror
</div>

<div class="mb-4">
    <label for="horas_x_dia" class="block text-sm font-medium text-gray-700">Horas de Trabajo por Día</label>
    <input type="text" id="horas_x_dia" name="horas_x_dia"
        value="{{ old('horas_x_dia', optional($adulto->actividadLaboral)->horas_x_dia ?? '') }}"
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
    @error('horas_x_dia')
        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
    @enderror
</div>

<div class="mb-4">
    <label for="rem_men_aprox" class="block text-sm font-medium text-gray-700">Remuneración Mensual Aproximada</label>
    <input type="text" id="rem_men_aprox" name="rem_men_aprox"
        value="{{ old('rem_men_aprox', optional($adulto->actividadLaboral)->rem_men_aprox ?? '') }}"
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
    @error('rem_men_aprox')
        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
    @enderror
</div>

<div class="mb-4">
    <label for="telefono_laboral" class="block text-sm font-medium text-gray-700">Teléfono</label>
    <input type="text" id="telefono_laboral" name="telefono_laboral"
        value="{{ old('telefono_laboral', optional($adulto->actividadLaboral)->telefono_laboral ?? '') }}"
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
    @error('telefono_laboral')
        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
    @enderror
</div>

<div style="margin-top: 20px; display: flex; justify-content: flex-end;">
    <!-- Input oculto para indicar al controlador que la pestaña fue omitida -->
    <input type="hidden" name="_skip_actividad_laboral" id="skipActividadLaboral" value="0">
    
    <button type="button" onclick="skipActividadTab()" 
            class="px-4 py-2 bg-yellow-500 text-gray-900 font-semibold rounded-lg shadow-md hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:ring-opacity-75">
        Omitir esta pestaña
    </button>
</div>

<script>
    /**
     * Función para omitir la pestaña de Actividad Laboral.
     * Establece un valor en un campo oculto y envía el formulario.
     */
    function skipActividadTab() {
        document.getElementById('skipActividadLaboral').value = '1';
        // Encuentra el formulario más cercano y envíalo
        const form = document.getElementById('actividad').querySelector('form');
        if (form) {
            form.submit();
        } else {
            console.error("No se pudo encontrar el formulario para la pestaña de Actividad Laboral.");
            // Considera usar un modal o mensaje en lugar de alert en un entorno de producción
            alert("Error: No se pudo procesar la omisión de la pestaña. Por favor, intente de nuevo.");
        }
    }
</script>
