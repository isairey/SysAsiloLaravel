{{-- TAB 1: HISTORIA CLÍNICA --}}
{{-- El control de visibilidad de la pestaña se maneja desde registrarHistoriaClinica.blade.php --}}

    <div class="form-group">
        <label for="municipio_nombre" class="form-label">Municipio:</label>
        <input type="text" class="full-width-input" id="municipio_nombre" name="municipio_nombre" value="{{ old('municipio_nombre', $historiaClinica->municipio_nombre ?? '') }}">
    </div>
    <div class="form-group">
        <label for="establecimiento" class="form-label">Establecimiento:</label>
        <input type="text" class="full-width-input" id="establecimiento" name="establecimiento" value="{{ old('establecimiento', $historiaClinica->establecimiento ?? '') }}">
    </div>

    <h4 class="mt-4 mb-3">Datos Personales del Adulto Mayor</h4>
    <div class="form-grid-2-col">
        <div class="form-group">
            <label class="form-label">Apellido Paterno:</label>
            <p class="form-static-value">{{ optional($adulto->persona)->primer_apellido }}</p>
        </div>
        <div class="form-group">
            <label class="form-label">Apellido Materno:</label>
            <p class="form-static-value">{{ optional($adulto->persona)->segundo_apellido }}</p>
        </div>
        <div class="form-group">
            <label class="form-label">Nombres:</label>
            <p class="form-static-value">{{ optional($adulto->persona)->nombres }}</p>
        </div>
        <div class="form-group">
            <label class="form-label">Sexo:</label>
            <p class="form-static-value">{{ optional($adulto->persona)->sexo }}</p>
        </div>
    </div>
    <div class="form-grid-2-col">
        <div class="form-group">
            <label class="form-label">Edad:</label>
            <p class="form-static-value">{{ optional($adulto->persona)->edad }}</p>
        </div>
        {{-- CAMBIALO A UN RADIOBUTTON --}}
        <div class="form-group">
            <label1 class="form-label">Consulta:</label1>
            <div class="radio-group">
                <div style="padding-left: 100px">
                    <input type="radio" id="consultaN" name="tipo_consulta" value="N"
                            {{ (old('tipo_consulta') == 'N' || (optional($historiaClinica)->tipo_consulta == 'N' && !old('tipo_consulta'))) ? 'checked' : '' }} >
                    <label1 for="consultaN">N</label1>
                </div>
                <div style="padding-left: 100px">
                    <input type="radio" id="consultaR" name="tipo_consulta" value="R"
                            {{ (old('tipo_consulta') == 'R' || (optional($historiaClinica)->tipo_consulta == 'R' && !old('tipo_consulta'))) ? 'checked' : '' }} >
                    <label1 for="consultaR">R</label1>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">Estado Civil:</label>
            <p class="form-static-value">{{ optional($adulto->persona)->estado_civil }}</p>
        </div>
        <div class="form-group">
            <label class="form-label">Ocupación:</label>
           <input type="text" class="full-width-input" id="ocupacion" name="ocupacion" value="{{ old('ocupacion', $historiaClinica->ocupacion ?? '') }}">
        </div>
        <div class="form-group">
            <label class="form-label">Fecha de Nacimiento:</label>
            <p class="form-static-value">{{ optional(optional($adulto->persona)->fecha_nacimiento)->format('d/m/Y') }}</p>
        </div>
    </div>
    <div class="form-grid-3-col">
        <div class="form-group">
            <label class="form-label">Domicilio:</label>
            <p class="form-static-value">{{ optional($adulto->persona)->domicilio }}</p>
        </div>
        <div class="form-group">
            <label class="form-label">Zona/Comunidad:</label>
            <p class="form-static-value">{{ optional($adulto->persona)->zona_comunidad }}</p>
        </div>
        <div class="form-group">
            <label for="domicilio_actual" class="form-label">Domicilio Actual:</label>
            <input type="text" class="full-width-input" id="domicilio_actual" name="domicilio_actual" value="{{ old('domicilio_actual', $historiaClinica->domicilio_actual ?? '') }}">
        </div>
        <div class="form-group">
            <label class="form-label">Teléfono:</label>
            <p class="form-static-value">{{ optional($adulto->persona)->telefono }}</p>
        </div>
    </div>

    <div class="form-group">
        <label for="antecedentes_personales" class="form-label">ANTECEDENTES PERSONALES:</label>
        <textarea class="full-width-input" id="antecedentes_personales" name="antecedentes_personales" rows="3">{{ old('antecedentes_personales', $historiaClinica->antecedentes_personales ?? '') }}</textarea>
    </div>
    <div class="form-group">
        <label for="antecedentes_familiares" class="form-label">ANTECEDENTES FAMILIARES:</label>
        <textarea class="full-width-input" id="antecedentes_familiares" name="antecedentes_familiares" rows="3">{{ old('antecedentes_familiares', $historiaClinica->antecedentes_familiares ?? '') }}</textarea>
    </div>
    <div class="form-group">
        <label for="estado_actual" class="form-label">ESTADO ACTUAL:</label>
        <textarea class="full-width-input" id="estado_actual" name="estado_actual" rows="3">{{ old('estado_actual', $historiaClinica->estado_actual ?? '') }}</textarea>
    </div>
    <div class="form-grid-2-col">
        <div class="form-group">
            <label for="grado_instruccion" class="form-label">Grado de Instrucción:</label>
            <input type="text" class="full-width-input" id="grado_instruccion" name="grado_instruccion" value="{{ old('grado_instruccion', $historiaClinica->grado_instruccion ?? '') }}">
        </div>
    </div>
    <div class="form-grid-2-col">
        <div class="form-group">
            <label for="lugar_nacimiento_provincia" class="form-label">Lugar de Nacimiento (Provincia):</label>
            <input type="text" class="full-width-input" id="lugar_nacimiento_provincia" name="lugar_nacimiento_provincia" value="{{ old('lugar_nacimiento_provincia', $historiaClinica->lugar_nacimiento_provincia ?? '') }}">
        </div>
        <div class="form-group">
            <label for="lugar_nacimiento_departamento" class="form-label">Lugar de Nacimiento (Departamento):</label>
            <input type="text" class="full-width-input" id="lugar_nacimiento_departamento" name="lugar_nacimiento_departamento" value="{{ old('lugar_nacimiento_departamento', $historiaClinica->lugar_nacimiento_departamento ?? '') }}">
        </div>
    </div>
