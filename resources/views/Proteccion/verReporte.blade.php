<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Caso de Protección - {{ optional($adulto->persona)->nombres }}</title>
    <link rel="stylesheet" href="{{ asset('css/Proteccion/verReporte.css') }}">
</head>
<body>
    <div class="report-container"> 
        <div class="report-header">
            <h1>REGISTRO DE ATENCION Y PROTECCION A PERSONAS ADULTAS MAYORES</h1>
            <div class="unit-info">
                Nombre de la Unidad: "Unidad de Atención Social, Familia y Generacional - Oficina del Adulto Mayor"
            </div>
            <div class="meta-info">
                <div>Fecha: {{ optional($adulto->fecha)->format('d/m/Y') ?? 'N/A' }}</div>
                <div>N° Caso: {{ $adulto->nro_caso ?? 'N/A' }}</div>
            </div>
        </div>

        <!-- I. Datos Generales de la Persona Adulta Mayor -->
        <div class="section">
            <div class="section-title">I. Datos Generales de la Persona Adulta Mayor</div>
            <table class="data-table">
                <tr>
                    <td><strong>Primer Apellido:</strong> <span class="field-value">{{ optional($adulto->persona)->primer_apellido ?? '' }}</span></td>
                    <td><strong>Segundo Apellido:</strong> <span class="field-value">{{ optional($adulto->persona)->segundo_apellido ?? '' }}</span></td>
                    <td><strong>Nombres:</strong> <span class="field-value">{{ optional($adulto->persona)->nombres ?? '' }}</span></td>
                    <td><strong>Sexo:</strong> <span class="field-value">{{ (optional($adulto->persona)->sexo == 'M' ? 'MASCULINO' : (optional($adulto->persona)->sexo == 'F' ? 'FEMENINO' : '')) }}</span></td>
                </tr>
                <tr>
                    <td><strong>Fecha Nacimiento:</strong> <span class="field-value">{{ optional(optional($adulto->persona)->fecha_nacimiento)->format('d/m/Y') ?? '' }}</span></td>
                    <td><strong>C.I:</strong> <span class="field-value">{{ optional($adulto->persona)->ci ?? '' }}</span></td>
                    <td><strong>Edad:</strong> <span class="field-value">{{ optional($adulto->persona)->edad ?? '' }}</span></td>
                    <td><strong>Discapacidad:</strong> <span class="field-value">{{ $adulto->discapacidad ?? '' }}</span></td>
                </tr>
                <tr>
                    <td colspan="2"><strong>Estado Civil:</strong> <span class="field-value">{{ optional($adulto->persona)->estado_civil ?? '' }}</span></td>
                    <td colspan="2"><strong>Teléfono:</strong> <span class="field-value">{{ optional($adulto->persona)->telefono ?? '' }}</span></td>
                </tr>
                <tr>
                    <td colspan="4"><strong>Domicilio:</strong> <span class="field-value">{{ optional($adulto->persona)->direccion_domicilio ?? '' }}</span></td>
                </tr>
                <tr>
                    <td colspan="2"><strong>Con quien vive:</strong> <span class="field-value">{{ $adulto->vive_con ?? '' }}</span></td>
                    <td colspan="2"><strong>Zona/Comunidad:</strong> <span class="field-value">{{ optional($adulto->persona)->zona_comunidad ?? '' }}</span></td>
                </tr>
                <tr>
                    <td colspan="4"><strong>¿Es migrante?:</strong> <span class="field-value">{{ ($adulto->migrante === true ? 'Sí' : ($adulto->migrante === false ? 'No' : '')) }}</span>
                        @if ($adulto->migrante && $adulto->de_donde_migrante)
                            &nbsp;&nbsp;&nbsp;<strong>De Donde:</strong> <span class="field-value">{{ $adulto->de_donde_migrante }}</span>
                        @endif
                    </td>
                </tr>
            </table>
        </div>

        <!-- II. Actividad Laboral Remunerada de la Persona Adulta Mayor -->
        <div class="section">
            <div class="section-title">II. Actividad Laboral Remunerada de la Persona Adulta Mayor:</div>
            @if(optional($adulto->actividadLaboral)->exists)
                <table class="data-table">
                    <tr>
                        <td><strong>Actividad Laboral:</strong> <span class="field-value">{{ optional($adulto->actividadLaboral)->nombre_actividad ?? '' }}</span></td>
                        <td><strong>Dirección Trabajo:</strong> <span class="field-value">{{ optional($adulto->actividadLaboral)->direccion_trabajo ?? '' }}</span></td>
                    </tr>
                    <tr>
                        <td><strong>Horario:</strong> <span class="field-value">{{ optional($adulto->actividadLaboral)->horario ?? '' }}</span></td>
                        <td><strong>Horas x Día:</strong> <span class="field-value">{{ optional($adulto->actividadLaboral)->horas_x_dia ?? '' }}</span></td>
                    </tr>
                    <tr>
                        <td><strong>Remuneración Mensual Aprox:</strong> <span class="field-value">{{ optional($adulto->actividadLaboral)->rem_men_aprox ?? '' }}</span></td>
                        <td><strong>Teléfono:</strong> <span class="field-value">{{ optional($adulto->actividadLaboral)->telefono_laboral ?? '' }}</span></td>
                    </tr>
                </table>
            @else
                <p class="no-data-message">No se registró actividad laboral.</p>
            @endif
        </div>
        
        <!-- III. Datos del: Informante / Solicitante / Denunciante -->
        <div class="section">
            <div class="section-title">III. Datos del: Informante / Solicitante / Denunciante</div>
            @if($informante)
                @if($informante->tipo_encargado == 'natural')
                    <p><strong>Persona Natural</strong></p>
                    <table class="data-table">
                        <tr>
                            <td><strong>Primer Apellido:</strong> <span class="field-value">{{ optional($informante->personaNatural)->primer_apellido ?? '' }}</span></td>
                            <td><strong>Segundo Apellido:</strong> <span class="field-value">{{ optional($informante->personaNatural)->segundo_apellido ?? '' }}</span></td>
                            <td colspan="2"><strong>Nombres:</strong> <span class="field-value">{{ optional($informante->personaNatural)->nombres ?? '' }}</span></td>
                        </tr>
                        <tr>
                            <td><strong>Edad:</strong> <span class="field-value">{{ optional($informante->personaNatural)->edad ?? '' }}</span></td>
                            <td><strong>C.I:</strong> <span class="field-value">{{ optional($informante->personaNatural)->ci ?? '' }}</span></td>
                            <td colspan="2"><strong>Teléfono:</strong> <span class="field-value">{{ optional($informante->personaNatural)->telefono ?? '' }}</span></td>
                        </tr>
                        <tr>
                            <td colspan="4"><strong>Dirección Domicilio (Comunidad):</strong> <span class="field-value">{{ optional($informante->personaNatural)->direccion_domicilio ?? '' }}</span></td>
                        </tr>
                        <tr>
                            <td><strong>Relación/Parentesco:</strong> <span class="field-value">{{ optional($informante->personaNatural)->relacion_parentesco ?? '' }}</span></td>
                            <td><strong>Dirección de Trabajo:</strong> <span class="field-value">{{ optional($informante->personaNatural)->direccion_de_trabajo ?? '' }}</span></td>
                            <td colspan="2"><strong>Ocupación:</strong> <span class="field-value">{{ optional($informante->personaNatural)->ocupacion ?? '' }}</span></td>
                        </tr>
                    </table>
                @elseif($informante->tipo_encargado == 'juridica')
                    <p><strong>Persona Jurídica</strong></p>
                    <table class="data-table">
                        <tr>
                            <td colspan="2"><strong>Nombre Institución:</strong> <span class="field-value">{{ optional($informante->personaJuridica)->nombre_institucion ?? '' }}</span></td>
                            <td colspan="2"><strong>Dirección:</strong> <span class="field-value">{{ optional($informante->personaJuridica)->direccion ?? '' }}</span></td>
                        </tr>
                        <tr>
                            <td colspan="2"><strong>Teléfono:</strong> <span class="field-value">{{ optional($informante->personaJuridica)->telefono_juridica ?? '' }}</span></td>
                            <td colspan="2"><strong>Nombre Funcionario:</strong> <span class="field-value">{{ optional($informante->personaJuridica)->nombre_funcionario ?? '' }}</span></td>
                        </tr>
                    </table>
                @endif
            @else
                <p class="no-data-message">No se registró informante/solicitante/denunciante.</p>
            @endif

            <!-- a) Datos del Ofensor(a) Denunciado(a) -->
            <div class="section-title" style="margin-top: 20px;">a) Datos del Ofensor(a) Denunciado(a)</div>
            @if(optional($adulto->denunciado)->exists && optional($adulto->denunciado)->personaNatural)
                <table class="data-table">
                    <tr>
                        <td><strong>Primer Apellido:</strong> <span class="field-value">{{ optional($adulto->denunciado->personaNatural)->primer_apellido ?? '' }}</span></td>
                        <td><strong>Segundo Apellido:</strong> <span class="field-value">{{ optional($adulto->denunciado->personaNatural)->segundo_apellido ?? '' }}</span></td>
                        <td><strong>Nombres:</strong> <span class="field-value">{{ optional($adulto->denunciado->personaNatural)->nombres ?? '' }}</span></td>
                        <td><strong>Sexo:</strong> <span class="field-value">{{ (optional($adulto->denunciado)->sexo == 'M' ? 'M' : (optional($adulto->denunciado)->sexo == 'F' ? 'F' : '')) }}</span></td>
                    </tr>
                    <tr>
                        <td><strong>Edad:</strong> <span class="field-value">{{ optional($adulto->denunciado->personaNatural)->edad ?? '' }}</span></td>
                        <td><strong>C.I:</strong> <span class="field-value">{{ optional($adulto->denunciado->personaNatural)->ci ?? '' }}</span></td>
                        <td><strong>Teléfono:</strong> <span class="field-value">{{ optional($adulto->denunciado->personaNatural)->telefono ?? '' }}</span></td>
                        <td><strong>Dirección Domicilio:</strong> <span class="field-value">{{ optional($adulto->denunciado->personaNatural)->direccion_domicilio ?? '' }}</span></td>
                    </tr>
                    <tr>
                        <td><strong>Relación/Parentesco:</strong> <span class="field-value">{{ optional($adulto->denunciado->personaNatural)->relacion_parentesco ?? '' }}</span></td>
                        <td colspan="2"><strong>Dirección de Trabajo:</strong> <span class="field-value">{{ optional($adulto->denunciado->personaNatural)->direccion_de_trabajo ?? '' }}</span></td>
                        <td><strong>Ocupación:</strong> <span class="field-value">{{ optional($adulto->denunciado->personaNatural)->ocupacion ?? '' }}</span></td>
                    </tr>
                </table>
            @else
                <p class="no-data-message">No se registró ofensor/denunciado.</p>
            @endif

            <!-- b) Descripción de los Hechos -->
            <div class="section-title" style="margin-top: 20px;">b) Descripción de los Hechos:</div>
            <div class="description-box">
                {{ optional($adulto->denunciado)->descripcion_hechos ?? 'No se registró descripción de hechos.' }}
            </div>

            <!-- c) Grupo Familiar de la Persona Adulta Mayor -->
            <div class="section-title" style="margin-top: 20px;">c) Grupo Familiar de la Persona Adulta Mayor:</div>
            @if($adulto->grupoFamiliar->isNotEmpty())
                <table class="list-table">
                    <thead>
                        <tr>
                            <th>N°</th>
                            <th>Apellido Paterno</th>
                            <th>Apellido Materno</th>
                            <th>Nombres</th>
                            <th>Parentesco</th>
                            <th>Edad</th>
                            <th>Ocupación</th>
                            <th>Dirección</th>
                            <th>Teléfono</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($adulto->grupoFamiliar as $index => $familiar)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $familiar->apellido_paterno ?? '' }}</td>
                                <td>{{ $familiar->apellido_materno ?? '' }}</td>
                                <td>{{ $familiar->nombres ?? '' }}</td>
                                <td>{{ $familiar->parentesco ?? '' }}</td>
                                <td>{{ $familiar->edad ?? '' }}</td>
                                <td>{{ $familiar->ocupacion ?? '' }}</td>
                                <td>{{ $familiar->direccion ?? '' }}</td>
                                <td>{{ $familiar->telefono ?? '' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="no-data-message">No se registró grupo familiar.</p>
            @endif
        </div> <!-- End of Section III -->

        <!-- IV. Croquis del domicilio o lugar de referencia del Adulto Mayor -->
        <div class="section page-break">
            <div class="section-title">IV. Croquis del domicilio o lugar de referencia del Adulto Mayor:</div>
            <div class="croquis-section">
                @if(optional($adulto->croquis)->ruta_imagen)
                    <div class="croquis-image-container">
                        <img src="{{ Storage::url($adulto->croquis->ruta_imagen) }}" alt="Croquis del Domicilio" class="croquis-image">
                    </div>
                @else
                    <div class="croquis-image-container">
                        <p class="text-muted">No hay imagen de croquis registrada.</p>
                    </div>
                @endif
                <div class="croquis-signature-line">
                    <div>
                        <span class="signature-line">{{ optional($adulto->croquis)->nombre_denunciante }} {{ optional($adulto->croquis)->apellidos_denunciante }}</span>
                        <div class="label">Nombre y Apellidos del Denunciante</div>
                    </div>
                    <div>
                        <span class="signature-line">{{ optional($adulto->croquis)->ci_denunciante }}</span>
                        <div class="label">C.I.</div>
                    </div>
                    <div>
                        <span class="signature-line"></span>
                        <div class="label">Firma o Huella digital del Denunciante</div>
                    </div>
                </div>
                <div class="croquis-signature-line" style="margin-top: 30px;">
                    <div>
                        <span class="signature-line"></span>
                        <div class="label">Firma y Sello del Funcionario</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- V. Seguimiento del caso -->
        <div class="section page-break">
            <div class="section-title">V. Seguimiento del caso:</div>
            @if($adulto->seguimientos->isNotEmpty())
                <table class="list-table">
                    <thead>
                        <tr>
                            <th>N°</th>
                            <th>Fecha</th>
                            <th>Acción Realizada</th>
                            <th>Resultado Obtenido</th>
                            <th>Nombre del/la Funcionario(a) que Realizo la Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($adulto->seguimientos->sortBy('fecha') as $index => $seguimiento)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ optional($seguimiento->fecha)->format('d/m/Y') ?? '' }}</td>
                                <td>{{ $seguimiento->accion_realizada ?? '' }}</td>
                                <td>{{ $seguimiento->resultado_obtenido ?? '' }}</td>
                                <td>{{ optional(optional($seguimiento->usuario)->persona)->nombres }} {{ optional(optional($seguimiento->usuario)->persona)->primer_apellido }} {{ optional(optional($seguimiento->usuario)->persona)->segundo_apellido }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="no-data-message">No se registraron seguimientos del caso.</p>
            @endif
        </div>

        <!-- VI. Intervención de la Institución -->
        <div class="section page-break">
            <div class="section-title">VI. Intervención de la Institución.</div>
            @php
                $ultimaIntervencion = optional($adulto->seguimientos->sortByDesc('fecha')->first())->intervencion;
            @endphp

            @if($ultimaIntervencion)
                <table class="data-table">
                    <tr>
                        <td colspan="2"><strong>Resuelto:</strong> <span class="field-value">{{ optional($ultimaIntervencion)->resuelto_descripcion ?? 'N/A' }}</span></td>
                    </tr>
                    <tr>
                        <td colspan="2"><strong>No Resultado (¿Por qué?):</strong> <span class="field-value">{{ optional($ultimaIntervencion)->no_resultado ?? 'N/A' }}</span></td>
                    </tr>
                    <tr>
                        <td colspan="2"><strong>Derivado a otra institución (¿Por qué?):</strong> <span class="field-value">{{ optional($ultimaIntervencion)->derivacion_institucion ?? 'N/A' }}</span></td>
                    </tr>
                    <tr>
                        <td><strong>Derivado y en seguimiento legal:</strong> <span class="field-value">{{ optional($ultimaIntervencion)->der_seguimiento_legal ?? 'N/A' }}</span></td>
                        <td><strong>Institución:</strong> <span class="field-value">{{ optional($ultimaIntervencion)->der_seguimiento_legal_institucion ?? '' }}</span></td> {{-- Asumiendo que puedes tener un campo para la institución --}}
                    </tr>
                     <tr>
                        <td><strong>Derivado y en seguimiento psicológico:</strong> <span class="field-value">{{ optional($ultimaIntervencion)->der_seguimiento_psi ?? 'N/A' }}</span></td>
                        <td><strong>Institución:</strong> <span class="field-value">{{ optional($ultimaIntervencion)->der_seguimiento_psi_institucion ?? '' }}</span></td> {{-- Asumiendo que puedes tener un campo para la institución --}}
                    </tr>
                    <tr>
                        <td><strong>Derivado y resuelto en otra institución:</strong> <span class="field-value">{{ optional($ultimaIntervencion)->der_resuelto_externo ?? 'N/A' }}</span></td>
                        <td><strong>Institución:</strong> <span class="field-value">{{ optional($ultimaIntervencion)->der_resuelto_externo_institucion ?? '' }}</span></td> {{-- Asumiendo que puedes tener un campo para la institución --}}
                    </tr>
                    <tr>
                        <td><strong>Derivado a otra institución y no resuelto:</strong> <span class="field-value">{{ optional($ultimaIntervencion)->der_noresuelto_externo ?? 'N/A' }}</span></td>
                        <td><strong>Institución:</strong> <span class="field-value">{{ optional($ultimaIntervencion)->der_noresuelto_externo_institucion ?? '' }}</span></td> {{-- Asumiendo que puedes tener un campo para la institución --}}
                    </tr>
                    <tr>
                        <td colspan="2"><strong>Abandonado por la Víctima (¿Qué paso?):</strong> <span class="field-value">{{ optional($ultimaIntervencion)->abandono_victima ?? 'N/A' }}</span></td>
                    </tr>
                     <tr>
                        <td colspan="2"><strong>Resuelto mediante conciliación según Justicia Indígena Originaria:</strong> <span class="field-value">{{ optional($ultimaIntervencion)->resuelto_conciliacion_jio ?? 'N/A' }}</span></td>
                    </tr>
                    <tr>
                        <td colspan="2"><strong>Fecha:</strong> <span class="field-value">{{ optional($ultimaIntervencion->fecha)->format('d/m/Y') ?? 'N/A' }}</span></td>
                    </tr>
                </table>
                 <div class="croquis-signature-line" style="margin-top: 30px;">
                    <div>
                        <span class="signature-line"></span>
                        <div class="label">Sello y Firma del Funcionario Responsable</div>
                    </div>
                </div>
            @else
                <p class="no-data-message">No se registró intervención de la institución.</p>
            @endif
        </div>

        <!-- ANEXO AL NUMERAL III. (Anexo N3) -->
        <div class="section page-break">
            <div class="section-title">ANEXO AL NUMERAL III. (Personas Adicionales)</div>
            @if($adulto->anexoN3->isNotEmpty())
                <table class="list-table">
                    <thead>
                        <tr>
                            <th>N°</th>
                            <th>Primer Apellido</th>
                            <th>Segundo Apellido</th>
                            <th>Nombres</th>
                            <th>Sexo</th>
                            <th>Edad</th>
                            <th>C.I.</th>
                            <th>Teléfono</th>
                            <th>Dirección Domicilio (Comunidad)</th>
                            <th>Relación/Parentesco</th>
                            <th>Dirección de Trabajo</th>
                            <th>Ocupación</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($adulto->anexoN3 as $index => $anexo3Item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ optional($anexo3Item->personaNatural)->primer_apellido ?? '' }}</td>
                                <td>{{ optional($anexo3Item->personaNatural)->segundo_apellido ?? '' }}</td>
                                <td>{{ optional($anexo3Item->personaNatural)->nombres ?? '' }}</td>
                                <td>{{ (optional($anexo3Item->personaNatural)->sexo == 'M' ? 'M' : (optional($anexo3Item->personaNatural)->sexo == 'F' ? 'F' : '')) }}</td>
                                <td>{{ optional($anexo3Item->personaNatural)->edad ?? '' }}</td>
                                <td>{{ optional($anexo3Item->personaNatural)->ci ?? '' }}</td>
                                <td>{{ optional($anexo3Item->personaNatural)->telefono ?? '' }}</td>
                                <td>{{ optional($anexo3Item->personaNatural)->direccion_domicilio ?? '' }}</td>
                                <td>{{ optional($anexo3Item->personaNatural)->relacion_parentesco ?? '' }}</td>
                                <td>{{ optional($anexo3Item->personaNatural)->direccion_de_trabajo ?? '' }}</td>
                                <td>{{ optional($anexo3Item->personaNatural)->ocupacion ?? '' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="no-data-message">No se registraron personas adicionales (Anexo N3).</p>
            @endif
        </div>

        <!-- ANEXO AL NUMERAL V. (Anexo N5) -->
        <div class="section page-break">
            <div class="section-title">ANEXO AL NUMERAL V. (Actividades de Seguimiento y Prevención)</div>
            @if($adulto->anexoN5->isNotEmpty())
                <table class="list-table">
                    <thead>
                        <tr>
                            <th>N°</th>
                            <th>Número de Anexo N5</th>
                            <th>Fecha</th>
                            <th>Acción Realizada</th>
                            <th>Resultado Obtenido</th>
                            <th>Nombre del/la Funcionario(a) que Realizo la Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($adulto->anexoN5->sortBy('fecha') as $index => $anexo5Item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $anexo5Item->numero ?? '' }}</td>
                                <td>{{ optional($anexo5Item->fecha)->format('d/m/Y') ?? '' }}</td>
                                <td>{{ $anexo5Item->accion_realizada ?? '' }}</td>
                                <td>{{ $anexo5Item->resultado_obtenido ?? '' }}</td>
                                <td>{{ optional(optional($anexo5Item->usuario)->persona)->nombres }} {{ optional(optional($anexo5Item->usuario)->persona)->primer_apellido }} {{ optional(optional($anexo5Item->usuario)->persona)->segundo_apellido }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="no-data-message">No se registraron actividades en Anexo N5.</p>
            @endif
        </div>

        <div class="print-actions">
            <button class="print-button" onclick="window.print()">Imprimir Reporte</button>
            <a href="{{ route('legal.reportes_proteccion.index') }}" class="print-button back-button">Volver a Reportes</a>
        </div>

    </div>
</body>
</html>
