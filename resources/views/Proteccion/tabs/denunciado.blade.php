<div class="denunciado-form">
    <h4>3. Datos del Ofensor(a) Denunciado(a)</h4>
    
    <div class="denunciado-fields">
        <div class="field-group">
            <label for="nombresDenunciado">Nombres</label>
            <input type="text" id="nombresDenunciado" name="denunciado_natural[nombres]" 
                value="{{ old('denunciado_natural.nombres', optional(optional($adulto->denunciado)->personaNatural)->nombres ?? '') }}">
        </div>

        <div class="field-group">
            <label for="primerApellidoDenunciado">Primer Apellido</label>
            <input type="text" id="primerApellidoDenunciado" name="denunciado_natural[primer_apellido]" 
                value="{{ old('denunciado_natural.primer_apellido', optional(optional($adulto->denunciado)->personaNatural)->primer_apellido ?? '') }}">
        </div>

        <div class="field-group">
            <label for="segundoApellidoDenunciado">Segundo Apellido</label>
            <input type="text" id="segundoApellidoDenunciado" name="denunciado_natural[segundo_apellido]" 
                value="{{ old('denunciado_natural.segundo_apellido', optional(optional($adulto->denunciado)->personaNatural)->segundo_apellido ?? '') }}">
        </div>

        <div class="field-group">
            <label for="sexoDenunciado">Sexo</label>
            <select id="sexoDenunciado" name="sexo">
                <option value="">Seleccione</option>
                <option value="M" {{ old('sexo', optional($adulto->denunciado)->sexo ?? '') === 'M' ? 'selected' : '' }}>Masculino</option>
                <option value="F" {{ old('sexo', optional($adulto->denunciado)->sexo ?? '') === 'F' ? 'selected' : '' }}>Femenino</option>
            </select>
        </div>

        <div class="field-group">
            <label for="edadDenunciado">Edad</label>
            <input type="number" id="edadDenunciado" name="denunciado_natural[edad]" 
                value="{{ old('denunciado_natural.edad', optional(optional($adulto->denunciado)->personaNatural)->edad ?? '') }}">
        </div>

        <div class="field-group">
            <label for="ciDenunciado">CI</label>
            <input type="text" id="ciDenunciado" name="denunciado_natural[ci]" 
                value="{{ old('denunciado_natural.ci', optional(optional($adulto->denunciado)->personaNatural)->ci ?? '') }}">
        </div>

        <div class="field-group">
            <label for="telefonoDenunciado">Teléfono</label>
            <input type="text" id="telefonoDenunciado" name="denunciado_natural[telefono]" 
                value="{{ old('denunciado_natural.telefono', optional(optional($adulto->denunciado)->personaNatural)->telefono ?? '') }}">
        </div>

        <div class="field-group">
            <label for="direccionDomicilioDenunciado">Dirección Domicilio (Comunidad)</label>
            <input type="text" id="direccionDomicilioDenunciado" name="denunciado_natural[direccion_domicilio]" 
                value="{{ old('denunciado_natural.direccion_domicilio', optional(optional($adulto->denunciado)->personaNatural)->direccion_domicilio ?? '') }}">
        </div>

        <div class="field-group">
            <label for="relacionParentescoDenunciado">Relación/Parentesco</label>
            <input type="text" id="relacionParentescoDenunciado" name="denunciado_natural[relacion_parentesco]" 
                value="{{ old('denunciado_natural.relacion_parentesco', optional(optional($adulto->denunciado)->personaNatural)->relacion_parentesco ?? '') }}">
        </div>

        <div class="field-group">
            <label for="direccionTrabajoDenunciado">Dirección de Trabajo</label>
            <input type="text" id="direccionTrabajoDenunciado" name="denunciado_natural[direccion_de_trabajo]" 
                value="{{ old('denunciado_natural.direccion_de_trabajo', optional(optional($adulto->denunciado)->personaNatural)->direccion_de_trabajo ?? '') }}">
        </div>

        <div class="field-group">
            <label for="ocupacionDenunciado">Ocupación</label>
            <input type="text" id="ocupacionDenunciado" name="denunciado_natural[ocupacion]" 
                value="{{ old('denunciado_natural.ocupacion', optional(optional($adulto->denunciado)->personaNatural)->ocupacion ?? '') }}">
        </div>

        <div class="field-group" style="grid-column: 1 / -1;">
            <label for="descripcionHechos">Descripción de los hechos</label>
            <textarea id="descripcionHechos" name="descripcion_hechos" rows="4">{{ old('descripcion_hechos', optional($adulto->denunciado)->descripcion_hechos ?? '') }}</textarea>
        </div>
    </div>
</div>