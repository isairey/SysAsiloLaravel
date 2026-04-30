{{-- Proteccion/tabs/croquis.blade.php --}}

<h4 class="text-xl font-semibold mb-4 text-gray-800">5. Croquis Del Domicilio O Lugar De Referencia Del Adulto Mayor</h4>

<div class="mb-4">
    <label for="nombre_denunciante" class="block text-sm font-medium text-gray-700">Nombres del Denunciante</label>
    <input type="text" id="nombre_denunciante" name="croquis[nombre_denunciante]"
        value="{{ old('croquis.nombre_denunciante', optional($adulto->croquis)->nombre_denunciante ?? '') }}"
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
    @error('croquis.nombre_denunciante')
        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
    @enderror
</div>

<div class="mb-4">
    <label for="apellidos_denunciante" class="block text-sm font-medium text-gray-700">Apellidos del Denunciante</label>
    <input type="text" id="apellidos_denunciante" name="croquis[apellidos_denunciante]"
        value="{{ old('croquis.apellidos_denunciante', optional($adulto->croquis)->apellidos_denunciante ?? '') }}"
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
    @error('croquis.apellidos_denunciante')
        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
    @enderror
</div>

<div class="mb-4">
    <label for="ci_denunciante" class="block text-sm font-medium text-gray-700">CI del Denunciante</label>
    <input type="text" id="ci_denunciante" name="croquis[ci_denunciante]"
        value="{{ old('croquis.ci_denunciante', optional($adulto->croquis)->ci_denunciante ?? '') }}"
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
    @error('croquis.ci_denunciante')
        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
    @enderror
</div>

<div class="mt-6 mb-4">
    <label for="image_file" class="block text-sm font-medium text-gray-700">Subir Imagen de Croquis</label>
    <input type="file" id="image_file" name="image_file" accept="image/*" class="mt-1 block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none">
    @error('image_file')
        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
    @enderror

    @php
        // Obtener la URL de la imagen actual si existe
        $currentImageUrl = optional($adulto->croquis)->ruta_imagen ? Storage::url($adulto->croquis->ruta_imagen) : null;
    @endphp

    <div class="mt-4 border border-gray-300 bg-gray-50 p-2 rounded-lg flex items-center justify-center overflow-hidden" style="width: 100%; max-width: 600px; height: 300px;">
        <img id="image_preview" src="{{ $currentImageUrl ?: asset('assets/images/brand/Croquis.png') }}" alt="Previsualización de Croquis" class="max-h-full max-w-full object-contain">
    </div>

    @if($currentImageUrl)
        <div class="mt-2 flex items-center">
            <input type="checkbox" id="remove_image" name="remove_image" value="1" class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
            <label for="remove_image" class="ml-2 block text-sm text-gray-900">Eliminar imagen existente</label>
        </div>
    @endif
</div>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        const imageFile = document.getElementById('image_file');
        const imagePreview = document.getElementById('image_preview');
        const removeImageCheckbox = document.getElementById('remove_image');

        if (imageFile) {
            imageFile.addEventListener('change', function(event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        imagePreview.src = e.target.result;
                        // Desmarcar "eliminar imagen" si se sube una nueva
                        if (removeImageCheckbox) {
                            removeImageCheckbox.checked = false;
                        }
                    };
                    reader.readAsDataURL(file);
                } else {
                    // Si no se selecciona un archivo, restaurar la imagen existente o el placeholder
                    imagePreview.src = "{{ $currentImageUrl ?: asset('assets/images/brand/Croquis.png') }}";
                }
            });
        }

        if (removeImageCheckbox) {
            removeImageCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    // Si se marca "eliminar imagen", limpiar la previsualización y el input de archivo
                    imagePreview.src = "{{ asset('assets/images/brand/Croquis.png') }}";
                    imageFile.value = ''; // Esto borra el archivo seleccionado en el input
                } else {
                    // Si se desmarca, restaurar la imagen existente o el placeholder (si el input de archivo está vacío)
                    if (!imageFile.value) { // Solo restaurar si no hay un nuevo archivo seleccionado
                         imagePreview.src = "{{ $currentImageUrl ?: asset('assets/images/brand/Croquis.png') }}";
                    }
                }
            });
        }
    });
</script>
