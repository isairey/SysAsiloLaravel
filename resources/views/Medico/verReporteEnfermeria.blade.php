<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Imprimir Planilla de Atención de Enfermería</title>
    
    {{-- Los estilos para impresión deberían ser lo más minimalistas posible --}}
    <style>
        @page {
            size: A4 landscape; /* Establece el tamaño A4 y la orientación horizontal */
            margin: 1.5cm; /* Márgenes ajustados para el modo horizontal */
        }

        body {
            font-family: 'Times New Roman', serif; /* Fuente similar a la de Word */
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            background-color: #f8f9fa; /* Color de fondo claro */
            color: #333;
        }
        .container-print {
            width: 100%;
            /* El width ahora será el largo de A4 en landscape (29.7cm) menos los márgenes */
            /* No necesitamos max-width en print, pero para visualización en pantalla lo podemos mantener */
            max-width: 28cm; /* Ancho ajustado para vista en pantalla, simulando landscape */
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border: 1px solid #ddd;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .header-print {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header-print h2 {
            margin: 5px 0;
            font-size: 1.5em;
            color: #333;
        }
        .header-print h3 {
            margin: 0;
            font-size: 1.2em;
            color: #555;
        }
        .header-print p {
            font-size: 0.9em;
            color: #777;
        }
        .report-info {
            margin-bottom: 20px;
            font-size: 0.9em;
            text-align: center;
        }
        .report-info span {
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 0.85em;
        }
        th, td {
            border: 1px solid #000; /* Bordes negros para simular Excel */
            padding: 8px 5px;
            text-align: center;
            vertical-align: middle;
        }
        th {
            background-color: #e9ecef; /* Un gris claro para los encabezados */
            font-weight: bold;
            white-space: nowrap; /* Evita que el texto de los encabezados se rompa */
        }
        .sub-header th {
            background-color: #dee2e6; /* Un gris un poco más claro para subencabezados */
            font-size: 0.8em;
            padding: 5px;
        }
        .signature-section {
            margin-top: 50px;
            display: flex;
            justify-content: space-around;
            text-align: center;
        }
        .signature-box {
            width: 30%;
            border-top: 1px solid #000;
            padding-top: 10px;
            font-size: 0.9em;
        }
        .footer-print {
            text-align: center;
            margin-top: 40px;
            font-size: 0.8em;
            color: #777;
        }

        /* Estilos específicos para impresión */
        @media print {
            body {
                background-color: #fff;
                padding: 0;
                margin: 0;
            }
            .container-print {
                border: none;
                box-shadow: none;
                padding: 0;
                margin: 0;
                max-width: none; /* Eliminar el max-width para impresión real */
                width: 100%;
            }
            /* Ocultar elementos no deseados en la impresión */
            .no-print {
                display: none !important;
            }
            /* Forzar salto de página si es necesario */
            .page-break {
                page-break-before: always;
            }
            /* Ajustar tamaño de fuente para impresión si es necesario */
            table {
                font-size: 0.75em; /* Un poco más pequeño para caber más */
            }
            th, td {
                padding: 4px 2px; /* Reducir padding para más espacio */
            }
        }
    </style>
    
    {{-- Script para iniciar la impresión automáticamente --}}
    <script>
        window.onload = function() {
            // Un pequeño retraso para asegurar que la página se renderice completamente
            setTimeout(function() {
                window.print();
                // Opcional: Cerrar la ventana después de imprimir si fue abierta con window.open
                // window.close(); 
            }, 500); 
        };
    </script>
</head>
<body>
    <div class="container-print">
        <div class="header-print">
            <h2>GOBIERNO AUTÓNOMO MUNICIPAL DE TARIJA</h2>
            <h3>OFICINA DEL ADULTO MAYOR</h3>
            <p>PLANILLA DE ATENCIÓN DE ENFERMERÍA CORRESPONDIENTE AL MES DE {{ \Carbon\Carbon::createFromFormat('m', $currentMonth)->locale('es')->monthName ?? 'N/A' }}</p>
        </div>

        <div class="report-info">
            <p>Reporte generado el: <span>{{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</span></p>
            @if($request->input('fecha_inicio') && $request->input('fecha_fin'))
                <p>Periodo: <span>{{ \Carbon\Carbon::parse($request->input('fecha_inicio'))->format('d/m/Y') }}</span> al <span>{{ \Carbon\Carbon::parse($request->input('fecha_fin'))->format('d/m/Y') }}</span></p>
            @elseif($request->input('fecha_inicio'))
                <p>Desde: <span>{{ \Carbon\Carbon::parse($request->input('fecha_inicio'))->format('d/m/Y') }}</span></p>
            @elseif($request->input('fecha_fin'))
                <p>Hasta: <span>{{ \Carbon\Carbon::parse($request->input('fecha_fin'))->format('d/m/Y') }}</span></p>
            @endif
            @if($request->input('search'))
                <p>Búsqueda aplicada: <span>"{{ $request->input('search') }}"</span></p>
            @endif
        </div>

        <table>
            <thead>
                <tr>
                    <th rowspan="3">Nº</th>
                    <th rowspan="3">NOMBRES Y APELLIDOS</th>
                    <th colspan="2" rowspan="2">SEXO</th>
                    <th rowspan="3">EDAD</th>
                    <th colspan="10">ATENCIÓN DE ENFERMERÍA</th>
                    <th rowspan="3">FIRMA</th>
                </tr>
                <tr>
                    <th colspan="5" class="sub-header">CONTROL SIGNOS VITALES</th>
                    <th colspan="5" class="sub-header"></th> {{-- Espacio para que coincidan las columnas --}}
                </tr>
                <tr class="sub-header">
                    <th>F</th>
                    <th>M</th>
                    <th>PRESIÓN ARTERIAL</th>
                    <th>FRECUENCIA CARDÍACA</th>
                    <th>FRECUENCIA RESPIRATORIA</th>
                    <th>PULSO</th>
                    <th>TEMPERATURA</th>
                    <th>CONTROL DE INYECTABLES</th>
                    <th>PESO Y TALLA</th>
                    <th>ORIENTACIÓN ALIMENTACIÓN</th>
                    <th>LAVADO DE OÍDO</th>
                    <th>OTROS (TIPO DE TRATAMIENTO)</th>
                    {{-- <th>CURACIÓN</th> No incluído en tu imagen, si lo necesitas añádelo --}}
                    <th>MEDICAMENTOS</th>
                    <th>DERIVACIÓN</th>
                </tr>
            </thead>
            <tbody>
                @forelse($atenciones as $index => $atencion)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td style="text-align: left;">
                        {{ optional($atencion->adulto->persona)->nombres }}
                        {{ optional($atencion->adulto->persona)->primer_apellido }}
                        {{ optional($atencion->adulto->persona)->segundo_apellido }}
                    </td>
                    <td>{{ optional($atencion->adulto->persona)->sexo == 'F' ? 'X' : '' }}</td>
                    <td>{{ optional($atencion->adulto->persona)->sexo == 'M' ? 'X' : '' }}</td>
                    <td>{{ optional($atencion->adulto->persona)->fecha_nacimiento ? \Carbon\Carbon::parse($atencion->adulto->persona->fecha_nacimiento)->age : 'N/A' }}</td>
                    
                    <td>{{ $atencion->presion_arterial ?? '' }}</td>
                    <td>{{ $atencion->frecuencia_cardiaca ?? '' }}</td>
                    <td>{{ $atencion->frecuencia_respiratoria ?? '' }}</td>
                    <td>{{ $atencion->pulso ?? '' }}</td>
                    <td>{{ $atencion->temperatura ?? '' }}</td>
                    <td>{{ $atencion->inyectables ?? '' }}</td>
                    <td>{{ $atencion->peso_talla ?? '' }}</td>
                    <td>{{ $atencion->orientacion_alimentacion ?? '' }}</td>
                    <td>{{ $atencion->lavado_oido ?? '' }}</td>
                    <td>{{ $atencion->otros_tratamiento ?? '' }}</td>
                    {{-- <td>{{ $atencion->curacion ?? '' }}</td> --}}
                    <td>{{ $atencion->medicamentos ?? '' }}</td>
                    <td>{{ $atencion->derivacion ?? '' }}</td>
                    <td></td> {{-- Columna para la firma --}}
                </tr>
                @empty
                <tr>
                    <td colspan="17" style="text-align: center; color: #777;">No hay registros de atención de enfermería para imprimir con los filtros aplicados.</td>
                </tr>
                @endforelse
                
                {{-- Añadir filas vacías si hay pocos registros para rellenar la página --}}
                @php
                    $min_rows = 20; // Número mínimo de filas para la impresión
                    $rows_to_add = $min_rows - count($atenciones);
                @endphp
                @for($i = 0; $i < $rows_to_add; $i++)
                <tr>
                    <td>{{ count($atenciones) + $i + 1 }}</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                @endfor
            </tbody>
        </table>

        <div class="signature-section">
            <div class="signature-box">
                _____________________________<br>
                FIRMA ADULTO MAYOR
            </div>
            <div class="signature-box">
                _____________________________<br>
                FIRMA ENFERMER@
            </div>
            <div class="signature-box">
                _____________________________<br>
                FIRMA ENCARGAD@ OF. ADULTO MAYOR
            </div>
        </div>

        <div class="footer-print">
            Documento generado automáticamente.
        </div>
    </div>
</body>
</html>
