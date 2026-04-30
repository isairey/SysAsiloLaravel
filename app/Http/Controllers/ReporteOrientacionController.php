<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Orientacion; // Importa el modelo Orientacion
use App\Models\AdultoMayor; // Importa el modelo AdultoMayor
use App\Models\Persona; // Importa el modelo Persona (si lo usas para filtrar datos de la persona)
use Illuminate\Support\Facades\Log; // Para depuración, si es necesario
use Carbon\Carbon;
// Para la exportación a Word
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\SimpleType\VerticalJc;
use PhpOffice\PhpWord\SimpleType\TblWidth;
use PhpOffice\PhpWord\SimpleType\Jc; // Importar para alineación de texto y elementos (START, END, CENTER)
use PhpOffice\PhpWord\Style\Image; // ¡Nueva importación para POS_HORIZONTAL_LEFT y POS_VERTICAL_TOP!
    // Para la generación de PDF con Dompdf
use Barryvdh\DomPDF\Facade\Pdf; // Importa el Facade de DomPDF


class ReporteOrientacionController extends Controller
{
    /**
     * Muestra la lista de fichas de orientación con filtros y buscador.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Obtener los valores de los filtros de la solicitud
        $search = $request->input('search'); // Buscador general
        $cod_or_filter = $request->input('cod_or_filter'); // Filtro por número de ficha de orientación
        $tipo_orientacion_filter = $request->input('tipo_orientacion_filter'); // Filtro por tipo de orientación

        // Construir la consulta base para las Orientaciones
        // Eager load AdultoMayor y Persona para acceder a sus datos
        $query = Orientacion::with('adulto.persona');

        // Aplicar filtro por número de ficha de orientación (cod_or)
        if (!empty($cod_or_filter)) {
            $query->where('cod_or', 'like', '%' . $cod_or_filter . '%');
        }

        // Aplicar filtro por tipo de orientación
        if (!empty($tipo_orientacion_filter)) {
            $query->where('tipo_orientacion', $tipo_orientacion_filter);
        }

        // Aplicar buscador general (en motivos, resultados, nombre de adulto, CI)
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('motivo_orientacion', 'like', '%' . $search . '%')
                  ->orWhere('resultado_obtenido', 'like', '%' . $search . '%')
                  ->orWhereHas('adulto.persona', function ($qr) use ($search) {
                      $qr->where('nombres', 'like', '%' . $search . '%')
                         ->orWhere('primer_apellido', 'like', '%' . $search . '%')
                         ->orWhere('segundo_apellido', 'like', '%' . $search . '%')
                         ->orWhere('ci', 'like', '%' . $search . '%');
                  });
            });
        }

        // Ordenar los resultados (ej. por fecha de ingreso descendente)
        $orientaciones = $query->orderBy('fecha_ingreso', 'desc')->paginate(10); // Paginación

        // Opciones para el filtro de tipo de orientación (debe coincidir con el enum de la DB)
        $tiposOrientacion = ['psicologica' => 'PSICOLÓGICA', 'social' => 'SOCIAL', 'legal' => 'LEGAL'];


        // Retornar la vista con las orientaciones y los valores de los filtros para mantenerlos en la interfaz
        return view('Orientacion.indexRep', compact('orientaciones', 'search', 'cod_or_filter', 'tipo_orientacion_filter', 'tiposOrientacion'));
    }
/**
     * Muestra el reporte detallado de una ficha de orientación para impresión.
     * Esta función reemplaza la lógica que estaba en RegistrarFichaController.
     *
     * @param int $cod_or El ID de la ficha de orientación a mostrar.
     * @return \Illuminate\View\View
     */
    public function showReporte($cod_or)
    {
        // Cargar la orientación con sus relaciones necesarias (adulto y persona)
        $orientacion = Orientacion::with('adulto.persona')->findOrFail($cod_or);
        $adulto = $orientacion->adulto; // Obtenemos el objeto adulto asociado a la orientación

        // Retorna la vista 'verReporte' con los datos
        return view('Orientacion.verReporte', compact('orientacion', 'adulto'));
    }

    /**
     * Elimina una ficha de orientación específica de la base de datos.
     *
     * @param  int  $cod_or  El ID de la ficha de orientación.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($cod_or)
    {
        try {
            $orientacion = Orientacion::findOrFail($cod_or);
            $orientacion->delete();
            return redirect()->route('legal.reportes_orientacion.index')->with('success', 'Ficha de orientación eliminada exitosamente.');
        } catch (\Exception $e) {
            Log::error('Error al eliminar ficha de orientación: ' . $e->getMessage());
            return redirect()->route('legal.reportes_orientacion.index')->with('error', 'Error al eliminar la ficha de orientación. Por favor, intente de nuevo.');
        }
    }
    /**
     * Exporta una ficha de Orientación individual a un archivo Word (.docx).
     *
     * @param int $cod_or El código de la ficha de orientación a exportar.
     * @return \Symfony\Component\HttpFoundation\StreamedResponse|\Illuminate\Http\RedirectResponse
     */
    public function exportarFichaOrientacionWordIndividual(int $cod_or)
    {
        try {
            // Cargar la ficha de orientación con sus relaciones necesarias
            $orientacion = Orientacion::with('adulto.persona')->findOrFail($cod_or);
            
            // Acceso simplificado a los datos relacionados
            $adulto = $orientacion->adulto;
            $persona = optional($adulto)->persona;

            $phpWord = new PhpWord();
            $section = $phpWord->addSection();

            // --- Estilos de fuente ---
            $phpWord->addFontStyle('headerStyle', ['name' => 'Arial', 'size' => 10, 'bold' => true]);
            $phpWord->addFontStyle('mainTitleStyle', ['name' => 'Arial', 'size' => 14, 'bold' => true]);
            $phpWord->addFontStyle('sectionTitleStyle', ['name' => 'Arial', 'size' => 11, 'bold' => true, 'underline' => 'single']);
            $phpWord->addFontStyle('labelStyle', ['name' => 'Arial', 'size' => 10, 'bold' => true]);
            $phpWord->addFontStyle('valueStyle', ['name' => 'Arial', 'size' => 10]);
            $phpWord->addFontStyle('checkboxStyle', ['name' => 'Arial', 'size' => 10]);
            $phpWord->addFontStyle('signatureStyle', ['name' => 'Arial', 'size' => 10, 'bold' => true]);

            // --- Estilos de párrafo ---
            $phpWord->addParagraphStyle('P_Center', ['align' => Jc::CENTER, 'spaceAfter' => 0, 'spaceBefore' => 0]);
            $phpWord->addParagraphStyle('P_Start', ['align' => Jc::START, 'spaceAfter' => 0, 'spaceBefore' => 0]);
            $phpWord->addParagraphStyle('P_End', ['align' => Jc::END, 'spaceAfter' => 0, 'spaceBefore' => 0]);
            $phpWord->addParagraphStyle('P_Indent', ['indentation' => ['left' => 360], 'spaceAfter' => 0, 'spaceBefore' => 0]); 
            $phpWord->addParagraphStyle('P_SpacingSmall', ['spaceAfter' => 60]); 
            $phpWord->addParagraphStyle('P_SpacingMedium', ['spaceAfter' => 120]); 
            $phpWord->addParagraphStyle('P_SpacingLarge', ['spaceAfter' => 240]); 

            // --- Configuración del Encabezado (Header) ---
            $header = $section->addHeader();
            $tableHeader = $header->addTable(['width' => 10000, 'unit' => TblWidth::TWIP]); 
            $tableHeader->addRow();

            // Celda para el logo (izquierda)
            $logoCell = $tableHeader->addCell(2000, ['valign' => VerticalJc::TOP]); 
            $logoPath = public_path('assets/images/brand/alcaldiaicon.png'); 
            if (file_exists($logoPath)) {
                $logoCell->addImage(
                    $logoPath,
                    [
                        'width'            => 80,   
                        'height'           => 80,   
                        'alignment'        => Jc::START,
                        'marginTop'        => 0,
                        'marginLeft'       => 0,
                        'wrappingStyle'    => 'tight', 
                        'positioning'      => 'absolute', 
                        'posHorizontal'    => Image::POSITION_HORIZONTAL_LEFT,
                        'posHorizontalRel' => 'page', 
                        'posVertical'      => Image::POSITION_VERTICAL_TOP,
                        'posVerticalRel'   => 'page', 
                    ]
                );
            } else {
                Log::warning('Logo no encontrado en: ' . $logoPath);
                $logoCell->addText(' [LOGO FALTANTE] ', 'valueStyle', 'P_Start');
            }

            // Celda para el texto del encabezado (derecha)
            $textCell = $tableHeader->addCell(8000); 
            $textCell->addText('GOBIERNO AUTONOMO MUNICIPAL DE TARIJA', 'headerStyle', 'P_Center');
            $textCell->addText('OFICINA DEL ADULTO MAYOR', 'headerStyle', 'P_Center');
            $textCell->addTextBreak(1); 

            // La "FICHA DE ORIENTACION" principal la colocamos directamente en el cuerpo
            $section->addText('FICHA DE ORIENTACION', 'mainTitleStyle', 'P_Center');
            $section->addTextBreak(1); 

            // FECHA DE INGRESO y CASO Nº
            $fechaIngreso = optional($orientacion->created_at)->format('d/m/Y H:i') ?? 'N/A'; // Usar created_at como fecha de ingreso si es la de registro
            $casoNro = $orientacion->cod_or ?? 'N/A'; 
            $tableFechaCaso = $section->addTable(['width' => 9500, 'unit' => TblWidth::TWIP]);
            $tableFechaCaso->addRow();
            $tableFechaCaso->addCell(4750)->addText('FECHA DE INGRESO: ' . mb_strtoupper($fechaIngreso), 'valueStyle', 'P_Start');
            $tableFechaCaso->addCell(4750)->addText('CASO Nº: ' . mb_strtoupper($casoNro), 'valueStyle', 'P_End');
            $section->addTextBreak(1);

            // Tipos de Orientación (replicando checkboxes)
            $tipoPsicologica = ($orientacion->tipo_orientacion == 'psicologica') ? 'X' : ' ';
            $tipoSocial = ($orientacion->tipo_orientacion == 'social') ? 'X' : ' ';
            $tipoLegal = ($orientacion->tipo_orientacion == 'legal') ? 'X' : ' ';

            $section->addText('ORIENTACION PSICOLOGICA ( ' . $tipoPsicologica . ' )');
            $section->addText('ORIENTACION SOCIAL       ( ' . $tipoSocial . ' )');
            $section->addText('ORIENTACION LEGAL         ( ' . $tipoLegal . ' )');
            $section->addTextBreak(1);

            // Datos de Identificación del Adulto Mayor
            $section->addText('DATOS DE IDENTIFICACION DEL ADULTO MAYOR Y/O SOLICITANTE:', 'sectionTitleStyle', 'P_Start');
            $section->addTextBreak(1); 

            $tableDatosIdentificacion = $section->addTable([
                'borderColor' => 'FFFFFF',
                'borderSize' => 0,
                'cellMargin' => 0,
                'alignment' => Jc::START,
                'width' => 9500, 
                'unit' => TblWidth::TWIP,
            ]); 

            $nombreCompleto = trim(
                mb_strtoupper(optional($persona)->nombres ?? '') . ' ' .
                mb_strtoupper(optional($persona)->primer_apellido ?? '') . ' ' .
                mb_strtoupper(optional($persona)->segundo_apellido ?? '')
            );
            $nombreCompleto = $nombreCompleto ?: 'N/A';
            $edad = optional($persona)->edad ?? 'N/A'; // Edad es numérica, no se convierte a mayúsculas

            $barrioComunidad = mb_strtoupper(optional($persona)->zona_comunidad ?? 'N/A') . ' / ' . mb_strtoupper(optional($persona)->domicilio ?? 'N/A');
            $telefono = mb_strtoupper(optional($persona)->telefono ?? 'N/A'); // Asumiendo que teléfono/celular podría ser string

            // Primera fila de la tabla de identificación: NOMBRE COMPLETO y EDAD
            $tableDatosIdentificacion->addRow();
            $tableDatosIdentificacion->addCell(6000)->addText('NOMBRE COMPLETO: ' . $nombreCompleto, 'valueStyle', 'P_Start');
            $tableDatosIdentificacion->addCell(3500)->addText('EDAD: ' . $edad, 'valueStyle', 'P_Start');
            
            // Segunda fila de la tabla de identificación: BARRIO/COMUNIDAD y TELEFONO
            $tableDatosIdentificacion->addRow();
            $tableDatosIdentificacion->addCell(6000)->addText('BARRIO/COMUNIDAD: ' . $barrioComunidad, 'valueStyle', 'P_Start');
            $tableDatosIdentificacion->addCell(3500)->addText('TELEFONO: ' . $telefono, 'valueStyle', 'P_Start');
            $section->addTextBreak(2); 

            // Motivos de Orientación
            $section->addText('MOTIVOS DE ORIENTACION', 'labelStyle', 'P_Start');
            $motivoOrientacion = mb_strtoupper($orientacion->motivo_orientacion ?? 'NO ESPECIFICADO.');
            $section->addText($motivoOrientacion, 'valueStyle', 'P_Start');
            $section->addTextBreak(5); 

            // Resultados Obtenidos
            $section->addText('RESULTADOS OBTENIDOS EN RELACION A LA ENTREVISTA DE ORIENTACION', 'labelStyle', 'P_Start');
            $resultadosObtenidos = mb_strtoupper($orientacion->resultado_obtenido ?? 'NO ESPECIFICADOS.');
            $section->addText($resultadosObtenidos, 'valueStyle', 'P_Start');
            $section->addTextBreak(5); 

            // Nota de Violencia
            $section->addTextBreak(1); 
            $section->addText('EN CASO DE QUE SE IDENTIFIQUE ALGUN TIPO DE VIOLENCIA SE DEBE HACER LA DENUNCIA INMEDIATAMENTE POR LA VÍA CORRESPONDIENTE.', null, 'P_Center');
            $section->addTextBreak(2);

            // Firmas
            $tableFirmas = $section->addTable([
                'borderColor' => 'FFFFFF',
                'borderSize' => 0,
                'cellMargin' => 0,
                'alignment' => Jc::CENTER,
                'width' => 9500,
                'unit' => TblWidth::TWIP,
            ]);

            $tableFirmas->addRow();
            $tableFirmas->addCell(4750)->addText('__________________________________', 'valueStyle', 'P_Center');
            $tableFirmas->addCell(4750)->addText('__________________________________', 'valueStyle', 'P_Center');
            $tableFirmas->addRow();
            $tableFirmas->addCell(4750)->addText('NOMBRE DEL TECNICO ASIGNADO', 'signatureStyle', 'P_Center');
            $tableFirmas->addCell(4750)->addText('FIRMA DEL USUARIO(A)', 'signatureStyle', 'P_Center');


            // Preparar el archivo para la descarga
            $objWriter = IOFactory::createWriter($phpWord, 'Word2007'); 

            $nombreAdulto = mb_strtoupper(trim(
                (optional($persona)->nombres ?? '') . '_' . 
                (optional($persona)->primer_apellido ?? '')
            ));
            $fileName = 'FICHA_ORIENTACION_' . mb_strtoupper($orientacion->cod_or) . '_' . $nombreAdulto . '_' . Carbon::now()->format('Ymd') . '.docx';
            $fileName = str_replace([' ', '/', '\\', ':', '*', '?', '"', '<', '>', '|'], '_', $fileName);
            $fileName = substr($fileName, 0, 200); 

            $response = new \Symfony\Component\HttpFoundation\StreamedResponse(function() use ($objWriter) {
                $objWriter->save('php://output');
            });

            $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');
            $response->headers->set('Content-Disposition', 'attachment;filename="' . $fileName . '"');
            $response->headers->set('Cache-Control', 'max-age=0');

            return $response;

        } catch (\Exception $e) {
            Log::error('Error al generar Word de la Ficha de Orientación individual (cod_or: ' . $cod_or . '): ' . $e->getMessage(), ['exception' => $e]);
            return back()->with('error', 'Ocurrió un error al generar el Word de la Ficha de Orientación: ' . $e->getMessage());
        }
    }
    public function exportarFichaOrientacionPdfIndividual(int $cod_or)
        {
            try {
                // Cargar la ficha de orientación con sus relaciones necesarias
                $orientacion = Orientacion::with('adulto.persona')->findOrFail($cod_or);
                
                // Preparar los datos para la vista del PDF.
                // Aquí podrías procesar los datos para convertirlos a mayúsculas si es necesario,
                // aunque la vista Blade del PDF también puede manejarlo con mb_strtoupper().
                $data = [
                    'orientacion' => $orientacion,
                ];

                // Cargar la vista Blade y generar el PDF
                $pdf = Pdf::loadView('Orientacion.pdf_ficha_orientacion', $data);

                // Configurar el nombre del archivo
                $nombreAdulto = mb_strtoupper(trim(
                    (optional($orientacion->adulto->persona)->nombres ?? '') . '_' . 
                    (optional($orientacion->adulto->persona)->primer_apellido ?? '')
                ));
                $fileName = 'FICHA_ORIENTACION_' . mb_strtoupper($orientacion->cod_or) . '_' . $nombreAdulto . '_' . Carbon::now()->format('Ymd_His') . '.pdf';
                // Asegurar un nombre de archivo válido
                $fileName = str_replace([' ', '/', '\\', ':', '*', '?', '"', '<', '>', '|'], '_', $fileName);
                $fileName = substr($fileName, 0, 200); 

                // Retornar el PDF para descarga
                return $pdf->download($fileName);

            } catch (\Exception $e) {
                Log::error('Error al generar PDF de la Ficha de Orientación individual (cod_or: ' . $cod_or . '): ' . $e->getMessage(), ['exception' => $e]);
                return back()->with('error', 'Ocurrió un error al generar el PDF de la Ficha de Orientación: ' . $e->getMessage());
            }
        }
}
