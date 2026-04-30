<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ficha de Protección - {{ mb_strtoupper(optional($adulto->persona)->nombres ?? '') }} {{ mb_strtoupper(optional($adulto->persona)->primer_apellido ?? '') }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 9.5pt;
            line-height: 1.2;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            margin: 0 auto;
            padding: 20px 30px;
            box-sizing: border-box;
        }

        /* Encabezado Principal (Logo y Títulos de Gobierno) */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .header-table td {
            vertical-align: top;
            padding: 0;
        }
        .header-table .logo-cell {
            width: 100px;
            text-align: left;
            padding-right: 10px;
        }
        .header-table .text-cell {
            text-align: center;
        }
        .header-table h3 {
            margin: 0;
            padding: 0;
            font-size: 11pt;
            font-weight: bold;
        }
        .header-table p {
            margin: 0;
            padding: 0;
            font-size: 9pt;
        }

        /* Títulos de Sección */
        .main-title {
            text-align: center;
            font-size: 14pt;
            font-weight: bold;
            margin-top: 15px;
            margin-bottom: 15px;
            padding-bottom: 5px;
            border-bottom: 2px solid black;
        }
        .section-title {
            font-size: 10.5pt;
            font-weight: bold;
            text-decoration: underline;
            margin-top: 15px;
            margin-bottom: 8px;
            text-align: left;
        }
        .subsection-title {
            font-size: 10pt;
            font-weight: bold;
            margin-top: 10px;
            margin-bottom: 5px;
        }

        /* Estilos de Tabla para Contenido (con bordes) */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .data-table, .data-table th, .data-table td {
            border: 1px solid black;
            padding: 4px 6px;
            text-align: left;
            vertical-align: middle;
        }
        .data-table th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }
        .data-table td.center {
            text-align: center;
        }

        /* Para campos de texto largos */
        .text-area-box {
            border: 1px solid black;
            padding: 5px;
            min-height: 50px;
            margin-bottom: 10px;
            line-height: 1.4;
            text-align: justify;
        }

        /* Controles de checkbox */
        .checkbox-label {
            display: inline-block;
            margin-right: 10px;
            font-weight: normal;
        }
        .checkbox-box {
            display: inline-block;
            width: 12px;
            height: 12px;
            border: 1px solid black;
            text-align: center;
            line-height: 12px;
            vertical-align: middle;
            margin-left: 5px;
            font-size: 8pt;
        }

        /* Estilos para ANEXO AL NUMERAL III (sin tabla) */
        .anexo-n3-item {
            margin-bottom: 15px;
            border: 1px solid black;
            padding: 5px;
        }
        .anexo-n3-label {
            font-weight: bold;
            margin-right: 5px;
        }
        .anexo-n3-value {
            display: inline-block;
            margin-right: 20px;
        }

        /* Firmas y PIE */
        .signatures-area {
            margin-top: 30px;
            width: 100%;
        }
        .signature-col {
            width: 49%;
            display: inline-block;
            text-align: center;
            vertical-align: top;
            padding: 0 10px;
            box-sizing: border-box;
        }
        .signature-line {
            border-bottom: 1px solid black;
            height: 1px;
            margin: 20px auto 5px auto;
            width: 80%;
        }
        .signature-text {
            font-weight: bold;
            font-size: 9pt;
        }
        .footer-text {
            margin-top: 30px;
            font-size: 8.5pt;
            text-align: center;
        }
    </style>
</head>
<body>
    @php
        use Carbon\Carbon;
        use Illuminate\Support\Facades\Storage;
    @endphp

    <div class="container">
        <table class="header-table">
            <tr>
                <td class="logo-cell">
                    <img src="{{ public_path('assets/images/brand/alcaldiaicon.png') }}" style="width: 80px; height: 80px; display: block;" alt="Logo Alcaldía">
                </td>
                <td class="text-cell">
                    <h3>GOBIERNO AUTONOMO MUNICIPAL DE TARIJA</h3>
                    <p>OFICINA DEL ADULTO MAYOR</p>
                </td>
            </tr>
        </table>

        <h1 class="main-title">REGISTRO DE ATENCIÓN Y PROTECCIÓN A PERSONAS ADULTAS MAYORES</h1>

        <table class="data-table">
            <tr>
                <td style="width: 25%; font-weight: bold;">NOMBRE DE LA UNIDAD:</td>
                <td style="width: 55%;">UNIDAD DE ATENCIÓN SOCIAL, FAMILIA Y GENERACIONAL – OFICINA DEL ADULTO MAYOR</td>
                <td style="width: 10%; font-weight: bold;">FECHA:</td>
                <td style="width: 10%;">{{ Carbon::now()->format('d/m/Y') }}</td>
            </tr>
            <tr>
                <td colspan="3" style="text-align: right; font-weight: bold;">N° CASO:</td>
                <td style="text-align: left;">{{ mb_strtoupper(optional($adulto)->nro_caso ?? 'N/A') }}</td>
            </tr>
        </table>

        <h2 class="section-title">I. DATOS GENERALES DE LA PERSONA ADULTA MAYOR</h2>
        <table class="data-table">
            <tr>
                <td colspan="2"><span class="label">PRIMER APELLIDO:</span> <span class="value">{{ mb_strtoupper(optional($adulto->persona)->primer_apellido ?? 'N/A') }}</span></td>
                <td colspan="2"><span class="label">SEGUNDO APELLIDO:</span> <span class="value">{{ mb_strtoupper(optional($adulto->persona)->segundo_apellido ?? '') }}</span></td>
                <td colspan="2"><span class="label">NOMBRES:</span> <span class="value">{{ mb_strtoupper(optional($adulto->persona)->nombres ?? 'N/A') }}</span></td>
                <td style="width: 15%;"><span class="label">SEXO:</span> <span class="value">{{ mb_strtoupper(optional($adulto->persona)->sexo == 'M' ? 'Masculino' : (optional($adulto->persona)->sexo == 'F' ? 'Femenino' : 'N/A')) }}</span></td>
            </tr>
            <tr>
                <td colspan="2"><span class="label">FECHA NACIMIENTO:</span> <span class="value">{{ optional($adulto->persona)->fecha_nacimiento ? Carbon::parse($adulto->persona->fecha_nacimiento)->format('d/m/Y') : 'N/A' }}</span></td>
                <td colspan="2"><span class="label">C.I.:</span> <span class="value">{{ mb_strtoupper(optional($adulto->persona)->ci ?? 'N/A') }}</span></td>
                <td colspan="2"><span class="label">EDAD:</span> <span class="value">{{ optional($adulto->persona)->fecha_nacimiento ? Carbon::parse($adulto->persona->fecha_nacimiento)->age : 'N/A' }}</span></td>
                <td colspan="1"><span class="label">DISCAPACIDAD:</span> <span class="value">{{ mb_strtoupper(optional($adulto)->discapacidad ?? 'N/A') }}</span></td>
            </tr>
            <tr>
                <td colspan="4"><span class="label">ESTADO CIVIL:</span> <span class="value">{{ mb_strtoupper(optional($adulto->persona)->estado_civil ?? 'N/A') }}</span></td>
                <td colspan="3"><span class="label">DOMICILIO:</span> <span class="value">{{ mb_strtoupper(optional($adulto->persona)->domicilio ?? 'N/A') }}</span></td>
            </tr>
            <tr>
                <td colspan="4"><span class="label">CON QUIÉN VIVE:</span> <span class="value">{{ mb_strtoupper(optional($adulto)->vive_con ?? 'N/A') }}</span></td>
                <td colspan="3"><span class="label">TELÉFONO:</span> <span class="value">{{ mb_strtoupper(optional($adulto->persona)->telefono ?? 'N/A') }}</span></td>
            </tr>
            <tr>
                <td colspan="4"><span class="label">ZONA/COMUNIDAD:</span> <span class="value">{{ mb_strtoupper(optional($adulto->persona)->zona_comunidad ?? 'N/A') }}</span></td>
                <td colspan="3"><span class="label">¿ES MIGRANTE?:</span> <span class="value">{{ mb_strtoupper(optional($adulto)->migrante === true ? 'SI' : (optional($adulto)->migrante === false ? 'NO' : 'N/A')) }}</span>
                    @if(optional($adulto)->migrante)
                        <span class="label" style="width: auto;"> DE DONDE?:</span> <span class="value">{{ mb_strtoupper(optional($adulto)->lugar_migracion ?? 'N/A') }}</span>
                    @endif
                </td>
            </tr>
        </table>

        <h2 class="section-title">II. ACTIVIDAD LABORAL REMUNERADA DE LA PERSONA ADULTA MAYOR:
            <span class="checkbox-label">SI (<span class="checkbox-box">{{ (optional($adulto->actividadLaboral)->nombre_actividad || optional($adulto->actividadLaboral)->ocupacion ? 'X' : '') }}</span>)</span>
            <span class="checkbox-label">NO (<span class="checkbox-box">{{ (!optional($adulto->actividadLaboral)->nombre_actividad && !optional($adulto->actividadLaboral)->ocupacion ? 'X' : '') }}</span>)</span>
        </h2>
        @php
            $actividadLaboral = optional($adulto)->actividadLaboral;
        @endphp
        @if($actividadLaboral && ($actividadLaboral->nombre_actividad || $actividadLaboral->direccion_trabajo || $actividadLaboral->horario || $actividadLaboral->horas_x_dia || $actividadLaboral->rem_men_aprox || $actividadLaboral->telefono_laboral))
            <table class="data-table">
                <tr>
                    <td><span class="label">ACTIVIDAD LABORAL:</span> <span class="value">{{ mb_strtoupper(optional($actividadLaboral)->nombre_actividad ?? 'N/A') }}</span></td>
                    <td><span class="label">DIRECCIÓN HABITUAL DEL TRABAJO:</span> <span class="value">{{ mb_strtoupper(optional($actividadLaboral)->direccion_trabajo ?? 'N/A') }}</span></td>
                    <td><span class="label">HORARIO:</span> <span class="value">{{ mb_strtoupper(optional($actividadLaboral)->horario ?? 'N/A') }}</span></td>
                </tr>
                <tr>
                    <td><span class="label">HORAS DE TRABAJO POR DÍA:</span> <span class="value">{{ mb_strtoupper(optional($actividadLaboral)->horas_x_dia ?? 'N/A') }}</span></td>
                    <td><span class="label">REMUNERACIÓN MENSUAL APROXIMADA:</span> <span class="value">{{ mb_strtoupper(optional($actividadLaboral)->rem_men_aprox ?? 'N/A') }}</span></td>
                    <td><span class="label">TELÉFONO:</span> <span class="value">{{ mb_strtoupper(optional($actividadLaboral)->telefono_laboral ?? 'N/A') }}</span></td>
                </tr>
            </table>
        @else
            <p>NO SE REGISTRÓ ACTIVIDAD LABORAL REMUNERADA.</p>
        @endif

        <h2 class="section-title">III. DATOS DEL: INFORMANTE <span class="checkbox-box">{{ (optional($informante)->tipo_encargado == 'informante' ? 'X' : '') }}</span>
            SOLICITANTE <span class="checkbox-box">{{ (optional($informante)->tipo_encargado == 'solicitante' ? 'X' : '') }}</span>
            DENUNCIANTE <span class="checkbox-box">{{ (optional($informante)->tipo_encargado == 'denunciante' ? 'X' : '') }}</span>
        </h2>
        <p style="font-size: 8pt; margin-bottom: 5px;">ANÓNIMO (<span class="checkbox-box">{{ (optional($informante)->anonimo ? 'X' : '') }}</span>) DATOS DE LA PERSONA QUE DA PARTE.</p>

        @if($informante)
            @if(optional($informante)->tipo_encargado === 'natural')
                <h3 class="subsection-title">PERSONA NATURAL:</h3>
                <table class="data-table">
                    <tr>
                        <td colspan="2"><span class="label">PRIMER APELLIDO:</span> <span class="value">{{ mb_strtoupper(optional($informante->personaNatural)->primer_apellido ?? '') }}</span></td>
                        <td colspan="2"><span class="label">SEGUNDO APELLIDO:</span> <span class="value">{{ mb_strtoupper(optional($informante->personaNatural)->segundo_apellido ?? '') }}</span></td>
                        <td colspan="2"><span class="label">NOMBRES:</span> <span class="value">{{ mb_strtoupper(optional($informante->personaNatural)->nombres ?? '') }}</span></td>
                    </tr>
                    <tr>
                        <td><span class="label">EDAD:</span> <span class="value">{{ optional($informante->personaNatural)->edad ?? '' }}</span></td>
                        <td><span class="label">C.I.:</span> <span class="value">{{ mb_strtoupper(optional($informante->personaNatural)->ci ?? '') }}</span></td>
                        <td><span class="label">TELÉFONO:</span> <span class="value">{{ mb_strtoupper(optional($informante->personaNatural)->telefono ?? '') }}</span></td>
                        <td colspan="3"><span class="label">DIRECCIÓN DOMICILIO (COMUNIDAD):</span> <span class="value">{{ mb_strtoupper(optional($informante->personaNatural)->direccion_domicilio ?? '') }}</span></td>
                    </tr>
                    <tr>
                        <td><span class="label">RELACIÓN/PARENTESCO:</span> <span class="value">{{ mb_strtoupper(optional($informante->personaNatural)->relacion_parentesco ?? '') }}</span></td>
                        <td colspan="3"><span class="label">DIRECCIÓN DE TRABAJO:</span> <span class="value">{{ mb_strtoupper(optional($informante->personaNatural)->direccion_de_trabajo ?? '') }}</span></td>
                        <td colspan="2"><span class="label">OCUPACIÓN:</span> <span class="value">{{ mb_strtoupper(optional($informante->personaNatural)->ocupacion ?? '') }}</span></td>
                    </tr>
                </table>
            @elseif(optional($informante)->tipo_encargado === 'juridica')
                <h3 class="subsection-title">PERSONA JURÍDICA:</h3>
                <table class="data-table">
                    <tr>
                        <td colspan="2"><span class="label">NOMBRE DE INSTITUCIÓN:</span> <span class="value">{{ mb_strtoupper(optional($informante->personaJuridica)->nombre_institucion ?? '') }}</span></td>
                        <td colspan="2"><span class="label">DIRECCIÓN:</span> <span class="value">{{ mb_strtoupper(optional($informante->personaJuridica)->direccion ?? '') }}</span></td>
                    </tr>
                    <tr>
                        <td colspan="2"><span class="label">TELÉFONO:</span> <span class="value">{{ mb_strtoupper(optional($informante->personaJuridica)->telefono_juridica ?? '') }}</span></td>
                        <td colspan="2"><span class="label">NOMBRE DEL FUNCIONARIO RESPONSABLE:</span> <span class="value">{{ mb_strtoupper(optional($informante->personaJuridica)->nombre_funcionario ?? '') }}</span></td>
                    </tr>
                </table>
            @else
                <p>NO SE REGISTRARON DATOS DEL INFORMANTE/SOLICITANTE/DENUNCIANTE.</p>
            @endif
        @else
            <p>NO SE REGISTRÓ INFORMACIÓN DEL INFORMANTE.</p>
        @endif

        <h2 class="subsection-title">a) DATOS DEL OFENSOR(A) DENUNCIADO(A)</h2>
        @php
            $denunciado = optional($adulto)->denunciado;
            $denunciadoPN = optional($denunciado)->personaNatural;
        @endphp
        @if($denunciadoPN)
            <table class="data-table">
                <tr>
                    <td colspan="2"><span class="label">PRIMER APELLIDO:</span> <span class="value">{{ mb_strtoupper(optional($denunciadoPN)->primer_apellido ?? '') }}</span></td>
                    <td colspan="2"><span class="label">SEGUNDO APELLIDO:</span> <span class="value">{{ mb_strtoupper(optional($denunciadoPN)->segundo_apellido ?? '') }}</span></td>
                    <td colspan="2"><span class="label">NOMBRES:</span> <span class="value">{{ mb_strtoupper(optional($denunciadoPN)->nombres ?? '') }}</span></td>
                </tr>
                <tr>
                    <td><span class="label">SEXO:</span> <span class="value">{{ mb_strtoupper(optional($denunciado)->sexo == 'M' ? 'Masculino' : (optional($denunciado)->sexo == 'F' ? 'Femenino' : 'N/A')) }}</span></td>
                    <td><span class="label">EDAD:</span> <span class="value">{{ optional($denunciadoPN)->edad ?? '' }}</span></td>
                    <td><span class="label">C.I.:</span> <span class="value">{{ mb_strtoupper(optional($denunciadoPN)->ci ?? '') }}</span></td>
                    <td colspan="3"><span class="label">TELÉFONO:</span> <span class="value">{{ mb_strtoupper(optional($denunciadoPN)->telefono ?? '') }}</span></td>
                </tr>
                <tr>
                    <td colspan="3"><span class="label">DIRECCIÓN DOMICILIO (COMUNIDAD):</span> <span class="value">{{ mb_strtoupper(optional($denunciadoPN)->direccion_domicilio ?? '') }}</span></td>
                    <td><span class="label">RELACIÓN/PARENTESCO:</span> <span class="value">{{ mb_strtoupper(optional($denunciadoPN)->relacion_parentesco ?? '') }}</span></td>
                    <td><span class="label">DIRECCIÓN DE TRABAJO:</span> <span class="value">{{ mb_strtoupper(optional($denunciadoPN)->direccion_de_trabajo ?? '') }}</span></td>
                    <td><span class="label">OCUPACIÓN:</span> <span class="value">{{ mb_strtoupper(optional($denunciadoPN)->ocupacion ?? '') }}</span></td>
                </tr>
            </table>
        @else
            <p>NO SE REGISTRARON DATOS DEL OFENSOR(A)/DENUNCIADO(A).</p>
        @endif

        <h2 class="subsection-title">b) DESCRIPCIÓN DE LOS HECHOS:</h2>
        <div class="text-area-box">{{ mb_strtoupper(optional($denunciado)->descripcion_hechos ?? 'NO ESPECIFICADO.') }}</div>

        <h2 class="subsection-title">c) GRUPO FAMILIAR DE LA PERSONA ADULTA MAYOR:</h2>
        @php
            $grupoFamiliar = optional($adulto)->grupoFamiliar;
        @endphp
        @if($grupoFamiliar && $grupoFamiliar->count() > 0)
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 5%;">N°</th>
                        <th style="width: 13%;">APELLIDO PATERNO</th>
                        <th style="width: 13%;">APELLIDO MATERNO</th>
                        <th style="width: 14%;">NOMBRES</th>
                        <th style="width: 9%;">PARENTESCO</th>
                        <th style="width: 5%;">EDAD</th>
                        <th style="width: 10%;">OCUPACIÓN</th>
                        <th style="width: 16%;">DIRECCIÓN</th>
                        <th style="width: 15%;">TELÉFONO</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($grupoFamiliar as $index => $familiar)
                        <tr>
                            <td class="center">{{ $index + 1 }}</td>
                            <td>{{ mb_strtoupper(optional($familiar)->apellido_paterno ?? 'N/A') }}</td>
                            <td>{{ mb_strtoupper(optional($familiar)->apellido_materno ?? 'N/A') }}</td>
                            <td>{{ mb_strtoupper(optional($familiar)->nombres ?? 'N/A') }}</td>
                            <td>{{ mb_strtoupper(optional($familiar)->parentesco ?? 'N/A') }}</td>
                            <td class="center">{{ optional($familiar)->edad ?? 'N/A' }}</td>
                            <td>{{ mb_strtoupper(optional($familiar)->ocupacion ?? 'N/A') }}</td>
                            <td>{{ mb_strtoupper(optional($familiar)->direccion ?? 'N/A') }}</td>
                            <td>{{ mb_strtoupper(optional($familiar)->telefono ?? 'N/A') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p>NO SE REGISTRÓ INFORMACIÓN DEL GRUPO FAMILIAR.</p>
        @endif

        <h2 class="section-title">IV. CROQUIS DEL DOMICILIO O LUGAR DE REFERENCIA DEL ADULTO MAYOR:</h2>
        @php
            $croquis = optional($adulto)->croquis;
        @endphp
        @if($croquis)
            <table class="data-table">
                <tr>
                    <td colspan="2"><span class="label">NOMBRE Y APELLIDOS DEL DENUNCIANTE:</span> <span class="value">{{ mb_strtoupper(trim(optional($croquis)->nombre_denunciante ?? '') . ' ' . trim(optional($croquis)->apellidos_denunciante ?? '')) }}</span></td>
                    <td colspan="1"><span class="label">C.I.:</span> <span class="value">{{ mb_strtoupper(optional($croquis)->ci_denunciante ?? 'N/A') }}</span></td>
                </tr>
                <tr>
                    <td colspan="3" style="text-align: center;">
                        @php
                            $imageFilename = optional($croquis)->ruta_imagen;
                            $fullImagePathInStorage = '';
                            if ($imageFilename) {
                                if (!str_starts_with($imageFilename, 'croquis_images/')) {
                                    $fullImagePathInStorage = 'croquis_images/' . $imageFilename;
                                } else {
                                    $fullImagePathInStorage = $imageFilename;
                                }
                            }
                        @endphp
                        @if($fullImagePathInStorage && Storage::disk('public')->exists($fullImagePathInStorage))
                            <img src="{{ storage_path('app/public/' . $fullImagePathInStorage) }}" style="max-width: 100%; height: auto; display: block; margin: 10px auto;" alt="Croquis del domicilio">
                            <p style="color: #666; font-style: italic; text-align: center;">[Imagen del Croquis]</p>
                        @else
                            <p style="color: #666; font-style: italic;">NO SE ADJUNTÓ IMAGEN DE CROQUIS O LA IMAGEN NO FUE ENCONTRADA.</p>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td colspan="3" style="text-align: center;">
                        <div style="height: 80px;"></div>
                        <span class="signature-text">FIRMA O HUELLA DIGITAL DEL ADULTO MAYOR</span>
                        <div style="height: 80px;"></div>
                        <span class="signature-text">FIRMA Y SELLO DEL FUNCIONARIO</span>
                    </td>
                </tr>
            </table>
        @else
            <p>NO SE REGISTRÓ INFORMACIÓN DEL CROQUIS.</p>
        @endif

        <h2 class="section-title">V. SEGUIMIENTO DEL CASO:</h2>
        @php
            $seguimientos = optional($adulto)->seguimientos;
        @endphp
        @if($seguimientos && $seguimientos->count() > 0)
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 5%;">N°</th>
                        <th style="width: 10%;">FECHA</th>
                        <th style="width: 28%;">ACCIÓN REALIZADA</th>
                        <th style="width: 28%;">RESULTADO OBTENIDO</th>
                        <th style="width: 29%;">NOMBRE DEL/LA FUNCIONARIO(A) QUE REALIZÓ LA ACCIÓN</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($seguimientos as $index => $seguimiento)
                        <tr>
                            <td class="center">{{ optional($seguimiento)->nro ?? 'N/A' }}</td>
                            <td>{{ optional($seguimiento)->fecha ? Carbon::parse($seguimiento->fecha)->format('d/m/Y') : 'N/A' }}</td>
                            <td>{{ mb_strtoupper(optional($seguimiento)->accion_realizada ?? 'N/A') }}</td>
                            <td>{{ mb_strtoupper(optional($seguimiento)->resultado_obtenido ?? 'N/A') }}</td>
                            <td>{{ mb_strtoupper(trim(optional(optional($seguimiento)->usuario)->persona->nombres ?? '') . ' ' . trim(optional(optional($seguimiento)->usuario)->persona->primer_apellido ?? '') . ' ' . trim(optional(optional($seguimiento)->usuario)->persona->segundo_apellido ?? '')) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p>NO SE REGISTRARON SEGUIMIENTOS PARA ESTE CASO.</p>
        @endif

        <h2 class="section-title">VI. INTERVENCIÓN DE LA INSTITUCIÓN.</h2>
        @if($intervencion)
            <table class="data-table" style="max-width: 180mm; margin: 0 auto; box-sizing: border-box;">
                <tr>
                    <td style="width: 20%; vertical-align: top;">
                        <span class="label">RESUELTO:</span> <span class="checkbox-box">{{ (optional($intervencion)->resuelto_descripcion !== null && optional($intervencion)->resuelto_descripcion !== '' ? 'X' : '') }}</span>
                    </td>
                    <td style="width: 80%; vertical-align: top;">
                        <span class="label">¿CÓMO?:</span> <span class="value" style="word-wrap: break-word; display: block;">{{ mb_strtoupper(optional($intervencion)->resuelto_descripcion ?? 'N/A') }}</span>
                    </td>
                </tr>
                <tr>
                    <td style="width: 20%; vertical-align: top;">
                        <span class="label">NO RESULTADO:</span> <span class="checkbox-box">{{ (optional($intervencion)->no_resultado !== null && optional($intervencion)->no_resultado !== '' ? 'X' : '') }}</span>
                    </td>
                    <td style="width: 80%; vertical-align: top;">
                        <span class="label">¿POR QUÉ?:</span> <span class="value" style="word-wrap: break-word; display: block;">{{ mb_strtoupper(optional($intervencion)->no_resultado ?? 'N/A') }}</span>
                    </td>
                </tr>
                <tr>
                    <td style="width: 20%; vertical-align: top;">
                        <span class="label">DERIVADO A OTRA INSTITUCIÓN:</span> <span class="checkbox-box">{{ (optional($intervencion)->derivacion_institucion !== null && optional($intervencion)->derivacion_institucion !== '' ? 'X' : '') }}</span>
                    </td>
                    <td style="width: 80%; vertical-align: top;">
                        <span class="label">¿POR QUÉ?:</span> <span class="value" style="word-wrap: break-word; display: block;">{{ mb_strtoupper(optional($intervencion)->derivacion_institucion ?? 'N/A') }}</span>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="vertical-align: top;">
                        <span class="label">DERIVACIONES Y RESULTADOS:</span>
                        <ul style="list-style-type: none; padding-left: 20px; margin: 5px 0;">
                            <li><span class="label">DERIVADO Y EN SEGUIMIENTO LEGAL:</span> <span class="value" style="word-wrap: break-word; display: block;">{{ mb_strtoupper(optional($intervencion)->der_seguimiento_legal ?? 'N/A') }}</span></li>
                            <li><span class="label">DERIVADO Y EN SEGUIMIENTO PSICOLÓGICO:</span> <span class="value" style="word-wrap: break-word; display: block;">{{ mb_strtoupper(optional($intervencion)->der_seguimiento_psi ?? 'N/A') }}</span></li>
                            <li><span class="label">DERIVADO Y RESUELTO EN OTRA INSTITUCIÓN:</span> <span class="value" style="word-wrap: break-word; display: block;">{{ mb_strtoupper(optional($intervencion)->der_resuelto_externo ?? 'N/A') }}</span></li>
                            <li><span class="label">DERIVADO A OTRA INSTITUCIÓN Y NO RESUELTO:</span> <span class="value" style="word-wrap: break-word; display: block;">{{ mb_strtoupper(optional($intervencion)->der_noresuelto_externo ?? 'N/A') }}</span></li>
                        </ul>
                    </td>
                </tr>
                <tr>
                    <td style="width: 20%; vertical-align: top;">
                        <span class="label">ABANDONADO POR LA VÍCTIMA - ¿QUÉ PASÓ?:</span>
                    </td>
                    <td style="width: 80%; vertical-align: top;">
                        <span class="value" style="word-wrap: break-word; display: block;">{{ mb_strtoupper(optional($intervencion)->abandono_victima ?? 'N/A') }}</span>
                    </td>
                </tr>
                <tr>
                    <td style="width: 20%; vertical-align: top;">
                        <span class="label">RESUELTO MEDIANTE CONCILIACIÓN SEGÚN JUSTICIA INDÍGENA ORIGINARIA:</span>
                    </td>
                    <td style="width: 80%; vertical-align: top;">
                        <span class="value" style="word-wrap: break-word; display: block;">{{ mb_strtoupper(optional($intervencion)->resuelto_conciliacion_jio ?? 'N/A') }}</span>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align: right; padding-top: 15px;">
                        <span class="label">FECHA:</span> <span class="value">{{ optional($intervencion->fecha_intervencion ?? null)->format('d/m/Y') ?? 'N/A' }}</span>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align: center; padding-top: 20px;">
                        <div style="height: 40px;"></div>
                        <span class="signature-text">SELLO Y FIRMA DEL FUNCIONARIO RESPONSABLE</span>
                    </td>
                </tr>
            </table>
        @else
            <p>NO SE REGISTRÓ INFORMACIÓN DE INTERVENCIÓN.</p>
        @endif

        <h2 class="section-title">ANEXO AL NUMERAL III.</h2>
        @php
            $anexosN3 = optional($adulto)->anexoN3;
        @endphp
        @if($anexosN3 && $anexosN3->count() > 0)
            @foreach($anexosN3 as $index => $anexo3)
                @php
                    $personaNaturalAnexo3 = optional($anexo3)->personaNatural;
                @endphp
                @if($personaNaturalAnexo3)
                    <div class="anexo-n3-item">
                        <p><span class="anexo-n3-label">N°:</span> <span class="anexo-n3-value">{{ $index + 1 }}</span></p>
                        <p><span class="anexo-n3-label">PRIMER APELLIDO:</span> <span class="anexo-n3-value">{{ mb_strtoupper(optional($personaNaturalAnexo3)->primer_apellido ?? 'N/A') }}</span></p>
                        <p><span class="anexo-n3-label">SEGUNDO APELLIDO:</span> <span class="anexo-n3-value">{{ mb_strtoupper(optional($personaNaturalAnexo3)->segundo_apellido ?? '') }}</span></p>
                        <p><span class="anexo-n3-label">NOMBRES:</span> <span class="anexo-n3-value">{{ mb_strtoupper(optional($personaNaturalAnexo3)->nombres ?? 'N/A') }}</span></p>
                        {{-- <p><span class="anexo-n3-label">SEXO:</span> <span class="anexo-n3-value">{{ mb_strtoupper(optional($personaNaturalAnexo3)->sexo == 'M' ? 'M' : (optional($personaNaturalAnexo3)->sexo == 'F' ? 'F' : 'N/A')) }}</span></p> --}}                        <p><span class="anexo-n3-label">EDAD:</span> <span class="anexo-n3-value">{{ optional($personaNaturalAnexo3)->edad ?? 'N/A' }}</span></p>
                        <p><span class="anexo-n3-label">CI:</span> <span class="anexo-n3-value">{{ mb_strtoupper(optional($personaNaturalAnexo3)->ci ?? 'N/A') }}</span></p>
                        <p><span class="anexo-n3-label">TELÉFONO:</span> <span class="anexo-n3-value">{{ mb_strtoupper(optional($personaNaturalAnexo3)->telefono ?? 'N/A') }}</span></p>
                        <p><span class="anexo-n3-label">DIRECCIÓN DOMICILIO (COMUNIDAD):</span> <span class="anexo-n3-value">{{ mb_strtoupper(optional($personaNaturalAnexo3)->direccion_domicilio ?? 'N/A') }}</span></p>
                        <p><span class="anexo-n3-label">RELACIÓN/PARENTESCO:</span> <span class="anexo-n3-value">{{ mb_strtoupper(optional($personaNaturalAnexo3)->relacion_parentesco ?? 'N/A') }}</span></p>
                        <p><span class="anexo-n3-label">DIRECCIÓN DE TRABAJO:</span> <span class="anexo-n3-value">{{ mb_strtoupper(optional($personaNaturalAnexo3)->direccion_de_trabajo ?? 'N/A') }}</span></p>
                        <p><span class="anexo-n3-label">OCUPACIÓN:</span> <span class="anexo-n3-value">{{ mb_strtoupper(optional($personaNaturalAnexo3)->ocupacion ?? 'N/A') }}</span></p>
                    </div>
                @endif
            @endforeach
        @else
            <p>NO SE REGISTRARON ANEXOS AL NUMERAL III.</p>
        @endif

        <h2 class="section-title">ANEXO AL NUMERAL V.</h2>
        @php
            $anexosN5 = optional($adulto)->anexoN5;
        @endphp
        @if($anexosN5 && $anexosN5->count() > 0)
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 5%;">N°</th>
                        <th style="width: 15%;">FECHA</th>
                        <th style="width: 30%;">ACCIÓN REALIZADA</th>
                        <th style="width: 30%;">RESULTADO OBTENIDO</th>
                        <th style="width: 20%;">NOMBRE DEL/LA FUNCIONARIO(A) QUE REALIZÓ LA ACCIÓN</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($anexosN5 as $index => $anexo5)
                        <tr>
                            <td class="center">{{ optional($anexo5)->numero ?? 'N/A' }}</td>
                            <td>{{ optional($anexo5)->fecha ? Carbon::parse($anexo5->fecha)->format('d/m/Y') : 'N/A' }}</td>
                            <td>{{ mb_strtoupper(optional($anexo5)->accion_realizada ?? 'N/A') }}</td>
                            <td>{{ mb_strtoupper(optional($anexo5)->resultado_obtenido ?? 'N/A') }}</td>
                            <td>{{ mb_strtoupper(trim(optional(optional($anexo5)->usuario)->persona->nombres ?? '') . ' ' . trim(optional(optional($anexo5)->usuario)->persona->primer_apellido ?? '') . ' ' . trim(optional(optional($anexo5)->usuario)->persona->segundo_apellido ?? '')) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p>NO SE REGISTRARON ANEXOS AL NUMERAL V.</p>
        @endif
    </div>
</body>
</html>