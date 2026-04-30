<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Fisioterapia;
use App\Models\Kinesiologia;
use App\Models\AdultoMayor;
use App\Models\HistoriaClinica;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;
// Para la exportación a Excel
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
// Para la exportación a Word
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\SimpleType\VerticalJc;
use PhpOffice\PhpWord\SimpleType\TblWidth;
use PhpOffice\PhpWord\Style\Cell;


class ReporteFisioKineController extends Controller
{
    /**
     * Muestra el listado de Fichas de Fisioterapia registradas con filtros.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function indexReporteFisio(Request $request)
    {
        // 1. Obtener todos los valores de los filtros desde la petición.
        $search = $request->input('search');
        $mes = $request->input('mes');
        $anio = $request->input('anio');

        try {
            // 2. Iniciar la consulta base con las relaciones necesarias para optimizar.
            $query = Fisioterapia::with('adulto.persona', 'usuario.persona');

            // 3. Aplicar filtro de búsqueda general si existe.
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('motivo_consulta', 'like', '%' . $search . '%')
                      ->orWhere('equipos', 'like', '%' . $search . '%')
                      ->orWhereHas('adulto.persona', function ($qr) use ($search) {
                          $qr->where('nombres', 'like', '%' . $search . '%')
                             ->orWhere('primer_apellido', 'like', '%' . $search . '%')
                             ->orWhere('segundo_apellido', 'like', '%' . $search . '%')
                             ->orWhere('ci', 'like', '%' . $search . '%');
                      });
                });
            }

            // 4. Aplicar filtro por mes si se ha seleccionado uno.
            if ($mes) {
                $query->whereMonth('fecha_programacion', $mes);
            }

            // 5. Aplicar filtro por año si se ha seleccionado uno.
            if ($anio) {
                $query->whereYear('fecha_programacion', $anio);
            }

            // 6. Obtener la lista de años únicos para el menú desplegable del filtro.
            $years = Fisioterapia::selectRaw('EXTRACT(YEAR FROM fecha_programacion) as year')
                ->whereNotNull('fecha_programacion')
                ->distinct()
                ->orderBy('year', 'desc')
                ->pluck('year');

            // 7. Paginar los resultados y ordenar por fecha más reciente.
            $fichasFisioterapia = $query->orderBy('fecha_programacion', 'desc')->paginate(10)->appends($request->query());
            
            // 8. Contar el total de fichas (sin filtros) para la tarjeta de resumen.
            $totalFichasFisioterapia = Fisioterapia::count();

            // 9. Devolver la vista con todas las variables necesarias.
            return view('Medico.indexRepFisio', compact(
                'fichasFisioterapia', 
                'totalFichasFisioterapia', 
                'search', 
                'years', 
                'mes', 
                'anio'
            ));

        } catch (\Exception $e) {
            Log::error('Error en ReporteFisioKineController@indexReporteFisio: ' . $e->getMessage(), ['exception' => $e]);
            return back()->with('error', 'Ocurrió un error al cargar las fichas de fisioterapia. Por favor, intente de nuevo más tarde.');
        }
    }

    /**
     * Muestra los detalles de una ficha de Fisioterapia.
     *
     * @param int $cod_fisio
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function showFisio($cod_fisio)
    {
        try {
            $fisioterapia = Fisioterapia::with('adulto.persona', 'historiaClinica', 'usuario.persona')->findOrFail($cod_fisio);
            return view('Medico.verDetallesFichaFisio', compact('fisioterapia'));
        } catch (\Exception $e) {
            Log::error('Error en ReporteFisioKineController@showFisio: ' . $e->getMessage(), ['cod_fisio' => $cod_fisio, 'exception' => $e]);
            return back()->with('error', 'No se pudo cargar los detalles de la ficha de Fisioterapia.');
        }
    }

    /**
     * Elimina una ficha de Fisioterapia.
     *
     * @param int $cod_fisio
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroyFisio($cod_fisio)
    {
        try {
            $fisioterapia = Fisioterapia::findOrFail($cod_fisio);
            $fisioterapia->delete();
            return redirect()->route('responsable.fisioterapia.reportefisio.index')->with('success', 'Ficha de Fisioterapia eliminada exitosamente.');
        } catch (\Exception $e) {
            Log::error('Error al eliminar Ficha de Fisioterapia: ' . $e->getMessage(), ['cod_fisio' => $cod_fisio, 'exception' => $e]);
            return back()->with('error', 'Error al eliminar la Ficha de Fisioterapia: ' . $e->getMessage());
        }
    }

    public function exportarFichaFisioWordIndividual(int $cod_fisio)
    {
        try {
            $fichaFisio = Fisioterapia::with('adulto.persona', 'historiaClinica', 'usuario.persona')->findOrFail($cod_fisio);
            $adulto = $fichaFisio->adulto;
            $persona = optional($adulto)->persona;
            $historiaClinica = $fichaFisio->historiaClinica; 
            $usuarioRegistro = optional($fichaFisio->usuario)->persona;

            $phpWord = new PhpWord();
            
            $section = $phpWord->addSection(['marginLeft' => 720, 'marginRight' => 720, 'marginTop' => 720, 'marginBottom' => 720]);

            $phpWord->addFontStyle('headerStyle', ['name' => 'Calibri', 'size' => 10, 'bold' => true, 'color' => '1A3C34']);
            $phpWord->addFontStyle('cityHeaderStyle', ['name' => 'Calibri', 'size' => 12, 'bold' => true, 'color' => '1A3C34']);
            $phpWord->addFontStyle('mainTitleStyle', ['name' => 'Calibri', 'size' => 16, 'bold' => true, 'color' => '1A3C34']);
            $phpWord->addFontStyle('sectionTitleStyle', ['name' => 'Calibri', 'size' => 11, 'bold' => true, 'color' => '1A3C34', 'underline' => 'single']);
            $phpWord->addFontStyle('labelStyle', ['name' => 'Calibri', 'size' => 10, 'bold' => true, 'color' => '1A3C34']);
            $phpWord->addFontStyle('valueStyle', ['name' => 'Calibri', 'size' => 10, 'color' => '333333']);
            $phpWord->addFontStyle('checkboxStyle', ['name' => 'Calibri', 'size' => 10, 'color' => '333333']);
            $phpWord->addFontStyle('signatureStyle', ['name' => 'Calibri', 'size' => 10, 'bold' => true, 'color' => '1A3C34']);
            $phpWord->addFontStyle('normalText', ['name' => 'Calibri', 'size' => 10, 'color' => '333333']);
            $phpWord->addFontStyle('footerStyle', ['name' => 'Calibri', 'size' => 9, 'color' => '666666']);

            $phpWord->addParagraphStyle('P_Center', ['alignment' => 'center', 'spaceAfter' => 100, 'spaceBefore' => 100]);
            $phpWord->addParagraphStyle('P_Left', ['alignment' => 'left', 'spaceAfter' => 80, 'spaceBefore' => 80]);
            $phpWord->addParagraphStyle('P_Right', ['alignment' => 'right', 'spaceAfter' => 80, 'spaceBefore' => 80]);
            $phpWord->addParagraphStyle('P_Indent', ['indentation' => ['left' => 400], 'spaceAfter' => 80, 'spaceBefore' => 80]);
            $phpWord->addParagraphStyle('P_NoSpace', ['spaceAfter' => 0, 'spaceBefore' => 0]);
            $phpWord->addParagraphStyle('P_SectionSpace', ['spaceAfter' => 300, 'spaceBefore' => 200]);

            $tableStyle = ['borderColor' => 'B0B0B0', 'borderSize' => 6, 'cellMargin' => 100, 'alignment' => 'center', 'width' => 10000, 'unit' => TblWidth::TWIP];
            $phpWord->addTableStyle('mainTableStyle', $tableStyle);

            $sectionHeaderCellStyle = ['valign' => VerticalJc::CENTER, 'bgColor' => 'F5F6F5', 'borderBottomSize' => 12, 'borderBottomColor' => '1A3C34'];
            $phpWord->addTableStyle('sectionHeaderTableStyle', ['borderColor' => 'B0B0B0', 'borderSize' => 6, 'cellMargin' => 100, 'alignment' => 'center', 'width' => 10000, 'unit' => TblWidth::TWIP]);

            $cellStyleNoBorder = ['borderSize' => 0, 'cellMargin' => 80, 'valign' => VerticalJc::CENTER];
            $phpWord->addTableStyle('noBorderTableStyle', ['borderSize' => 0, 'cellMargin' => 80, 'alignment' => 'left', 'width' => 10000, 'unit' => TblWidth::TWIP]);

            $header = $section->addHeader();
            $headerTable = $header->addTable(['width' => 10000, 'unit' => TblWidth::TWIP]);
            $headerTable->addRow();
            $cellTitles = $headerTable->addCell(10000, ['valign' => VerticalJc::CENTER]);
            $cellTitles->addText('ALCALDÍA DE TARIJA', 'cityHeaderStyle', 'P_Center');
            $cellTitles->addText('CENTRO DE ATENCIÓN MUNICIPAL DEL ADULTO MAYOR ' . Carbon::now()->year, 'headerStyle', 'P_Center');
            $header->addTextBreak(1);

            $footer = $section->addFooter();
            $footerTable = $footer->addTable(['width' => 10000, 'unit' => TblWidth::TWIP]);
            $footerTable->addRow();
            $footerTable->addCell(10000)->addPreserveText('Página {PAGE} de {NUMPAGES}', 'footerStyle', 'P_Center');

            $section->addTextBreak(1);
            $section->addText('FICHA DE INGRESO DEL ADULTO MAYOR EN EL CTAM', 'mainTitleStyle', 'P_Center');
            $section->addTextBreak(2);

            $sectionHeaderTable = $section->addTable('sectionHeaderTableStyle');
            $sectionHeaderTable->addRow();
            $sectionHeaderTable->addCell(10000, $sectionHeaderCellStyle)->addText('I. DATOS PERSONALES DEL ADULTO MAYOR', 'sectionTitleStyle', 'P_Left');
            $section->addTextBreak(1);

            $tablePersonal = $section->addTable('noBorderTableStyle');
            $addRow = function ($table, $label, $value, $labelWidth = 3500, $valueWidth = 6500) use ($cellStyleNoBorder) {
                $table->addRow(400);
                $table->addCell($labelWidth, $cellStyleNoBorder)->addText(mb_strtoupper($label), 'labelStyle', 'P_NoSpace');
                $table->addCell($valueWidth, $cellStyleNoBorder)->addText(mb_strtoupper($value ?? 'N/A'), 'valueStyle', 'P_NoSpace');
            };

            $addRow($tablePersonal, 'NOMBRE COMPLETO', (optional($persona)->nombres ?? 'N/A') . ' ' . (optional($persona)->primer_apellido ?? 'N/A') . ' ' . (optional($persona)->segundo_apellido ?? ''));
            
            $tablePersonal->addRow(400);
            $tablePersonal->addCell(3500, $cellStyleNoBorder)->addText('FECHA DE NACIMIENTO', 'labelStyle', 'P_NoSpace');
            $tablePersonal->addCell(2500, $cellStyleNoBorder)->addText((optional($persona)->fecha_nacimiento ? Carbon::parse($persona->fecha_nacimiento)->format('d/m/Y') : 'N/A'), 'valueStyle', 'P_NoSpace');
            $tablePersonal->addCell(1500, $cellStyleNoBorder)->addText('EDAD', 'labelStyle', 'P_NoSpace');
            $tablePersonal->addCell(2000, $cellStyleNoBorder)->addText((optional($persona)->fecha_nacimiento ? Carbon::parse($persona->fecha_nacimiento)->age : 'N/A'), 'valueStyle', 'P_NoSpace');

            $tablePersonal->addRow(400);
            $tablePersonal->addCell(3500, $cellStyleNoBorder)->addText('NÚMERO DE CÉDULA', 'labelStyle', 'P_NoSpace');
            $tablePersonal->addCell(2500, $cellStyleNoBorder)->addText(mb_strtoupper(optional($persona)->ci ?? 'N/A'), 'valueStyle', 'P_NoSpace');
            $tablePersonal->addCell(1500, $cellStyleNoBorder)->addText('TELÉFONO', 'labelStyle', 'P_NoSpace');
            $tablePersonal->addCell(2000, $cellStyleNoBorder)->addText(mb_strtoupper(optional($persona)->telefono ?? 'N/A'), 'valueStyle', 'P_NoSpace');

            $addRow($tablePersonal, 'DOMICILIO', (optional($persona)->zona_comunidad ?? 'N/A') . ', ' . (optional($persona)->domicilio ?? 'N/A'));
            $addRow($tablePersonal, 'CON QUIÉN VIVE', optional($adulto)->vive_con ?? 'N/A');
            $addRow($tablePersonal, 'ESTADO CIVIL', optional($persona)->estado_civil ?? 'N/A');
            $addRow($tablePersonal, 'GRADO DE INSTRUCCIÓN', optional($historiaClinica)->grado_instruccion ?? 'N/A');
            $addRow($tablePersonal, 'OCUPACIÓN', optional($historiaClinica)->ocupacion ?? 'N/A');
            $addRow($tablePersonal, 'NÚMEROS DE EMERGENCIA', optional($fichaFisio)->num_emergencia ?? 'N/A');
            $section->addTextBreak(2);

            $sectionHeaderTable = $section->addTable('sectionHeaderTableStyle');
            $sectionHeaderTable->addRow();
            $sectionHeaderTable->addCell(10000, $sectionHeaderCellStyle)->addText('II. SITUACIÓN DE SALUD', 'sectionTitleStyle', 'P_Left');
            $section->addTextBreak(1);

            $tableSalud = $section->addTable('noBorderTableStyle');
            $tableSalud->addRow(400);
            $tableSalud->addCell(3500, $cellStyleNoBorder)->addText('ENFERMEDADES', 'labelStyle', 'P_NoSpace');
            $enfermedadesActuales = mb_strtoupper(optional($fichaFisio)->enfermedades_actuales ?? '');
            $tableSalud->addCell(6500, $cellStyleNoBorder)->addText('(' . (mb_strpos($enfermedadesActuales, 'HIPERTENSIÓN ARTERIAL') !== false ? 'X' : ' ') . ') HIPERTENSIÓN ARTERIAL', 'checkboxStyle', 'P_Indent');
            $tableSalud->addRow(400);
            $tableSalud->addCell(3500, $cellStyleNoBorder)->addText('', 'labelStyle', 'P_NoSpace');
            $tableSalud->addCell(6500, $cellStyleNoBorder)->addText('(' . (mb_strpos($enfermedadesActuales, 'DIABETES') !== false ? 'X' : ' ') . ') DIABETES', 'checkboxStyle', 'P_Indent');
            $tableSalud->addRow(400);
            $tableSalud->addCell(3500, $cellStyleNoBorder)->addText('', 'labelStyle', 'P_NoSpace');
            $tableSalud->addCell(6500, $cellStyleNoBorder)->addText('(' . (mb_strpos($enfermedadesActuales, 'ARTROSIS') !== false ? 'X' : ' ') . ') ARTROSIS', 'checkboxStyle', 'P_Indent');
            $tableSalud->addRow(400);
            $tableSalud->addCell(3500, $cellStyleNoBorder)->addText('', 'labelStyle', 'P_NoSpace');
            $tableSalud->addCell(6500, $cellStyleNoBorder)->addText('(' . (mb_strpos($enfermedadesActuales, 'OSTEOPOROSIS') !== false ? 'X' : ' ') . ') OSTEOPOROSIS', 'checkboxStyle', 'P_Indent');
            $tableSalud->addRow(400);
            $tableSalud->addCell(3500, $cellStyleNoBorder)->addText('', 'labelStyle', 'P_NoSpace');
            $tableSalud->addCell(6500, $cellStyleNoBorder)->addText('(' . (mb_strpos($enfermedadesActuales, 'PARKINSON') !== false ? 'X' : ' ') . ') PARKINSON', 'checkboxStyle', 'P_Indent');
            $tableSalud->addRow(400);
            $tableSalud->addCell(3500, $cellStyleNoBorder)->addText('', 'labelStyle', 'P_NoSpace');
            $tableSalud->addCell(6500, $cellStyleNoBorder)->addText('(' . (mb_strpos($enfermedadesActuales, 'NO REFIERE') !== false ? 'X' : ' ') . ') NO REFIERE', 'checkboxStyle', 'P_Indent');
            $tableSalud->addRow(400);
            $tableSalud->addCell(3500, $cellStyleNoBorder)->addText('', 'labelStyle', 'P_NoSpace');
            $tableSalud->addCell(6500, $cellStyleNoBorder)->addText('OTRAS: ' . (empty($enfermedadesActuales) ? 'N/A' : $enfermedadesActuales), 'valueStyle', 'P_Indent');
            $section->addTextBreak(1);

            $tableAlergias = $section->addTable('noBorderTableStyle');
            $tableAlergias->addRow(400);
            $tableAlergias->addCell(3500, $cellStyleNoBorder)->addText('ALERGIAS', 'labelStyle', 'P_NoSpace');
            $alergias = mb_strtoupper(optional($fichaFisio)->alergias ?? '');
            $tableAlergias->addCell(6500, $cellStyleNoBorder)->addText('(' . (mb_strpos($alergias, 'MEDICAMENTOS') !== false ? 'X' : ' ') . ') MEDICAMENTOS: ' . (mb_strpos($alergias, 'MEDICAMENTOS') !== false ? mb_strtoupper($fichaFisio->alergias) : 'N/A'), 'checkboxStyle', 'P_Indent');
            $tableAlergias->addRow(400);
            $tableAlergias->addCell(3500, $cellStyleNoBorder)->addText('', 'labelStyle', 'P_NoSpace');
            $tableAlergias->addCell(6500, $cellStyleNoBorder)->addText('(' . (mb_strpos($alergias, 'ALIMENTOS') !== false ? 'X' : ' ') . ') ALIMENTOS: ' . (mb_strpos($alergias, 'ALIMENTOS') !== false ? mb_strtoupper($fichaFisio->alergias) : 'N/A'), 'checkboxStyle', 'P_Indent');
            $tableAlergias->addRow(400);
            $tableAlergias->addCell(3500, $cellStyleNoBorder)->addText('', 'labelStyle', 'P_NoSpace');
            $tableAlergias->addCell(6500, $cellStyleNoBorder)->addText('(' . (mb_strpos($alergias, 'NO REFIERE') !== false ? 'X' : ' ') . ') NO REFIERE', 'checkboxStyle', 'P_Indent');
            $otherAlergias = '';
            if (!empty($fichaFisio->alergias)) {
                if (mb_strpos($alergias, 'MEDICAMENTOS') === false && mb_strpos($alergias, 'ALIMENTOS') === false && mb_strpos($alergias, 'NO REFIERE') === false) {
                    $otherAlergias = $fichaFisio->alergias;
                }
            }
            $tableAlergias->addRow(400);
            $tableAlergias->addCell(3500, $cellStyleNoBorder)->addText('', 'labelStyle', 'P_NoSpace');
            $tableAlergias->addCell(6500, $cellStyleNoBorder)->addText('OTRAS: ' . (empty($otherAlergias) ? 'N/A' : mb_strtoupper($otherAlergias)), 'valueStyle', 'P_Indent');
            $section->addTextBreak(2);

            $sectionHeaderTable = $section->addTable('sectionHeaderTableStyle');
            $sectionHeaderTable->addRow();
            $sectionHeaderTable->addCell(10000, $sectionHeaderCellStyle)->addText('III. PLAN DE PARTICIPACIÓN INDIVIDUAL O GRUPAL (FISIOTERAPIA)', 'sectionTitleStyle', 'P_Left');
            $section->addTextBreak(1);

            $tablePlan = $section->addTable('noBorderTableStyle');
            $addRow($tablePlan, 'ATENCIÓN FISIOTERAPIA', '');
            $addRow($tablePlan, 'FECHA DE PROGRAMACIÓN', (optional($fichaFisio->fecha_programacion)->format('d/m/Y') ?? 'N/A'));
            $addRow($tablePlan, 'FECHA DE INICIO', (optional($fichaFisio->fecha_inicio)->format('d/m/Y') ?? 'N/A'));
            $addRow($tablePlan, 'FECHA DE FIN', (optional($fichaFisio->fecha_fin)->format('d/m/Y') ?? 'N/A'));
            $addRow($tablePlan, 'NÚMERO DE SESIONES', $fichaFisio->numero_sesiones ?? 'N/A');
            $addRow($tablePlan, 'MOTIVO DE CONSULTA', mb_strtoupper($fichaFisio->motivo_consulta ?? 'N/A'));
            $addRow($tablePlan, 'SOLICITUD ATENCIÓN', mb_strtoupper($fichaFisio->solicitud_atencion ?? 'N/A'));
            $section->addTextBreak(1);

            $tableEquipos = $section->addTable('noBorderTableStyle');
            $tableEquipos->addRow(400);
            $tableEquipos->addCell(3500, $cellStyleNoBorder)->addText('EQUIPOS', 'labelStyle', 'P_NoSpace');
            $equiposFisio = mb_strtoupper(optional($fichaFisio)->equipos ?? '');
            $tableEquipos->addCell(6500, $cellStyleNoBorder)->addText('(' . (mb_strpos($equiposFisio, 'ELECTRO ESTIMULADOR') !== false ? 'X' : ' ') . ') ELECTRO ESTIMULADOR', 'checkboxStyle', 'P_Indent');
            $tableEquipos->addRow(400);
            $tableEquipos->addCell(3500, $cellStyleNoBorder)->addText('', 'labelStyle', 'P_NoSpace');
            $tableEquipos->addCell(6500, $cellStyleNoBorder)->addText('(' . (mb_strpos($equiposFisio, 'ULTRASONIDO') !== false ? 'X' : ' ') . ') ULTRASONIDO', 'checkboxStyle', 'P_Indent');
            $otrosEquipos = [];
            $equiposArray = array_map('trim', explode(',', optional($fichaFisio)->equipos ?? ''));
            foreach ($equiposArray as $equipo) {
                $equipoUpper = mb_strtoupper($equipo);
                if (!empty($equipo) && $equipoUpper !== 'ELECTRO ESTIMULADOR' && $equipoUpper !== 'ULTRASONIDO') {
                    $otrosEquipos[] = $equipo;
                }
            }
            $tableEquipos->addRow(400);
            $tableEquipos->addCell(3500, $cellStyleNoBorder)->addText('', 'labelStyle', 'P_NoSpace');
            $tableEquipos->addCell(6500, $cellStyleNoBorder)->addText('OTROS: ' . (!empty($otrosEquipos) ? implode(', ', $otrosEquipos) : 'N/A'), 'valueStyle', 'P_Indent');
            $section->addTextBreak(3);

            $signatureTable = $section->addTable(['width' => 10000, 'unit' => TblWidth::TWIP]);
            $signatureTable->addRow();
            $signatureTable->addCell(5000, $cellStyleNoBorder)->addText('____________________________', 'normalText', 'P_Center');
            $signatureTable->addCell(5000, $cellStyleNoBorder)->addText('____________________________', 'normalText', 'P_Center');
            $signatureTable->addRow();
            $signatureTable->addCell(5000, $cellStyleNoBorder)->addText('FIRMA FISIOTERAPEUTA', 'signatureStyle', 'P_Center');
            $signatureTable->addCell(5000, $cellStyleNoBorder)->addText('FIRMA ENCARGADO OF. ADULTO MAYOR', 'signatureStyle', 'P_Center');
            $section->addTextBreak(2);

            $section->addText('REGISTRADO POR: ' . mb_strtoupper(optional($usuarioRegistro)->nombres ?? 'N/A') . ' ' . mb_strtoupper(optional($usuarioRegistro)->primer_apellido ?? 'N/A'), 'normalText', 'P_Center');
            $section->addText('FECHA DE REGISTRO: ' . (optional($fichaFisio)->created_at ? Carbon::parse($fichaFisio->created_at)->format('d/m/Y H:i') : 'N/A'), 'normalText', 'P_Center');

            $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
            $nombreAdulto = mb_strtoupper((optional($persona)->nombres ?? '') . '_' . (optional($persona)->primer_apellido ?? ''));
            $fileName = 'FICHA_FISIOTERAPIA_' . $fichaFisio->cod_fisio . '_' . $nombreAdulto . '_' . Carbon::now()->format('Ymd') . '.docx';
            $fileName = str_replace([' ', '/', '\\', ':', '*', '?', '"', '<', '>', '|'], '_', $fileName);
            $fileName = substr($fileName, 0, 200);

            $response = new StreamedResponse(function() use ($objWriter) {
                $objWriter->save('php://output');
            });

            $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');
            $response->headers->set('Content-Disposition', 'attachment;filename="' . $fileName . '"');
            $response->headers->set('Cache-Control', 'max-age=0');

            return $response;

        } catch (\Exception $e) {
            Log::error('Error al generar Word de la Ficha de Fisioterapia individual (cod_fisio: ' . $cod_fisio . '): ' . $e->getMessage(), ['exception' => $e]);
            return back()->with('error', 'Ocurrió un error al generar el Word de la Fisioterapia: ' . $e->getMessage());
        }
    }

    // ###########################################################################################################################
    // REPORTES KINESIOLOGIA
    // --- MÉTODOS PARA KINESIOLOGÍA ---

    /**
     * Muestra el listado de Fichas de Kinesiología registradas (el reporte).
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function indexReporteKine(Request $request)
    {
        // --- INICIO DE LA MEJORA ---
        $search = $request->input('search');
        $mes = $request->input('mes');
        $anio = $request->input('anio');

        try {
            $query = Kinesiologia::with(['adulto.persona', 'usuario.persona']);

            if ($search) {
                $query->whereHas('adulto.persona', function ($qr) use ($search) {
                    $qr->where('nombres', 'like', '%' . $search . '%')
                       ->orWhere('primer_apellido', 'like', '%' . $search . '%')
                       ->orWhere('segundo_apellido', 'like', '%' . $search . '%')
                       ->orWhere('ci', 'like', '%' . $search . '%');
                });
            }

            if ($mes) {
                $query->whereMonth('created_at', $mes);
            }

            if ($anio) {
                $query->whereYear('created_at', $anio);
            }
            
            $years = Kinesiologia::selectRaw('EXTRACT(YEAR FROM created_at) as year')
                ->whereNotNull('created_at')
                ->distinct()
                ->orderBy('year', 'desc')
                ->pluck('year');

            $fichasKinesiologia = $query->orderBy('created_at', 'desc')->paginate(10)->appends($request->query());
            $totalFichasKinesiologia = Kinesiologia::count();

            return view('Medico.indexRepKine', compact(
                'fichasKinesiologia', 
                'totalFichasKinesiologia', 
                'search',
                'years',
                'mes',
                'anio'
            ));

        } catch (\Exception $e) {
            Log::error('Error en ReporteFisioKineController@indexReporteKine: ' . $e->getMessage(), ['exception' => $e]);
            return back()->with('error', 'Ocurrió un error al cargar las fichas de kinesiología.');
        }
        // --- FIN DE LA MEJORA ---
    }

    /**
     * Muestra el formulario para editar una ficha de Kinesiología.
     *
     * @param int $cod_kine
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function editKine($cod_kine)
    {
        try {
            $kinesiologia = Kinesiologia::with('adulto.persona', 'historiaClinica')->findOrFail($cod_kine);
            $adulto = $kinesiologia->adulto;
            $historiaClinica = $kinesiologia->historiaClinica;

            return view('Medico.registrarFichaKine', compact('kinesiologia', 'adulto', 'historiaClinica'));
        } catch (\Exception $e) {
            Log::error('Error en ReporteFisioKineController@editKine: ' . $e->getMessage(), ['cod_kine' => $cod_kine, 'exception' => $e]);
            return back()->with('error', 'No se pudo cargar la ficha de Kinesiología para edición.');
        }
    }

    /**
     * Actualiza una ficha de Kinesiología existente.
     *
     * @param Request $request
     * @param int $cod_kine
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateKine(Request $request, $cod_kine)
    {
        $request->validate([
            'entrenamiento_funcional' => 'boolean',
            'gimnasio_maquina' => 'boolean',
            'aquafit' => 'boolean',
            'hidroterapia' => 'boolean',
            'manana' => 'boolean',
            'tarde' => 'boolean',
        ]);

        DB::beginTransaction();
        try {
            $kinesiologia = Kinesiologia::findOrFail($cod_kine);

            $kinesiologia->update([
                'entrenamiento_funcional' => $request->has('entrenamiento_funcional'),
                'gimnasio_maquina' => $request->has('gimnasio_maquina'),
                'aquafit' => $request->has('aquafit'),
                'hidroterapia' => $request->has('hidroterapia'),
                'manana' => $request->has('manana'),
                'tarde' => $request->has('tarde'),
            ]);

            DB::commit();

            return redirect()->route('responsable.kinesiologia.reportekine.index')->with('success', 'Ficha de Kinesiología actualizada exitosamente.');

        } catch (ValidationException $e) {
            DB::rollBack();
            Log::error('Error de validación al actualizar Ficha de Kinesiología: ', ['errors' => $e->errors()]);
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error inesperado al actualizar Ficha de Kinesiología: ' . $e->getMessage(), ['exception' => $e]);
            return back()->with('error', 'Ocurrió un error al actualizar la Ficha de Kinesiología: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Muestra los detalles de una ficha de Kinesiología.
     *
     * @param int $cod_kine
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function showKine($cod_kine)
    {
        try {
            $kinesiologia = Kinesiologia::with('adulto.persona', 'historiaClinica', 'usuario.persona')->findOrFail($cod_kine);
            return view('Medico.verDetallesKine', compact('kinesiologia'));
        } catch (\Exception $e) {
            Log::error('Error en ReporteFisioKineController@showKine: ' . $e->getMessage(), ['cod_kine' => $cod_kine, 'exception' => $e]);
            return back()->with('error', 'No se pudo cargar los detalles de la ficha de Kinesiología.');
        }
    }

    /**
     * Elimina una ficha de Kinesiología.
     *
     * @param int $cod_kine
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroyKine($cod_kine)
    {
        try {
            $kinesiologia = Kinesiologia::findOrFail($cod_kine);
            $kinesiologia->delete();
            return redirect()->route('responsable.kinesiologia.reportekine.index')->with('success', 'Ficha de Kinesiología eliminada exitosamente.');
        } catch (\Exception $e) {
            Log::error('Error al eliminar Ficha de Kinesiología: ' . $e->getMessage(), ['cod_kine' => $cod_kine, 'exception' => $e]);
            return back()->with('error', 'Error al eliminar la Ficha de Kinesiología: ' . $e->getMessage());
        }
    }

    /**
     * Exporta los registros de Kinesiología a un archivo Excel.
     *
     * @param Request $request La solicitud HTTP que puede contener parámetros de filtro.
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function exportarFichaKineExcel(Request $request)
    {
        try {
            // --- INICIO DE LA MEJORA ---
            $search = $request->input('search');
            $mes = $request->input('mes');
            $anio = $request->input('anio');
            
            $query = Kinesiologia::with('adulto.persona', 'historiaClinica', 'usuario.persona');

            if ($search) {
                $query->whereHas('adulto.persona', function ($qr) use ($search) {
                    $qr->where('nombres', 'like', '%' . $search . '%')
                       ->orWhere('primer_apellido', 'like', '%' . $search . '%')
                       ->orWhere('segundo_apellido', 'like', '%' . $search . '%')
                       ->orWhere('ci', 'like', '%' . $search . '%');
                });
            }
            if ($mes) {
                $query->whereMonth('created_at', $mes);
            }
            if ($anio) {
                $query->whereYear('created_at', $anio);
            }
            // --- FIN DE LA MEJORA ---

            $fichasKinesiologia = $query->orderBy('created_at', 'asc')->get();

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Fichas Kinesiología');

            // --- Encabezado General ---
            $sheet->mergeCells('A1:M1');
            $sheet->setCellValue('A1', 'GOBIERNO AUTÓNOMO MUNICIPAL DE TARIJA');
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
            $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $sheet->mergeCells('A2:M2');
            $sheet->setCellValue('A2', 'OFICINAS DE ASISTENCIA GENERACIONAL Y PERSONAS ADULTAS MAYORES');
            $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(12);
            $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $sheet->getRowDimension(3)->setRowHeight(15);

            Carbon::setLocale('es');
            $currentMonthName = Carbon::now()->locale('es')->monthName;
            $sheet->mergeCells('A4:M4');
            $sheet->setCellValue('A4', 'PLANILLA DE ATENCION EN KINESIOLOGIA CORRESPONDIENTE AL MES: ' . mb_strtoupper($currentMonthName));
            $sheet->getStyle('A4')->getFont()->setBold(true)->setSize(12);
            $sheet->getStyle('A4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $sheet->getRowDimension(5)->setRowHeight(15);

            // --- Cabeceras de la Tabla ---
            $sheet->setCellValue('A6', 'N°');
            $sheet->setCellValue('B6', 'NOMBRE Y APELLIDO');
            $sheet->setCellValue('C6', 'SEXO');
            $sheet->setCellValue('E6', 'SERVICIOS REALIZADO');
            $sheet->setCellValue('I6', 'TURNO');
            $sheet->setCellValue('K6', 'LUGAR DE NACIMIENTO');
            $sheet->setCellValue('L6', 'BARRIO');
            $sheet->setCellValue('M6', 'FIRMA');

            $sheet->setCellValue('C7', 'F');
            $sheet->setCellValue('D7', 'M');
            $sheet->setCellValue('E7', 'ENTRENAMIENTO FUNCIONAL');
            $sheet->setCellValue('F7', 'GIMNASIO MAQUINAS');
            $sheet->setCellValue('G7', 'AQUAFIT');
            $sheet->setCellValue('H7', 'HIDROTERAPIA');
            $sheet->setCellValue('I7', 'MAÑANA');
            $sheet->setCellValue('J7', 'TARDE');

            $sheet->mergeCells('A6:A7');
            $sheet->mergeCells('B6:B7');
            $sheet->mergeCells('C6:D6');
            $sheet->mergeCells('E6:H6');
            $sheet->mergeCells('I6:J6');
            $sheet->mergeCells('K6:K7');
            $sheet->mergeCells('L6:L7');
            $sheet->mergeCells('M6:M7');

            $headerStyle = [
                'font' => ['bold' => true, 'size' => 10],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF000000']]],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFE0E0E0']],
            ];
            $sheet->getStyle('A6:M7')->applyFromArray($headerStyle);
            $sheet->getRowDimension(6)->setRowHeight(30);
            $sheet->getRowDimension(7)->setRowHeight(30);

            $verticalTextColumns = ['E', 'F', 'G', 'H', 'I', 'J'];
            foreach ($verticalTextColumns as $column) {
                $sheet->getStyle($column . '7')->getAlignment()->setTextRotation(90);
            }
            $sheet->getStyle('K6')->getAlignment()->setTextRotation(90);
            $sheet->getStyle('L6')->getAlignment()->setTextRotation(90);
            $sheet->getStyle('M6')->getAlignment()->setTextRotation(90);

            // --- Datos de los Registros ---
            $currentRow = 8;
            foreach ($fichasKinesiologia as $index => $fichaKine) {
                $sheet->setCellValue('A' . $currentRow, $index + 1);
                $sheet->setCellValue('B' . $currentRow, optional($fichaKine->adulto->persona)->nombres . ' ' . optional($fichaKine->adulto->persona)->primer_apellido . ' ' . optional($fichaKine->adulto->persona)->segundo_apellido);
                $sheet->setCellValue('C' . $currentRow, (optional($fichaKine->adulto->persona)->sexo == 'F' ? 'X' : ''));
                $sheet->setCellValue('D' . $currentRow, (optional($fichaKine->adulto->persona)->sexo == 'M' ? 'X' : ''));
                $sheet->setCellValue('E' . $currentRow, ($fichaKine->entrenamiento_funcional ? 'X' : ''));
                $sheet->setCellValue('F' . $currentRow, ($fichaKine->gimnasio_maquina ? 'X' : ''));
                $sheet->setCellValue('G' . $currentRow, ($fichaKine->aquafit ? 'X' : ''));
                $sheet->setCellValue('H' . $currentRow, ($fichaKine->hidroterapia ? 'X' : ''));
                $sheet->setCellValue('I' . $currentRow, ($fichaKine->manana ? 'X' : ''));
                $sheet->setCellValue('J' . $currentRow, ($fichaKine->tarde ? 'X' : ''));
                $sheet->setCellValue('K' . $currentRow, optional($fichaKine->historiaClinica)->lugar_nacimiento_departamento ?? 'N/A');
                $sheet->setCellValue('L' . $currentRow, (optional($fichaKine->adulto->persona)->zona_comunidad ?? 'N/A') . ' / ' . (optional($fichaKine->adulto->persona)->domicilio ?? 'N/A'));
                $sheet->setCellValue('M' . $currentRow, '');

                $currentRow++;
            }

            if (count($fichasKinesiologia) > 0) {
                $dataStyle = [
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF000000']]],
                ];
                $sheet->getStyle('A8:M' . ($currentRow - 1))->applyFromArray($dataStyle);
                $sheet->getStyle('B8:B' . ($currentRow - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            } else {
                $sheet->mergeCells('A8:M8');
                $sheet->setCellValue('A8', 'No se encontraron fichas de kinesiología registradas.');
                $sheet->getStyle('A8')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A8')->getFont()->setItalic(true)->setColor(new Color('FF808080'));
                $currentRow = 9;
            }
            
            $sheet->getColumnDimension('A')->setAutoSize(true);
            $sheet->getColumnDimension('B')->setWidth(35);
            $sheet->getColumnDimension('C')->setWidth(4);
            $sheet->getColumnDimension('D')->setWidth(4);
            $sheet->getColumnDimension('E')->setWidth(4);
            $sheet->getColumnDimension('F')->setWidth(4);
            $sheet->getColumnDimension('G')->setWidth(4);
            $sheet->getColumnDimension('H')->setWidth(4);
            $sheet->getColumnDimension('I')->setWidth(4);
            $sheet->getColumnDimension('J')->setWidth(4);
            $sheet->getColumnDimension('K')->setWidth(15);
            $sheet->getColumnDimension('L')->setWidth(15);
            $sheet->getColumnDimension('M')->setWidth(15);

            // --- Firmas al final ---
            $currentRow += 3;
            $sheet->setCellValue('C' . $currentRow, '____________________________');
            $sheet->getStyle('C' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->mergeCells('C' . ($currentRow + 1) . ':F' . ($currentRow + 1));
            $sheet->setCellValue('C' . ($currentRow + 1), 'FIRMA ADULTO MAYOR');
            $sheet->getStyle('C' . ($currentRow + 1))->applyFromArray(['font' => ['bold' => true, 'size' => 10], 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]]);

            $sheet->setCellValue('G' . $currentRow, '____________________________');
            $sheet->getStyle('G' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->mergeCells('G' . ($currentRow + 1) . ':J' . ($currentRow + 1));
            $sheet->setCellValue('G' . ($currentRow + 1), 'FIRMA KINESIOLOGO/A');
            $sheet->getStyle('G' . ($currentRow + 1))->applyFromArray(['font' => ['bold' => true, 'size' => 10], 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]]);

            $sheet->setCellValue('K' . $currentRow, '____________________________');
            $sheet->getStyle('K' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->mergeCells('K' . ($currentRow + 1) . ':M' . ($currentRow + 1));
            $sheet->setCellValue('K' . ($currentRow + 1), 'FIRMA ENCARGAD@ OF. ADULTO MAYOR');
            $sheet->getStyle('K' . ($currentRow + 1))->applyFromArray(['font' => ['bold' => true, 'size' => 10], 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]]);

            $fileName = 'reporte_kinesiologia_' . Carbon::now()->format('Ymd_His') . '.xlsx';
            $writer = new Xlsx($spreadsheet);

            $response = new StreamedResponse(function() use ($writer) {
                $writer->save('php://output');
            });

            $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            $response->headers->set('Content-Disposition', 'attachment;filename="' . $fileName . '"');
            $response->headers->set('Cache-Control', 'max-age=0');

            return $response;

        } catch (\Exception $e) {
            Log::error('Error al generar Excel del Reporte de Kinesiología: ' . $e->getMessage(), ['exception' => $e]);
            return back()->with('error', 'Error al generar el Excel del Reporte de Kinesiología: ' . $e->getMessage());
        }
    }
}
