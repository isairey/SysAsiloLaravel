<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Orientación - {{ optional($orientacion->adulto->persona)->nombres }} {{ optional($orientacion->adulto->persona)->primer_apellido }}</title>
    {{-- Solo se enlaza el CSS específico para el reporte --}}
    <link rel="stylesheet" href="{{ asset('css/Orientacion/verReporte.css') }}">
    <style>
        /* Estilos generales para el documento, similares a Word */
        body {
            font-family: 'Times New Roman', serif; /* Fuente similar a la de Word */
            margin: 0;
            padding: 0;
            background-color: #f0f0f0; /* Fondo ligero para la página */
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 100vh;
        }

        .report-container {
            width: 21cm; /* Ancho de una hoja A4 */
            min-height: 29.7cm; /* Alto de una hoja A4 */
            background-color: white;
            padding: 2.5cm; /* Márgenes similares a un documento */
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin: 20px 0;
            box-sizing: border-box; /* Incluir padding en el ancho/alto */
        }

        .report-header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 5px;
        }

        .report-header h1 {
            font-size: 1.4em; /* Tamaño de título */
            font-weight: bold;
            margin: 0;
            padding: 0;
            border-bottom: 2px solid black; /* Línea debajo del título */
            display: inline-block; /* Para que la línea se ajuste al texto */
            padding-bottom: 5px;
        }

        .report-section-inline {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            margin-bottom: 10px;
        }

        .field-group {
            display: flex;
            align-items: baseline;
            flex-wrap: nowrap; /* Evita que los elementos del campo se envuelvan */
        }

        .field-label {
            font-weight: bold;
            white-space: nowrap; /* Evita que la etiqueta se rompa */
            margin-right: 5px;
            font-size: 0.9em;
        }

        .field-value {
            flex-grow: 1; /* Permite que los puntos llenen el espacio restante */
            border-bottom: 1px solid black;
            padding-bottom: 2px;
            font-size: 0.9em;
            white-space: pre-wrap; /* Permite que los puntos se envuelvan */
            word-break: break-all; /* Rompe palabras largas (puntos) para que se ajusten */
            min-width: 50px; /* Ancho mínimo para el campo de valor */
        }

        /* Ajustes específicos para los anchos de grupo de campos */
        .field-group-wide {
            width: 70%; /* Aproximado al ancho del documento */
            display: flex;
            align-items: baseline;
            margin-right: 10px;
        }

        .field-group-narrow {
            width: 28%; /* Aproximado al ancho del documento */
            display: flex;
            align-items: baseline;
        }

        .report-section-block {
            margin-bottom: 20px;
        }

        .section-title {
            font-weight: bold;
            font-size: 0.95em;
            margin-bottom: 5px;
            display: block; /* Asegura que tome su propia línea */
        }

        .type-orientacion .section-title {
            text-align: center;
            margin-bottom: 15px;
            font-size: 1.1em;
            text-decoration: underline; /* Subrayado para "ORIENTACION" */
        }

        .checkbox-group {
            display: flex;
            justify-content: center;
            gap: 40px; /* Espacio entre los checkboxes */
            margin-top: 10px;
            margin-bottom: 20px;
        }

        .checkbox-item {
            display: flex;
            align-items: center;
            font-size: 0.9em;
        }

        .checkbox-box {
            width: 15px;
            height: 15px;
            border: 1px solid black;
            margin-right: 5px;
            display: inline-block;
            box-sizing: border-box;
            vertical-align: middle;
        }

        .checkbox-box.checked {
            background-color: black; /* Relleno para el checked */
        }

        .textarea-field {
            border: 1px solid black; /* Borde completo para el campo de texto */
            padding: 5px;
            min-height: 150px; /* Altura mínima para motivos */
            line-height: 1.5; /* Espaciado entre líneas */
            white-space: pre-wrap; /* Mantiene saltos de línea y espacios */
            word-break: break-all; /* Rompe palabras si son demasiado largas (útil para los puntos) */
            font-size: 0.9em;
            margin-top: 5px; /* Espacio entre título y campo */
        }

        /* Alturas específicas para las áreas de texto */
        .motivos-textarea {
            min-height: 250px; /* Ajusta según la necesidad visual del documento */
        }

        .resultados-textarea {
            min-height: 250px; /* Ajusta según la necesidad visual del documento */
        }

        .violence-note {
            font-size: 0.8em;
            text-align: center;
            margin-top: 30px;
            margin-bottom: 50px;
            font-style: italic;
        }

        .signature-section {
            display: flex;
            justify-content: space-around;
            margin-top: 60px; /* Espacio antes de las firmas */
            text-align: center;
        }

        .signature-block {
            width: 45%; /* Ancho para cada bloque de firma */
            border-top: 1px solid black; /* Línea para la firma */
            padding-top: 5px;
            font-size: 0.85em;
            font-weight: bold;
        }

        /* Media query para impresión */
        @media print {
            body {
                background-color: white;
                margin: 0;
                padding: 0;
                display: block; /* Restablecer para impresión */
                -webkit-print-color-adjust: exact; /* Para imprimir colores de fondo */
                color-adjust: exact;
            }
            .report-container {
                box-shadow: none;
                margin: 0;
                padding: 2.5cm; /* Asegurar márgenes de impresión */
                min-height: auto; /* Dejar que el contenido defina la altura */
            }
            /* Ocultar elementos no deseados en la impresión si los hubiera */
        }
    </style>
</head>
<body>

<div class="report-container">
    <div class="report-header">
        <h1>FICHA DE ORIENTACION</h1>
    </div>

    <div class="report-section-inline">
        <div class="field-group">
            <span class="field-label">FECHA DE INGRESO:</span>
            <span class="field-value">{{ optional($orientacion)->fecha_ingreso ? \Carbon\Carbon::parse($orientacion->fecha_ingreso)->format('d/m/Y') : str_repeat('.', 40) }}</span>
        </div>
        <div class="field-group" style="margin-left: 20px;">
            <span class="field-label">CASO Nº:</span>
            <span class="field-value">{{ optional($orientacion->adulto)->nro_caso ?? str_repeat('.', 30) }}</span>
        </div>
    </div>

    <div class="report-section-block type-orientacion">
        <span class="section-title">ORIENTACION</span>
        <div class="checkbox-group">
            <div class="checkbox-item">
                <span class="checkbox-box @if(optional($orientacion)->tipo_orientacion == 'psicologica') checked @endif"></span>
                <span>PSICOLÓGICA</span>
            </div>
            <div class="checkbox-item">
                <span class="checkbox-box @if(optional($orientacion)->tipo_orientacion == 'social') checked @endif"></span>
                <span>SOCIAL</span>
            </div>
            <div class="checkbox-item">
                <span class="checkbox-box @if(optional($orientacion)->tipo_orientacion == 'legal') checked @endif"></span>
                <span>LEGAL</span>
            </div>
        </div>
    </div>

    <div class="report-section-block">
        <h2 class="section-title">DATOS DE IDENTIFICACION DEL ADULTO MAYOR Y/O SOLICITANTE:</h2>
        <div class="report-section-inline">
            <div class="field-group-wide">
                <span class="field-label">NOMBRE COMPLETO:</span>
                <span class="field-value">
                    {{ optional($orientacion->adulto->persona)->nombres }}
                    {{ optional($orientacion->adulto->persona)->primer_apellido }}
                    {{ optional($orientacion->adulto->persona)->segundo_apellido }}
                    @if(!optional($orientacion->adulto->persona)->nombres && !optional($orientacion->adulto->persona)->primer_apellido)
                        {{ str_repeat('.', 100) }}
                    @endif
                </span>
            </div>
            <div class="field-group-narrow">
                <span class="field-label">EDAD:</span>
                <span class="field-value">{{ optional($orientacion->adulto->persona)->edad ?? str_repeat('.', 10) }}</span>
            </div>
        </div>
        <div class="report-section-inline">
            <div class="field-group-wide">
                <span class="field-label">BARRIO/COMUNIDAD:</span>
                <span class="field-value">
                    {{ optional($orientacion->adulto->persona)->domicilio }}
                    @if(optional($orientacion->adulto->persona)->domicilio && optional($orientacion->adulto->persona)->zona_comunidad) / @endif
                    {{ optional($orientacion->adulto->persona)->zona_comunidad }}
                    @if(!optional($orientacion->adulto->persona)->domicilio && !optional($orientacion->adulto->persona)->zona_comunidad)
                        {{ str_repeat('.', 100) }}
                    @endif
                </span>
            </div>
            <div class="field-group-narrow">
                <span class="field-label">TELEFONO:</span>
                <span class="field-value">{{ optional($orientacion->adulto->persona)->telefono ?? str_repeat('.', 20) }}</span>
            </div>
        </div>
    </div>

    <div class="report-section-block">
        <h2 class="section-title">MOTIVOS DE ORIENTACION</h2>
        <div class="textarea-field motivos-textarea">
            {{ optional($orientacion)->motivo_orientacion ?? str_repeat('.', 1000) }}
        </div>
    </div>

    <div class="report-section-block">
        <h2 class="section-title">RESULTADOS OBTENIDOS EN RELACION A LA ENTREVISTA DE ORIENTACION</h2>
        <div class="textarea-field resultados-textarea">
            {{ optional($orientacion)->resultado_obtenido ?? str_repeat('.', 1000) }}
        </div>
    </div>

    <div class="violence-note">
        EN CASO DE QUE SE IDENTIFIQUE ALGUN TIPO DE VIOLENCIA SE DEBE HACER LA DENUNCIA INMEDIATAMENTE POR LA VIA CORRESPONDIENTE.
    </div>

    <div class="signature-section">
        <div class="signature-block">
            NOMBRE DEL TECNICO ASIGNADO
            {{-- Aquí podrías añadir el nombre del técnico si lo tienes en el objeto $orientacion --}}
            {{-- Ejemplo: {{ optional($orientacion->usuario->persona)->nombres }} {{ optional($orientacion->usuario->persona)->primer_apellido }} --}}
        </div>
        <div class="signature-block">
            FIRMA DEL USUARIO(A)
        </div>
    </div>
</div>

{{-- Script para reemplazar iconos Feather (no necesario para impresión estática, pero se mantiene si dashboard.css lo usa) --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
});
</script>

</body>
</html>
