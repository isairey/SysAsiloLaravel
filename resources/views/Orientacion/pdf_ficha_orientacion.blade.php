<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ficha de Orientación - {{ mb_strtoupper(optional($orientacion->adulto->persona)->nombres ?? '') }} {{ mb_strtoupper(optional($orientacion->adulto->persona)->primer_apellido ?? '') }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            margin: 0 auto;
            padding: 20px; /* Mantener el padding original */
            box-sizing: border-box; /* Incluir padding en el ancho */
        }

        /* Nuevo estilo para el encabezado con tabla */
        .header-table {
            width: 100%;
            border-collapse: collapse; /* Eliminar espacio entre celdas */
            margin-bottom: 20px;
        }
        .header-table td {
            vertical-align: top; /* Alinear contenido arriba en las celdas */
            padding: 0;
        }
        .header-table .logo-cell {
            width: 100px; /* Ancho fijo para la celda del logo, ajusta si es necesario */
            text-align: left;
            padding-right: 10px; /* Pequeño espacio a la derecha del logo */
        }
        .header-table .text-cell {
            text-align: center; /* Centrar el texto en su celda */
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

        /* Estilos para el título principal y secciones (manteniendo lo que tenías antes) */
        .title {
            text-align: center;
            font-size: 14pt;
            font-weight: bold;
            margin-top: 20px;
            margin-bottom: 20px;
            padding-bottom: 5px;
            border-bottom: 2px solid black;
        }
        .section-title {
            font-size: 11pt;
            font-weight: bold;
            text-decoration: underline;
            margin-top: 15px;
            margin-bottom: 10px;
        }
        .data-row {
            margin-bottom: 5px;
            /* clear: both; */ /* No necesario si usamos floats consistentemente o no los usamos aquí */
        }
        .data-label {
            font-weight: bold;
            display: inline-block;
            width: 150px; /* Ajusta el ancho según necesidad para alinear los labels */
            vertical-align: top;
        }
        .value {
            display: inline; /* Mantiene el valor en la misma línea */
        }
        .full-width {
            width: 100%;
        }
        .text-center {
            text-align: center;
        }
        .text-justify {
            text-align: justify;
        }
        .underline {
            text-decoration: underline;
        }
        .paragraph-space-small { margin-bottom: 5px; }
        .paragraph-space-medium { margin-bottom: 10px; }
        .paragraph-space-large { margin-bottom: 20px; }
        
        /* Simulación de Checkbox */
        .checkbox-box {
            display: inline-block;
            width: 12px;
            height: 12px;
            border: 1px solid black;
            text-align: center;
            line-height: 12px;
            vertical-align: middle; /* Alinear con el texto */
            margin-right: 5px;
            font-size: 8pt; /* Para que la 'X' quepa bien */
        }

        /* Estilos para los elementos flotantes (Fecha/Caso, Datos Identificación, Firmas) */
        .float-left {
            float: left;
            width: 49%; /* Ajustado un poco para evitar que se solapen */
        }
        .float-right {
            float: right;
            width: 49%; /* Ajustado un poco para evitar que se solapen */
            text-align: right;
        }
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }

        /* Estilos para el área de firmas */
        .signature-area {
            margin-top: 50px;
            width: 100%;
            text-align: center;
        }
        .signature-column-float { /* Mantenemos este nombre de clase para la flotación */
            display: inline-block; /* O usar float: left y float: right si es necesario */
            width: 48%; /* Mantener el ancho para que quepan dos */
            text-align: center;
            vertical-align: top;
        }
        .signature-line {
            display: inline-block;
            width: 250px; /* Ancho de la línea de firma */
            border-bottom: 1px solid black;
            margin-bottom: 5px;
        }
        .signature-text {
            font-weight: bold;
            font-size: 9pt;
        }
    </style>
</head>
<body>
    <div class="container">
        {{-- Encabezado: Usando una tabla para mejor control de layout del logo y el texto --}}
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

        <h1 class="title">FICHA DE ORIENTACIÓN</h1>

        {{-- FECHA DE INGRESO y CASO Nº (usando floats como en la versión anterior preferida) --}}
        <div class="clearfix paragraph-space-medium">
            <div class="float-left">
                <span class="label">FECHA DE INGRESO:</span> 
                <span class="value">{{ mb_strtoupper(optional($orientacion->created_at)->format('d/m/Y H:i') ?? 'N/A') }}</span>
            </div>
            <div class="float-right">
                <span class="label">CASO Nº:</span> 
                <span class="value">{{ mb_strtoupper($orientacion->cod_or ?? 'N/A') }}</span>
            </div>
        </div>

        {{-- Tipos de Orientación (con checkboxes) --}}
        <div class="paragraph-space-medium">
            <div class="paragraph-space-small">
                <span class="label" style="text-decoration: none;">ORIENTACION PSICOLOGICA</span> 
                (<span class="checkbox-box">{{ ($orientacion->tipo_orientacion == 'psicologica') ? 'X' : '' }}</span>)
            </div>
            <div class="paragraph-space-small">
                <span class="label" style="text-decoration: none;">ORIENTACION SOCIAL</span> 
                (<span class="checkbox-box">{{ ($orientacion->tipo_orientacion == 'social') ? 'X' : '' }}</span>)
            </div>
            <div class="paragraph-space-small">
                <span class="label" style="text-decoration: none;">ORIENTACION LEGAL</span> 
                (<span class="checkbox-box">{{ ($orientacion->tipo_orientacion == 'legal') ? 'X' : '' }}</span>)
            </div>
        </div>

        {{-- Sección: DATOS DE IDENTIFICACIÓN DEL ADULTO MAYOR Y/O SOLICITANTE --}}
        <h2 class="section-title">DATOS DE IDENTIFICACION DEL ADULTO MAYOR Y/O SOLICITANTE:</h2>
        <div class="clearfix paragraph-space-medium">
            <div class="float-left">
                <span class="label">NOMBRE COMPLETO:</span> 
                <span class="value">
                    {{ mb_strtoupper(optional($orientacion->adulto->persona)->nombres ?? '') }}
                    {{ mb_strtoupper(optional($orientacion->adulto->persona)->primer_apellido ?? '') }}
                    {{ mb_strtoupper(optional($orientacion->adulto->persona)->segundo_apellido ?? '') }}
                </span>
            </div>
            <div class="float-right">
                <span class="label">EDAD:</span> 
                <span class="value">{{ optional($orientacion->adulto->persona)->edad ?? 'N/A' }}</span>
            </div>
        </div>
        <div class="clearfix paragraph-space-medium">
            <div class="float-left">
                <span class="label">BARRIO/COMUNIDAD:</span> 
                <span class="value">{{ mb_strtoupper(optional($orientacion->adulto->persona)->zona_comunidad ?? 'N/A') }} / {{ mb_strtoupper(optional($orientacion->adulto->persona)->domicilio ?? 'N/A') }}</span>
            </div>
            <div class="float-right">
                <span class="label">TELEFONO:</span> 
                <span class="value">{{ mb_strtoupper(optional($orientacion->adulto->persona)->celular ?? optional($orientacion->adulto->persona)->telefono ?? 'N/A') }}</span>
            </div>
        </div>
        
        {{-- Sección: MOTIVOS DE ORIENTACIÓN --}}
        <h2 class="section-title">MOTIVOS DE ORIENTACION:</h2>
        <p class="text-justify paragraph-space-large">{{ mb_strtoupper($orientacion->motivo_orientacion ?? 'NO ESPECIFICADO.') }}</p>

        {{-- Sección: RESULTADOS OBTENIDOS --}}
        <h2 class="section-title">RESULTADOS OBTENIDOS EN RELACION A LA ENTREVISTA DE ORIENTACION:</h2>
        <p class="text-justify paragraph-space-large">{{ mb_strtoupper($orientacion->resultado_obtenido ?? 'NO ESPECIFICADOS.') }}</p>

        {{-- Nota de Violencia --}}
        <p class="text-center paragraph-space-large" style="font-size: 9pt;">
            EN CASO DE QUE SE IDENTIFIQUE ALGUN TIPO DE VIOLENCIA SE DEBE HACER LA DENUNCIA INMEDIATAMENTE POR LA VÍA CORRESPONDIENTE.
        </p>

        {{-- Firmas --}}
        <div class="signature-area clearfix">
            <div class="signature-column-float">
                <div class="signature-line"></div>
                <div class="signature-text">NOMBRE DEL TECNICO ASIGNADO</div>
            </div>
            <div class="signature-column-float">
                <div class="signature-line"></div>
                <div class="signature-text">FIRMA DEL USUARIO(A)</div>
            </div>
        </div>

    </div> {{-- Fin container --}}
</body>
</html>
