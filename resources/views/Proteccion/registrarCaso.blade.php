@php
    $modoEdicion = $modoEdicion ?? false;
    $idAdulto = $adulto->id_adulto ?? null;
    $activeTab = $activeTab ?? (old('active_tab', session('active_tab', 'actividad')));
@endphp

@extends('layouts.main')

@section('content')
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $modoEdicion ? 'Editar' : 'Registrar' }} Caso</title>
    <link rel="stylesheet" href="{{ asset('css/tabs.css') }}">
    <link rel="stylesheet" href="{{ asset('css/Proteccion/RegistrarCaso.css') }}">
    <link rel="stylesheet" href="{{ asset('css/Proteccion/tabs/actividad.css') }}">
    <link rel="stylesheet" href="{{ asset('css/Proteccion/tabs/encargado.css') }}">
    <link rel="stylesheet" href="{{ asset('css/Proteccion/tabs/denunciado.css') }}">
    <link rel="stylesheet" href="{{ asset('css/Proteccion/tabs/grupo.css') }}">
    <link rel="stylesheet" href="{{ asset('css/Proteccion/tabs/croquis.css') }}">
    <link rel="stylesheet" href="{{ asset('css/Proteccion/tabs/seguimiento.css') }}">
    <link rel="stylesheet" href="{{ asset('css/Proteccion/tabs/intervencion.css') }}">
    <link rel="stylesheet" href="{{ asset('css/Proteccion/tabs/anexoN3.css') }}">
    <link rel="stylesheet" href="{{ asset('css/Proteccion/tabs/anexoN5.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="navigation-buttons">
        <a href="{{ route('legal.caso.index') }}">Volver al listado</a>
    </div>
    <h6>
        @if($modoEdicion)
            Editar Caso de: {{ optional($adulto->persona)->nombres }} {{ optional($adulto->persona)->primer_apellido }} {{ optional($adulto->persona)->segundo_apellido }}
        @else
            Registrar Nuevo Caso para: {{ optional($adulto->persona)->nombres }} {{ optional($adulto->persona)->primer_apellido }} {{ optional($adulto->persona)->segundo_apellido }}
        @endif
    </h6>

    @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    icon: 'success',
                    title: 'Éxito',
                    html: '<p>{{ session('success') }}</p>',
                    confirmButtonText: 'Aceptar'
                });
            });
        </script>
    @endif
    @if($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Errores en el formulario',
                    html: '<ul style="text-align: left;">' + @json($errors->all()).map(error => `<li>${error}</li>`).join('') + '</ul>',
                    confirmButtonText: 'Corregir'
                });
            });
        </script>
    @endif

    <ul id="formTabs">
        <li><a class="tab-link {{ $activeTab == 'actividad' ? 'active' : '' }}" href="{{ route('legal.caso.edit', ['id_adulto' => $adulto->id_adulto, 'active_tab' => 'actividad']) }}">1. Actividad</a></li>
        <li><a class="tab-link {{ $activeTab == 'encargado' ? 'active' : '' }}" href="{{ route('legal.caso.edit', ['id_adulto' => $adulto->id_adulto, 'active_tab' => 'encargado']) }}">2. Encargado</a></li>
        <li><a class="tab-link {{ $activeTab == 'denunciado' ? 'active' : '' }}" href="{{ route('legal.caso.edit', ['id_adulto' => $adulto->id_adulto, 'active_tab' => 'denunciado']) }}">3. Denunciado</a></li>
        <li><a class="tab-link {{ $activeTab == 'grupo' ? 'active' : '' }}" href="{{ route('legal.caso.edit', ['id_adulto' => $adulto->id_adulto, 'active_tab' => 'grupo']) }}">4. Grupo Familiar</a></li>
        <li><a class="tab-link {{ $activeTab == 'croquis' ? 'active' : '' }}" href="{{ route('legal.caso.edit', ['id_adulto' => $adulto->id_adulto, 'active_tab' => 'croquis']) }}">5. Croquis</a></li>
        <li><a class="tab-link {{ $activeTab == 'seguimiento' ? 'active' : '' }}" href="{{ route('legal.caso.edit', ['id_adulto' => $adulto->id_adulto, 'active_tab' => 'seguimiento']) }}">6. Seguimiento</a></li>
        <li><a class="tab-link {{ $activeTab == 'intervencion' ? 'active' : '' }}" href="{{ route('legal.caso.edit', ['id_adulto' => $adulto->id_adulto, 'active_tab' => 'intervencion']) }}">7. Intervención</a></li>
        <li><a class="tab-link {{ $activeTab == 'anexo3' ? 'active' : '' }}" href="{{ route('legal.caso.edit', ['id_adulto' => $adulto->id_adulto, 'active_tab' => 'anexo3']) }}">8. Anexo N3</a></li>
        <li><a class="tab-link {{ $activeTab == 'anexo5' ? 'active' : '' }}" href="{{ route('legal.caso.edit', ['id_adulto' => $adulto->id_adulto, 'active_tab' => 'anexo5']) }}">9. Anexo N5</a></li>
    </ul>

    <div class="tab-content">
        <div class="tab-pane {{ $activeTab == 'actividad' ? 'active' : '' }}" id="actividad">
            <form action="{{ route('legal.caso.storeActividad', $adulto->id_adulto) }}" method="POST">
                @csrf
                <input type="hidden" name="active_tab" value="actividad">
                @include('Proteccion.tabs.actividad', ['adulto' => $adulto, 'modoEdicion' => $modoEdicion])
                <div class="next-act">
                    <button type="submit">Siguiente →</button>
                </div>
            </form>
        </div>

        <div class="tab-pane {{ $activeTab == 'encargado' ? 'active' : '' }}" id="encargado">
            <form action="{{ route('legal.caso.storeEncargado', $adulto->id_adulto) }}" method="POST">
                @csrf
                <input type="hidden" name="active_tab" value="encargado">
                @include('Proteccion.tabs.encargado', ['adulto' => $adulto, 'modoEdicion' => $modoEdicion])
                <div class="navigation-buttons">
                    <a href="{{ route('legal.caso.edit', ['id_adulto' => $adulto->id_adulto, 'active_tab' => 'actividad']) }}">← Anterior</a>
                    <button type="submit">Siguiente →</button>
                </div>
            </form>
        </div>

        <div class="tab-pane {{ $activeTab == 'denunciado' ? 'active' : '' }}" id="denunciado">
            <form action="{{ route('legal.caso.storeDenunciado', $adulto->id_adulto) }}" method="POST">
                @csrf
                <input type="hidden" name="active_tab" value="denunciado">
                @include('Proteccion.tabs.denunciado', ['adulto' => $adulto, 'modoEdicion' => $modoEdicion])
                <div class="navigation-buttons">
                    <a href="{{ route('legal.caso.edit', ['id_adulto' => $adulto->id_adulto, 'active_tab' => 'encargado']) }}">← Anterior</a>
                    <button type="submit">Siguiente →</button>
                </div>
            </form>
        </div>

        <div class="tab-pane {{ $activeTab == 'grupo' ? 'active' : '' }}" id="grupo">
            <form action="{{ route('legal.caso.storeGrupoFamiliar', $adulto->id_adulto) }}" method="POST">
                @csrf
                <input type="hidden" name="active_tab" value="grupo">
                @include('Proteccion.tabs.grupo', ['adulto' => $adulto, 'modoEdicion' => $modoEdicion])
                <div class="navigation-buttons">
                    <a href="{{ route('legal.caso.edit', ['id_adulto' => $adulto->id_adulto, 'active_tab' => 'denunciado']) }}">← Anterior</a>
                    <button type="submit">Siguiente →</button>
                </div>
            </form>
        </div>

        <div class="tab-pane {{ $activeTab == 'croquis' ? 'active' : '' }}" id="croquis">
            <form action="{{ route('legal.caso.storeCroquis', $adulto->id_adulto) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="active_tab" value="croquis">
                @include('Proteccion.tabs.croquis', ['adulto' => $adulto, 'modoEdicion' => $modoEdicion])
                <div class="navigation-buttons">
                    <a href="{{ route('legal.caso.edit', ['id_adulto' => $adulto->id_adulto, 'active_tab' => 'grupo']) }}">← Anterior</a>
                    <button type="submit">Siguiente →</button>
                </div>
            </form>
        </div>

        <div class="tab-pane {{ $activeTab == 'seguimiento' ? 'active' : '' }}" id="seguimiento">
            <form action="{{ route('legal.caso.storeSeguimiento', $adulto->id_adulto) }}" method="POST">
                @csrf
                <input type="hidden" name="active_tab" value="seguimiento">
                @include('Proteccion.tabs.seguimiento', ['adulto' => $adulto, 'modoEdicion' => $modoEdicion])
                <div class="navigation-buttons">
                    <a href="{{ route('legal.caso.edit', ['id_adulto' => $adulto->id_adulto, 'active_tab' => 'croquis']) }}">← Anterior</a>
                    <button type="submit">Siguiente →</button>
                </div>
            </form>
        </div>

        <div class="tab-pane {{ $activeTab == 'intervencion' ? 'active' : '' }}" id="intervencion">
            <form action="{{ route('legal.caso.storeIntervencion', $adulto->id_adulto) }}" method="POST">
                @csrf
                <input type="hidden" name="active_tab" value="intervencion">
                @include('Proteccion.tabs.intervencion', ['adulto' => $adulto, 'modoEdicion' => $modoEdicion])
                <div class="navigation-buttons">
                    <a href="{{ route('legal.caso.edit', ['id_adulto' => $adulto->id_adulto, 'active_tab' => 'seguimiento']) }}">← Anterior</a>
                    <button type="submit">Siguiente →</button>
                </div>
            </form>
        </div>

        <div class="tab-pane {{ $activeTab == 'anexo3' ? 'active' : '' }}" id="anexo3">
            <form action="{{ route('legal.caso.storeAnexoN3', $adulto->id_adulto) }}" method="POST">
                @csrf
                <input type="hidden" name="active_tab" value="anexo3">
                @include('Proteccion.tabs.anexo3', ['adulto' => $adulto, 'modoEdicion' => $modoEdicion])
                <div class="navigation-buttons">
                    <a href="{{ route('legal.caso.edit', ['id_adulto' => $adulto->id_adulto, 'active_tab' => 'intervencion']) }}">← Anterior</a>
                    <button type="submit">Siguiente →</button>
                </div>
            </form>
        </div>

        <div class="tab-pane {{ $activeTab == 'anexo5' ? 'active' : '' }}" id="anexo5">
            <form action="{{ route('legal.caso.storeAnexoN5', $adulto->id_adulto) }}" method="POST">
                @csrf
                <input type="hidden" name="active_tab" value="anexo5">
                @include('Proteccion.tabs.anexo5', ['adulto' => $adulto, 'modoEdicion' => $modoEdicion])
                <div class="navigation-buttons">
                    <a href="{{ route('legal.caso.edit', ['id_adulto' => $adulto->id_adulto, 'active_tab' => 'anexo3']) }}">← Anterior</a>
                    <button type="submit" id="btnGuardarFinal">Guardar Registro</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const tabLinks = document.querySelectorAll('.tab-link');
            const tabPanes = document.querySelectorAll('.tab-pane');

            function showTab(targetId) {
                tabLinks.forEach(link => {
                    const linkHref = link.getAttribute('href');
                    if (linkHref && linkHref.indexOf(`active_tab=${targetId}`) > -1) {
                        link.classList.add('active');
                    } else {
                        link.classList.remove('active');
                    }
                });

                tabPanes.forEach(pane => {
                    if (pane.id === targetId) {
                        pane.classList.add('active');
                    } else {
                        pane.classList.remove('active');
                    }
                });
            }

            const initialTabId = "{{ $activeTab }}";
            showTab(initialTabId);
        });

        document.addEventListener('DOMContentLoaded', function () {
            // Función para validar formato de CI (números y guiones o solo un guion)
            function validarCI(ci) {
                return ci === '-' || /^[0-9-]{1,20}$/.test(ci);
            }

            // Función para validar formato de teléfono (números y guiones o solo un guion)
            function validarTelefono(telefono) {
                return telefono === '-' || /^[0-9-]{0,20}$/.test(telefono);
            }

            // Función para validar edad (número entre 0-120 o guion)
            function validarEdad(edad) {
                return edad === '-' || (/^[0-9]+$/.test(edad) && parseInt(edad) >= 0 && parseInt(edad) <= 120);
            }

            // Función para validar fecha (formato válido o guion)
            function validarFecha(fecha) {
                return fecha === '-' || !isNaN(Date.parse(fecha));
            }

            // Asignar validaciones a cada formulario según la pestaña
            const forms = document.querySelectorAll('form');
            forms.forEach(form => {
                form.addEventListener('submit', function (event) {
                    let errors = [];
                    const activeTab = form.querySelector('input[name="active_tab"]').value;

                    // Tab 1: Actividad Laboral
                    if (activeTab === 'actividad') {
                        const skipActividad = document.getElementById('skipActividadLaboral')?.value === '1';
                        if (!skipActividad) {
                            const actividadFields = [
                                { id: 'nombre_actividad', label: 'Nombre de la Actividad Laboral' },
                                { id: 'direccion_trabajo', label: 'Dirección Habitual del Trabajo' },
                                { id: 'horario', label: 'Horario' },
                                { id: 'horas_x_dia', label: 'Horas de Trabajo por Día' },
                                { id: 'rem_men_aprox', label: 'Remuneración Mensual Aproximada' },
                                { id: 'telefono_laboral', label: 'Teléfono Laboral' }
                            ];
                            actividadFields.forEach(field => {
                                const input = document.getElementById(field.id);
                                if (input && input.value.trim() !== '' && input.value !== '-') {
                                    if (field.id === 'telefono_laboral' && !validarTelefono(input.value.trim())) {
                                        errors.push(`El campo "${field.label}" debe contener solo números y guiones (máx. 20 caracteres) o un guion (-).`);
                                    }
                                }
                            });
                        }
                    }

                    // Tab 2: Encargado
                    if (activeTab === 'encargado') {
                        const tipoEncargado = form.querySelector('input[name="tipo_encargado"]:checked')?.value;
                        if (!tipoEncargado) {
                            errors.push('Debe seleccionar un tipo de encargado (Persona Natural o Jurídica).');
                        } else if (tipoEncargado === 'natural') {
                            const naturalFields = [
                                { name: 'encargado_natural[nombres]', label: 'Nombres', required: true },
                                { name: 'encargado_natural[primer_apellido]', label: 'Primer Apellido', required: true },
                                { name: 'encargado_natural[segundo_apellido]', label: 'Segundo Apellido' },
                                { name: 'encargado_natural[ci]', label: 'CI' },
                                { name: 'encargado_natural[edad]', label: 'Edad', required: true },
                                { name: 'encargado_natural[telefono]', label: 'Teléfono' },
                                { name: 'encargado_natural[direccion_domicilio]', label: 'Dirección Domicilio' },
                                { name: 'encargado_natural[relacion_parentesco]', label: 'Relación/Parentesco' },
                                { name: 'encargado_natural[direccion_de_trabajo]', label: 'Dirección de Trabajo' },
                                { name: 'encargado_natural[ocupacion]', label: 'Ocupación' }
                            ];
                            naturalFields.forEach(field => {
                                const input = form.querySelector(`input[name="${field.name}"]`);
                                if (input) {
                                    if (field.required && input.value.trim() === '' && input.value !== '-') {
                                        errors.push(`El campo "${field.label}" es obligatorio. Ingrese un valor o un guion (-).`);
                                    } else if (input.value.trim() !== '' && input.value !== '-') {
                                        if (field.name === 'encargado_natural[ci]' && !validarCI(input.value.trim())) {
                                            errors.push(`El campo "${field.label}" debe contener solo números y guiones (máx. 20 caracteres) o un guion (-).`);
                                        } else if (field.name === 'encargado_natural[telefono]' && !validarTelefono(input.value.trim())) {
                                            errors.push(`El campo "${field.label}" debe contener solo números y guiones (máx. 20 caracteres) o un guion (-).`);
                                        } else if (field.name === 'encargado_natural[edad]' && !validarEdad(input.value.trim())) {
                                            errors.push(`El campo "${field.label}" debe ser un número entre 0 y 120 o un guion (-).`);
                                        }
                                    }
                                }
                            });
                        } else if (tipoEncargado === 'juridica') {
                            const juridicaFields = [
                                { name: 'nombre_institucion', label: 'Nombre de Institución', required: true },
                                { name: 'direccion', label: 'Dirección', required: true },
                                { name: 'telefono_juridica', label: 'Teléfono', required: true },
                                { name: 'nombre_funcionario', label: 'Nombre del Funcionario Responsable', required: true }
                            ];
                            juridicaFields.forEach(field => {
                                const input = form.querySelector(`input[name="${field.name}"]`);
                                if (input) {
                                    if (field.required && input.value.trim() === '' && input.value !== '-') {
                                        errors.push(`El campo "${field.label}" es obligatorio. Ingrese un valor o un guion (-).`);
                                    } else if (input.value.trim() !== '' && input.value !== '-') {
                                        if (field.name === 'telefono_juridica' && !validarTelefono(input.value.trim())) {
                                            errors.push(`El campo "${field.label}" debe contener solo números y guiones (máx. 20 caracteres) o un guion (-).`);
                                        }
                                    }
                                }
                            });
                        }
                    }

                    // Tab 3: Denunciado
                    if (activeTab === 'denunciado') {
                        const denunciadoFields = [
                            { name: 'denunciado_natural[nombres]', label: 'Nombres', required: true },
                            { name: 'denunciado_natural[primer_apellido]', label: 'Primer Apellido', required: true },
                            { name: 'denunciado_natural[segundo_apellido]', label: 'Segundo Apellido' },
                            { name: 'sexo', label: 'Sexo', required: true },
                            { name: 'denunciado_natural[edad]', label: 'Edad', required: true },
                            { name: 'denunciado_natural[ci]', label: 'CI' },
                            { name: 'denunciado_natural[telefono]', label: 'Teléfono' },
                            { name: 'denunciado_natural[direccion_domicilio]', label: 'Dirección Domicilio' },
                            { name: 'denunciado_natural[relacion_parentesco]', label: 'Relación/Parentesco' },
                            { name: 'denunciado_natural[direccion_de_trabajo]', label: 'Dirección de Trabajo' },
                            { name: 'denunciado_natural[ocupacion]', label: 'Ocupación' },
                            { name: 'descripcion_hechos', label: 'Descripción de los Hechos', required: true }
                        ];
                        denunciadoFields.forEach(field => {
                            const input = form.querySelector(`[name="${field.name}"]`);
                            if (input) {
                                if (field.required && input.value.trim() === '' && input.value !== '-') {
                                    errors.push(`El campo "${field.label}" es obligatorio. Ingrese un valor o un guion (-).`);
                                } else if (input.value.trim() !== '' && input.value !== '-') {
                                    if (field.name === 'denunciado_natural[edad]' && !validarEdad(input.value.trim())) {
                                        errors.push(`El campo "${field.label}" debe ser un número entre 0 y 120 o un guion (-).`);
                                    } else if (field.name === 'denunciado_natural[ci]' && !validarCI(input.value.trim())) {
                                        errors.push(`El campo "${field.label}" debe contener solo números y guiones (máx. 20 caracteres) o un guion (-).`);
                                    } else if (field.name === 'denunciado_natural[telefono]' && !validarTelefono(input.value.trim())) {
                                        errors.push(`El campo "${field.label}" debe contener solo números y guiones (máx. 20 caracteres) o un guion (-).`);
                                    } else if (field.name === 'sexo' && !['M', 'F', '-'].includes(input.value.trim())) {
                                        errors.push(`El campo "${field.label}" debe ser "M", "F", o un guion (-).`);
                                    }
                                }
                            }
                        });
                    }

                    // Tab 4: Grupo Familiar
                    if (activeTab === 'grupo') {
                        const familiares = form.querySelectorAll('#familiares-container .familiar-group');
                        if (familiares.length === 0) {
                            errors.push('Debe registrar al menos un familiar en Grupo Familiar.');
                        } else {
                            familiares.forEach((group, index) => {
                                const familiarFields = [
                                    { name: `familiares[${index}][apellido_paterno]`, label: 'Apellido Paterno', required: true },
                                    { name: `familiares[${index}][apellido_materno]`, label: 'Apellido Materno' },
                                    { name: `familiares[${index}][nombres]`, label: 'Nombres', required: true },
                                    { name: `familiares[${index}][parentesco]`, label: 'Parentesco', required: true },
                                    { name: `familiares[${index}][edad]`, label: 'Edad', required: true },
                                    { name: `familiares[${index}][ocupacion]`, label: 'Ocupación' },
                                    { name: `familiares[${index}][direccion]`, label: 'Dirección' },
                                    { name: `familiares[${index}][telefono]`, label: 'Teléfono' }
                                ];
                                familiarFields.forEach(field => {
                                    const input = group.querySelector(`input[name="${field.name}"]`);
                                    if (input) {
                                        if (field.required && input.value.trim() === '' && input.value !== '-') {
                                            errors.push(`El campo "${field.label}" del Familiar #${index + 1} es obligatorio. Ingrese un valor o un guion (-).`);
                                        } else if (input.value.trim() !== '' && input.value !== '-') {
                                            if (field.name.includes('[telefono]') && !validarTelefono(input.value.trim())) {
                                                errors.push(`El campo "${field.label}" del Familiar #${index + 1} debe contener solo números y guiones (máx. 20 caracteres) o un guion (-).`);
                                            } else if (field.name.includes('[edad]') && !validarEdad(input.value.trim())) {
                                                errors.push(`El campo "${field.label}" del Familiar #${index + 1} debe ser un número entre 0 y 120 o un guion (-).`);
                                            }
                                        }
                                    }
                                });
                            });
                        }
                    }

                    // Tab 5: Croquis
                    if (activeTab === 'croquis') {
                        const croquisFields = [
                            { id: 'nombre_denunciante', label: 'Nombres del Denunciante', required: true },
                            { id: 'apellidos_denunciante', label: 'Apellidos del Denunciante', required: true },
                            { id: 'ci_denunciante', label: 'CI del Denunciante', required: true }
                        ];
                        croquisFields.forEach(field => {
                            const input = document.getElementById(field.id);
                            if (input) {
                                if (field.required && input.value.trim() === '' && input.value !== '-') {
                                    errors.push(`El campo "${field.label}" es obligatorio. Ingrese un valor o un guion (-).`);
                                } else if (input.value.trim() !== '' && input.value !== '-') {
                                    if (field.id === 'ci_denunciante' && !validarCI(input.value.trim())) {
                                        errors.push(`El campo "${field.label}" debe contener solo números y guiones (máx. 20 caracteres) o un guion (-).`);
                                    }
                                }
                            }
                        });
                        const imageFile = document.getElementById('image_file');
                        const removeImage = document.getElementById('remove_image');
                        if (imageFile && imageFile.files.length > 0) {
                            const file = imageFile.files[0];
                            const validTypes = ['image/jpeg', 'image/png', 'image/gif'];
                            if (!validTypes.includes(file.type)) {
                                errors.push('La imagen del croquis debe ser JPG, PNG o GIF.');
                            }
                            if (file.size > 2 * 1024 * 1024) {
                                errors.push('La imagen del croquis no debe superar los 2MB.');
                            }
                        } else if (!removeImage || !removeImage.checked) {
                            const currentImage = document.getElementById('image_preview')?.src;
                            if (!currentImage || currentImage.includes('Croquis.png')) {
                                errors.push('Debe subir una imagen para el Croquis o tener una imagen existente.');
                            }
                        }
                    }

                    // Tab 6: Seguimiento
                    if (activeTab === 'seguimiento') {
                        const seguimientos = form.querySelectorAll('#seguimientos-container .seguimiento-group');
                        if (seguimientos.length === 0) {
                            errors.push('Debe registrar al menos un seguimiento en Seguimiento del Caso.');
                        } else {
                            seguimientos.forEach((group, index) => {
                                const seguimientoFields = [
                                    { name: `seguimientos[${index}][nro]`, label: 'Nro de Seguimiento', required: true },
                                    { name: `seguimientos[${index}][fecha]`, label: 'Fecha', required: true },
                                    { name: `seguimientos[${index}][accion_realizada]`, label: 'Acción Realizada', required: true },
                                    { name: `seguimientos[${index}][resultado_obtenido]`, label: 'Resultado Obtenido', required: true }
                                ];
                                seguimientoFields.forEach(field => {
                                    const input = group.querySelector(`[name="${field.name}"]`);
                                    if (input) {
                                        if (field.required && input.value.trim() === '' && input.value !== '-') {
                                            errors.push(`El campo "${field.label}" del Seguimiento #${index + 1} es obligatorio. Ingrese un valor o un guion (-).`);
                                        } else if (input.value.trim() !== '' && input.value !== '-') {
                                            if (field.name.includes('[fecha]') && !validarFecha(input.value.trim())) {
                                                errors.push(`El campo "${field.label}" del Seguimiento #${index + 1} debe ser una fecha válida o un guion (-).`);
                                            }
                                        }
                                    }
                                });
                            });
                        }
                    }

                    // Tab 7: Intervención
                    if (activeTab === 'intervencion') {
                        const intervencionFields = [
                            'intervencion[resuelto_descripcion]',
                            'intervencion[no_resultado]',
                            'intervencion[derivacion_institucion]',
                            'intervencion[der_seguimiento_legal]',
                            'intervencion[der_seguimiento_psi]',
                            'intervencion[der_resuelto_externo]',
                            'intervencion[der_noresuelto_externo]',
                            'intervencion[abandono_victima]',
                            'intervencion[resuelto_conciliacion_jio]'
                        ];
                        let hasIntervencionValue = false;
                        intervencionFields.forEach(name => {
                            const input = form.querySelector(`[name="${name}"]`);
                            if (input && input.value.trim() !== '' && input.value !== '-') {
                                hasIntervencionValue = true;
                            }
                        });
                        if (!hasIntervencionValue) {
                            errors.push('Debe completar al menos un campo en Intervención con información válida (no solo un guion).');
                        }
                        const fechaIntervencion = form.querySelector('input[name="intervencion[fecha_intervencion]"]');
                        if (fechaIntervencion && fechaIntervencion.value.trim() === '') {
                            errors.push('El campo "Fecha de Intervención" es obligatorio.');
                        } else if (fechaIntervencion && fechaIntervencion.value !== '-' && !validarFecha(fechaIntervencion.value.trim())) {
                            errors.push('El campo "Fecha de Intervención" debe ser una fecha válida o un guion (-).');
                        }
                    }

                    // Tab 8: Anexo N3
                    if (activeTab === 'anexo3') {
                        const anexosN3 = form.querySelectorAll('#anexos3-container .anexo3-group');
                        if (anexosN3.length === 0) {
                            errors.push('Debe registrar al menos una persona en Anexo N3.');
                        } else {
                            const cis = [];
                            anexosN3.forEach((group, index) => {
                                const anexoFields = [
                                    { name: `anexos_n3[${index}][primer_apellido]`, label: 'Primer Apellido', required: true },
                                    { name: `anexos_n3[${index}][segundo_apellido]`, label: 'Segundo Apellido' },
                                    { name: `anexos_n3[${index}][nombres]`, label: 'Nombres', required: true },
                                    { name: `anexos_n3[${index}][sexo]`, label: 'Sexo', required: true },
                                    { name: `anexos_n3[${index}][edad]`, label: 'Edad', required: true },
                                    { name: `anexos_n3[${index}][ci]`, label: 'CI', required: true },
                                    { name: `anexos_n3[${index}][telefono]`, label: 'Teléfono' },
                                    { name: `anexos_n3[${index}][direccion_domicilio]`, label: 'Dirección Domicilio' },
                                    { name: `anexos_n3[${index}][relacion_parentesco]`, label: 'Relación/Parentesco' },
                                    { name: `anexos_n3[${index}][direccion_de_trabajo]`, label: 'Dirección de Trabajo' },
                                    { name: `anexos_n3[${index}][ocupacion]`, label: 'Ocupación' }
                                ];
                                anexoFields.forEach(field => {
                                    const input = group.querySelector(`[name="${field.name}"]`);
                                    if (input) {
                                        if (field.required && input.value.trim() === '' && input.value !== '-') {
                                            errors.push(`El campo "${field.label}" de la Persona Natural #${index + 1} en Anexo N3 es obligatorio. Ingrese un valor o un guion (-).`);
                                        } else if (input.value.trim() !== '' && input.value !== '-') {
                                            if (field.name.includes('[ci]') && !validarCI(input.value.trim())) {
                                                errors.push(`El campo "${field.label}" de la Persona Natural #${index + 1} en Anexo N3 debe contener solo números y guiones (máx. 20 caracteres) o un guion (-).`);
                                            } else if (field.name.includes('[telefono]') && !validarTelefono(input.value.trim())) {
                                                errors.push(`El campo "${field.label}" de la Persona Natural #${index + 1} en Anexo N3 debe contener solo números y guiones (máx. 20 caracteres) o un guion (-).`);
                                            } else if (field.name.includes('[edad]') && !validarEdad(input.value.trim())) {
                                                errors.push(`El campo "${field.label}" de la Persona Natural #${index + 1} en Anexo N3 debe ser un número entre 0 y 120 o un guion (-).`);
                                            } else if (field.name.includes('[sexo]') && !['M', 'F', '-'].includes(input.value.trim())) {
                                                errors.push(`El campo "${field.label}" de la Persona Natural #${index + 1} en Anexo N3 debe ser "M", "F", o un guion (-).`);
                                            }
                                        }
                                        // if (field.name.includes('[ci]') && input.value.trim() && input.value !== '-') {
                                        //     if (cis.includes(input.value.trim())) {
                                        //         errors.push(`El CI ${input.value.trim()} está duplicado dentro de los Anexos N3.`);
                                        //     } else {
                                        //         cis.push(input.value.trim());
                                        //     }
                                        // }
                                    }
                                });
                            });
                            // Validar CI de Encargado (si es Persona Natural)
                            // const tipoEncargado = form.querySelector('input[name="tipo_encargado"]:checked')?.value;
                            // if (tipoEncargado === 'natural') {
                            //     const encargadoCI = form.querySelector('input[name="encargado_natural[ci]"]')?.value.trim();
                            //     if (encargadoCI && encargadoCI !== '-' && cis.includes(encargadoCI)) {
                            //         errors.push('El CI ingresado en Anexo N3 coincide con el CI del Encargado (Persona Natural).');
                            //     }
                            // }
                        }
                    }

                    // Tab 9: Anexo N5
                    if (activeTab === 'anexo5') {
                        const anexosN5 = form.querySelectorAll('#anexo5-container .anexo5-group');
                        if (anexosN5.length === 0) {
                            errors.push('Debe registrar al menos un anexo en Anexo N5.');
                        } else {
                            anexosN5.forEach((group, index) => {
                                const anexoFields = [
                                    { name: `anexos_n5[${index}][numero]`, label: 'Número', required: true },
                                    { name: `anexos_n5[${index}][fecha]`, label: 'Fecha', required: true },
                                    { name: `anexos_n5[${index}][accion_realizada]`, label: 'Acción Realizada', required: true },
                                    { name: `anexos_n5[${index}][resultado_obtenido]`, label: 'Resultado Obtenido' }
                                ];
                                anexoFields.forEach(field => {
                                    const input = group.querySelector(`[name="${field.name}"]`);
                                    if (input) {
                                        if (field.required && input.value.trim() === '' && input.value !== '-') {
                                            errors.push(`El campo "${field.label}" del Anexo N5 #${index + 1} es obligatorio. Ingrese un valor o un guion (-).`);
                                        } else if (input.value.trim() !== '' && input.value !== '-') {
                                            if (field.name.includes('[fecha]') && !validarFecha(input.value.trim())) {
                                                errors.push(`El campo "${field.label}" del Anexo N5 #${index + 1} debe ser una fecha válida o un guion (-).`);
                                            }
                                        }
                                    }
                                });
                            });
                        }
                    }

                    // Mostrar errores con SweetAlert2
                    if (errors.length > 0) {
                        event.preventDefault();
                        Swal.fire({
                            icon: 'error',
                            title: 'Errores en el formulario',
                            html: '<ul style="text-align: left;">' + errors.map(error => `<li>${error}</li>`).join('') + '</ul>',
                            confirmButtonText: 'Corregir'
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>
@endsection