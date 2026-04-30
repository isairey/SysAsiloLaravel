{{-- TAB 2: Encargado --}}
{{--
    Sección para registrar o editar la información del Encargado.
    Permite seleccionar entre Persona Natural o Persona Jurídica.
--}}
<h4>2. Datos Del Informante</h4>
<div>
    <label>Tipo de Encargado</label><br>
    {{-- Radio button para Persona Natural --}}
    <label>
        <input type="radio" name="tipo_encargado" value="natural"
            onclick="toggleTipoEncargado('natural')"
            {{ old('tipo_encargado', optional($adulto->encargados)->tipo_encargado ?? 'natural') === 'natural' ? 'checked' : '' }}>
        Persona Natural
    </label>
    {{-- Radio button para Persona Jurídica --}}
    <label>
        <input type="radio" name="tipo_encargado" value="juridica"
            onclick="toggleTipoEncargado('juridica')"
            {{ old('tipo_encargado', optional($adulto->encargados)->tipo_encargado ?? '') === 'juridica' ? 'checked' : '' }}>
        Persona Jurídica
    </label>
    @error('tipo_encargado')
        <span style="color: red; font-size: 0.75em; font-style: italic;">{{ $message }}</span>
    @enderror
</div>

{{-- Campos para Persona Natural (inicialmente ocultos) --}}
<div id="naturalFields" style="display: none; margin-top: 10px;">
    <strong>Datos de Persona Natural</strong>

    <div>
        <label>Nombres</label>
        <input type="text" name="encargado_natural[nombres]"
            value="{{ old('encargado_natural.nombres', optional(optional($adulto->encargados)->personaNatural)->nombres ?? '') }}">
        @error('encargado_natural.nombres')
            <span style="color: red; font-size: 0.75em; font-style: italic;">{{ $message }}</span>
        @enderror
    </div>

    <div>
        <label>Primer Apellido</label>
        <input type="text" name="encargado_natural[primer_apellido]"
            value="{{ old('encargado_natural.primer_apellido', optional(optional($adulto->encargados)->personaNatural)->primer_apellido ?? '') }}">
        @error('encargado_natural.primer_apellido')
            <span style="color: red; font-size: 0.75em; font-style: italic;">{{ $message }}</span>
        @enderror
    </div>

    <div>
        <label>Segundo Apellido</label>
        <input type="text" name="encargado_natural[segundo_apellido]"
            value="{{ old('encargado_natural.segundo_apellido', optional(optional($adulto->encargados)->personaNatural)->segundo_apellido ?? '') }}">
        @error('encargado_natural.segundo_apellido')
            <span style="color: red; font-size: 0.75em; font-style: italic;">{{ $message }}</span>
        @enderror
    </div>

    <div>
        <label>Edad</label>
        <input type="number" name="encargado_natural[edad]"
            value="{{ old('encargado_natural.edad', optional(optional($adulto->encargados)->personaNatural)->edad ?? '') }}">
        @error('encargado_natural.edad')
            <span style="color: red; font-size: 0.75em; font-style: italic;">{{ $message }}</span>
        @enderror
    </div>

    <div>
        <label>CI</label>
        <input type="text" name="encargado_natural[ci]"
            value="{{ old('encargado_natural.ci', optional(optional($adulto->encargados)->personaNatural)->ci ?? '') }}">
        @error('encargado_natural.ci')
            <span style="color: red; font-size: 0.75em; font-style: italic;">{{ $message }}</span>
        @enderror
    </div>

    <div>
        <label>Teléfono</label>
        <input type="text" name="encargado_natural[telefono]"
            value="{{ old('encargado_natural.telefono', optional(optional($adulto->encargados)->personaNatural)->telefono ?? '') }}">
        @error('encargado_natural.telefono')
            <span style="color: red; font-size: 0.75em; font-style: italic;">{{ $message }}</span>
        @enderror
    </div>

    <div>
        <label>Dirección Domicilio (Comunidad)</label>
        <input type="text" name="encargado_natural[direccion_domicilio]"
            value="{{ old('encargado_natural.direccion_domicilio', optional(optional($adulto->encargados)->personaNatural)->direccion_domicilio ?? '') }}">
        @error('encargado_natural.direccion_domicilio')
            <span style="color: red; font-size: 0.75em; font-style: italic;">{{ $message }}</span>
        @enderror
    </div>

    <div>
        <label>Relación/Parentesco</label>
        <input type="text" name="encargado_natural[relacion_parentesco]"
            value="{{ old('encargado_natural.relacion_parentesco', optional(optional($adulto->encargados)->personaNatural)->relacion_parentesco ?? '') }}">
        @error('encargado_natural.relacion_parentesco')
            <span style="color: red; font-size: 0.75em; font-style: italic;">{{ $message }}</span>
        @enderror
    </div>

    <div>
        <label>Dirección de Trabajo</label>
        <input type="text" name="encargado_natural[direccion_de_trabajo]"
            value="{{ old('encargado_natural.direccion_de_trabajo', optional(optional($adulto->encargados)->personaNatural)->direccion_de_trabajo ?? '') }}">
        @error('encargado_natural.direccion_de_trabajo')
            <span style="color: red; font-size: 0.75em; font-style: italic;">{{ $message }}</span>
        @enderror
    </div>

    <div>
        <label>Ocupación</label>
        <input type="text" name="encargado_natural[ocupacion]"
            value="{{ old('encargado_natural.ocupacion', optional(optional($adulto->encargados)->personaNatural)->ocupacion ?? '') }}">
        @error('encargado_natural.ocupacion')
            <span style="color: red; font-size: 0.75em; font-style: italic;">{{ $message }}</span>
        @enderror
    </div>
</div>

{{-- Campos para Persona Jurídica (inicialmente ocultos) --}}
<div id="juridicaFields" style="display: none; margin-top: 10px;">
    <strong>Datos de Persona Jurídica</strong>

    <div>
        <label>Nombre de Institución</label>
        <input type="text" name="nombre_institucion"
            value="{{ old('nombre_institucion', optional(optional($adulto->encargados)->personaJuridica)->nombre_institucion ?? '') }}">
        @error('nombre_institucion')
            <span style="color: red; font-size: 0.75em; font-style: italic;">{{ $message }}</span>
        @enderror
    </div>

    <div>
        <label>Dirección</label>
        <input type="text" name="direccion"
            value="{{ old('direccion', optional(optional($adulto->encargados)->personaJuridica)->direccion ?? '') }}">
        @error('direccion')
            <span style="color: red; font-size: 0.75em; font-style: italic;">{{ $message }}</span>
        @enderror
    </div>

    <div>
        <label>Teléfono</label>
        <input type="text" name="telefono_juridica"
            value="{{ old('telefono_juridica', optional(optional($adulto->encargados)->personaJuridica)->telefono_juridica ?? '') }}">
        @error('telefono_juridica')
            <span style="color: red; font-size: 0.75em; font-style: italic;">{{ $message }}</span>
        @enderror
    </div>

    <div>
        <label>Nombre del Funcionario Responsable</label>
        <input type="text" name="nombre_funcionario"
            value="{{ old('nombre_funcionario', optional(optional($adulto->encargados)->personaJuridica)->nombre_funcionario ?? '') }}">
        @error('nombre_funcionario')
            <span style="color: red; font-size: 0.75em; font-style: italic;">{{ $message }}</span>
        @enderror
    </div>
</div>

<script>
    // Función para mostrar/ocultar campos según el tipo de encargado seleccionado
    function toggleTipoEncargado(tipo) {
        document.getElementById('naturalFields').style.display = (tipo === 'natural') ? 'block' : 'none';
        document.getElementById('juridicaFields').style.display = (tipo === 'juridica') ? 'block' : 'none';
    }

    // Al cargar el documento, inicializa la visualización de los campos
    document.addEventListener('DOMContentLoaded', () => {
        const selectedRadio = document.querySelector('input[name="tipo_encargado"]:checked');
        if (selectedRadio) {
            // Si hay un radio seleccionado (ya sea por old() o por datos existentes), activa la vista
            toggleTipoEncargado(selectedRadio.value);
        } else {
            // Si no hay ningún radio seleccionado (ej. al crear un caso por primera vez),
            // selecciona y muestra 'natural' por defecto.
            const defaultRadio = document.querySelector('input[name="tipo_encargado"][value="natural"]');
            if (defaultRadio) {
                defaultRadio.checked = true;
                toggleTipoEncargado('natural');
            }
        }
    });
</script>
