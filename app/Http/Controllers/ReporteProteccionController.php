<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AdultoMayor; // Modelo principal para el caso de protección
use App\Models\ActividadLaboral; // Asumiendo que existe este modelo
use App\Models\Encargado; // Asumiendo que existe este modelo
use App\Models\PersonaNatural; // Asumiendo que existe este modelo
use App\Models\PersonaJuridica; // Asumiendo que existe este modelo
use App\Models\Denunciado; // Asumiendo que existe este modelo
use App\Models\GrupoFamiliar; // Asumiendo que existe este modelo
use App\Models\Croquis; // Asumiendo que existe este modelo
use App\Models\Seguimiento; // Asumiendo que existe este modelo
use App\Models\Intervencion; // Asumiendo que existe este modelo (o que los datos están en el mismo AdultoMayor)
use App\Models\AnexoN3; // Asumiendo que existe este modelo
use App\Models\AnexoN5; // Asumiendo que existe este modelo
use App\Models\User; // Para el usuario que registra el seguimiento/anexo

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage; // Para manejar la imagen del croquis
use Carbon\Carbon;

// Para la exportación a Word
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\SimpleType\VerticalJc;
use PhpOffice\PhpWord\SimpleType\TblWidth;
use PhpOffice\PhpWord\SimpleType\Jc; // Para alineación de texto y elementos (START, END, CENTER)
use PhpOffice\PhpWord\Style\Image; // Para posicionamiento de imagen

// Para la generación de PDF con Dompdf
use Barryvdh\DomPDF\Facade\Pdf; // Importa el Facade de DomPDF

class ReporteProteccionController extends Controller
{
    /**
     * Muestra la lista de casos de protección con filtros.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $search = $request->input('search'); // Buscador general (Nro. Caso, Nombres/Apellidos/CI Adulto)
        $nro_caso_filter = $request->input('nro_caso_filter'); // Filtro específico por Nro. Caso
        $denunciado_search = $request->input('denunciado_search'); // NUEVO: Buscador por Nombres/Apellidos Denunciado

        // Cargar las relaciones necesarias, incluyendo la persona del denunciado
        $query = AdultoMayor::with(['persona', 'denunciado.personaNatural']);

        // Aplicar filtro por número de caso (se mantiene la lógica original)
        if (!empty($nro_caso_filter)) {
            $query->where('nro_caso', 'like', '%' . $nro_caso_filter . '%');
        }

        // Aplicar buscador general (Nro. Caso, Nombres, Apellidos, CI del Adulto Mayor)
        if (!empty($search)) {
            $searchTermLower = strtolower($search); // Convertir la búsqueda a minúsculas
            $searchTermsArray = explode(' ', $searchTermLower); // Dividir la cadena en palabras

            $query->where(function ($q) use ($search, $searchTermsArray) {
                // Primero, buscar directamente en nro_caso (se mantiene la lógica original)
                $q->where('nro_caso', 'like', '%' . $search . '%');

                // Luego, buscar cada palabra en los campos de la persona del Adulto Mayor (case-insensitive)
                $q->orWhereHas('persona', function ($qr) use ($searchTermsArray) {
                    $qr->where(function ($subQuery) use ($searchTermsArray) {
                        foreach ($searchTermsArray as $term) {
                            $term = '%' . $term . '%'; // Añadir comodines para coincidencia parcial
                            $subQuery->orWhereRaw('LOWER(nombres) LIKE ?', [$term])
                                     ->orWhereRaw('LOWER(primer_apellido) LIKE ?', [$term])
                                     ->orWhereRaw('LOWER(segundo_apellido) LIKE ?', [$term])
                                     ->orWhereRaw('LOWER(ci) LIKE ?', [$term]); // Incluir CI en la búsqueda case-insensitive
                        }
                    });
                });
            });
        }

        // Aplicar buscador por nombres y apellidos del Denunciado (Ofensor)
        if (!empty($denunciado_search)) {
            $denunciadoSearchTermLower = strtolower($denunciado_search); // Convertir a minúsculas
            $denunciadoSearchTermsArray = explode(' ', $denunciadoSearchTermLower); // Dividir la cadena en palabras

            $query->whereHas('denunciado.personaNatural', function ($qr) use ($denunciadoSearchTermsArray) {
                $qr->where(function ($subQuery) use ($denunciadoSearchTermsArray) {
                    foreach ($denunciadoSearchTermsArray as $term) {
                        $term = '%' . $term . '%'; // Añadir comodines para coincidencia parcial
                        $subQuery->orWhereRaw('LOWER(nombres) LIKE ?', [$term])
                                 ->orWhereRaw('LOWER(primer_apellido) LIKE ?', [$term])
                                 ->orWhereRaw('LOWER(segundo_apellido) LIKE ?', [$term]);
                    }
                });
            });
        }

        // === FILTRO CLAVE: Asegurar que el Adulto Mayor tenga al menos UN módulo de protección activo ===
        $query->where(function ($q) {
            $q->whereHas('actividadLaboral') 
              ->orWhereHas('encargados')     
              ->orWhereHas('denunciado')     
              ->orWhereHas('grupoFamiliar')   
              ->orWhereHas('croquis')        
              ->orWhereHas('seguimientos')    
              ->orWhereHas('anexoN3')        
              ->orWhereHas('anexoN5');       
        });

        // Ordenar los resultados y paginar
        $casos = $query->orderBy('fecha', 'desc')->paginate(10);

        // Retornar la vista con los casos y los valores de los filtros para mantenerlos en la interfaz
        return view('Proteccion.indexRep', compact('casos', 'search', 'nro_caso_filter', 'denunciado_search'));
    }

    /**
     * Muestra el reporte detallado de un caso de protección para impresión.
     *
     * @param int $id_adulto
     * @return \Illuminate->View->View
     */
    public function showReporte($id_adulto)
    {
        $adulto = AdultoMayor::with([
            'persona',
            'actividadLaboral',
            'encargados.personaNatural',
            'encargados.personaJuridica',
            'denunciado.personaNatural',
            'grupoFamiliar',
            'croquis',
            'seguimientos.usuario.persona',
            'seguimientos.intervencion',
            'anexoN3.personaNatural',
            'anexoN5.usuario.persona',
        ])->findOrFail($id_adulto);

        $informante = $adulto->encargados->first();

        return view('Proteccion.verReporte', compact('adulto', 'informante'));
    }
    
    public function exportarFichaProteccionWordIndividual(int $id_adulto)
    {
        try {
            $adulto = AdultoMayor::with([
                'persona', 
                'actividadLaboral',
                'encargados.personaNatural',
                'encargados.personaJuridica',
                'denunciado.personaNatural',
                'grupoFamiliar',
                'croquis',
                'seguimientos.usuario.persona', 
                'seguimientos.intervencion', 
                'anexoN3.personaNatural',
                'anexoN5.usuario.persona' 
            ])->findOrFail($id_adulto);

            $phpWord = new PhpWord();
            $section = $phpWord->addSection([
                'marginLeft' => 1440, 
                'marginRight' => 1440,
                'marginTop' => 1440,
                'marginBottom' => 1440,
            ]);

            // --- Estilos de fuente y párrafo ---
            $phpWord->addFontStyle('headerStyle', ['name' => 'Arial', 'size' => 10, 'bold' => true]);
            $phpWord->addFontStyle('mainTitleStyle', ['name' => 'Arial', 'size' => 14, 'bold' => true, 'underline' => 'single']);
            $phpWord->addFontStyle('sectionTitleStyle', ['name' => 'Arial', 'size' => 11, 'bold' => true, 'underline' => 'single']);
            $phpWord->addFontStyle('subSectionTitleStyle', ['name' => 'Arial', 'size' => 10, 'bold' => true]);
            $phpWord->addFontStyle('labelStyle', ['name' => 'Arial', 'size' => 10, 'bold' => true]);
            $phpWord->addFontStyle('valueStyle', ['name' => 'Arial', 'size' => 10]);
            $phpWord->addFontStyle('checkboxStyle', ['name' => 'Arial', 'size' => 10]);
            $phpWord->addFontStyle('signatureStyle', ['name' => 'Arial', 'size' => 10, 'bold' => true]);

            $phpWord->addParagraphStyle('P_Center', ['align' => Jc::CENTER, 'spaceAfter' => 0, 'spaceBefore' => 0]);
            $phpWord->addParagraphStyle('P_Start', ['align' => Jc::START, 'spaceAfter' => 0, 'spaceBefore' => 0]); 
            $phpWord->addParagraphStyle('P_End', ['align' => Jc::END, 'spaceAfter' => 0, 'spaceBefore' => 0]);     
            $phpWord->addParagraphStyle('P_Indent', ['indentation' => ['left' => 360], 'spaceAfter' => 0, 'spaceBefore' => 0]); 
            $phpWord->addParagraphStyle('P_Textarea', ['spaceAfter' => 180]); 
            $phpWord->addParagraphStyle('P_TableCell', ['spaceAfter' => 0, 'spaceBefore' => 0, 'alignment' => Jc::CENTER]); // Estilo para centrar el texto en las celdas

            // --- Encabezado del Documento (Logo y Títulos de Gobierno) ---
            $header = $section->addHeader();
            $tableHeader = $header->addTable(['width' => 9500, 'unit' => TblWidth::TWIP]); // Usamos TWIP
            $tableHeader->addRow();

            $logoCell = $tableHeader->addCell(1500, ['valign' => VerticalJc::TOP]); // Ancho en TWIP
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
                $logoCell->addText(' [Logo Faltante] ', 'valueStyle', 'P_Start');
            }

            $textCell = $tableHeader->addCell(8000); // Ancho en TWIP (Total 9500)
            $textCell->addText('GOBIERNO AUTONOMO MUNICIPAL DE TARIJA', 'headerStyle', 'P_Center');
            $textCell->addText('OFICINA DEL ADULTO MAYOR', 'headerStyle', 'P_Center');
            $textCell->addTextBreak(1); 

            // --- Título Principal del Caso ---
            $section->addText('FICHA DE CASO DE PROTECCIÓN', 'mainTitleStyle', 'P_Center');
            $section->addTextBreak(1);
            $section->addText('Nro. de Caso: ' . (optional($adulto)->nro_caso ?? 'N/A'), 'labelStyle', 'P_Start');
            $section->addText('Fecha de Registro: ' . (optional($adulto->fecha) ? Carbon::parse($adulto->fecha)->format('d/m/Y') : 'N/A'), 'labelStyle', 'P_Start');
            $section->addTextBreak(1);

            // --- DATOS PERSONALES DEL ADULTO MAYOR (Tomados de la persona asociada al caso) ---
            $section->addText('I. DATOS GENERALES DE LA PERSONA ADULTA MAYOR', 'sectionTitleStyle', 'P_Start');
            $persona = optional($adulto)->persona;
            if ($persona) {
                $section->addText('Primer Apellido: ' . (optional($persona)->primer_apellido ?? 'N/A'), 'valueStyle', 'P_Start');
                $section->addText('Segundo Apellido: ' . (optional($persona)->segundo_apellido ?? ''), 'valueStyle', 'P_Start');
                $section->addText('Nombres: ' . (optional($persona)->nombres ?? 'N/A'), 'valueStyle', 'P_Start');
                $section->addText('Sexo: ' . (optional($persona)->sexo == 'M' ? 'Masculino' : (optional($persona)->sexo == 'F' ? 'Femenino' : 'N/A')), 'valueStyle', 'P_Start');
                $section->addText('Fecha de Nacimiento: ' . (optional($persona)->fecha_nacimiento ? Carbon::parse($persona->fecha_nacimiento)->format('d/m/Y') : 'N/A'), 'valueStyle', 'P_Start');
                $section->addText('CI: ' . (optional($persona)->ci ?? 'N/A'), 'valueStyle', 'P_Start');
                $section->addText('Edad: ' . (optional($persona)->fecha_nacimiento ? Carbon::parse($persona->fecha_nacimiento)->age : 'N/A'), 'valueStyle', 'P_Start');
                $section->addText('Sufre algún tipo de discapacidad? ' . (optional($adulto)->discapacidad ? (optional($adulto)->discapacidad == 'SI' ? 'SI' : 'NO') : 'NO'), 'valueStyle', 'P_Start');
                $section->addText('Estado Civil: ' . (optional($persona)->estado_civil ?? 'N/A'), 'valueStyle', 'P_Start'); 
                $section->addText('Domicilio: ' . (optional($persona)->domicilio ?? 'N/A'), 'valueStyle', 'P_Start');
                $section->addText('Con quien vive: ' . (optional($adulto)->vive_con ?? 'N/A'), 'valueStyle', 'P_Start');
                $section->addText('Teléfono: ' . (optional($persona)->telefono ?? 'N/A'), 'valueStyle', 'P_Start');
                $section->addText('Zona/Comunidad: ' . (optional($persona)->zona_comunidad ?? 'N/A'), 'valueStyle', 'P_Start');
                $section->addText('¿Es migrante? ' . (optional($adulto)->migrante === true ? 'Sí' : (optional($adulto)->migrante === false ? 'No' : 'N/A')), 'valueStyle', 'P_Start');
            } else {
                $section->addText('No se encontraron datos personales para este adulto mayor.', 'valueStyle', 'P_Start');
            }
            $section->addTextBreak(2);

            // --- TAB 1: Actividad Laboral Remunerada --- (Coincide con II. en el DOC)
            $section->addText('II. ACTIVIDAD LABORAL REMUNERADA DE LA PERSONA ADULTA MAYOR', 'sectionTitleStyle', 'P_Start');
            $actividadLaboral = optional($adulto)->actividadLaboral;
            if ($actividadLaboral && ($actividadLaboral->nombre_actividad || $actividadLaboral->direccion_trabajo || $actividadLaboral->horario || $actividadLaboral->horas_x_dia || $actividadLaboral->rem_men_aprox || $actividadLaboral->telefono_laboral)) {
                $section->addText('¿Actividad Laboral Remunerada? SI', 'labelStyle', 'P_Start');
                $section->addText('Actividad Laboral: ' . (optional($actividadLaboral)->nombre_actividad ?? 'N/A'), 'valueStyle', 'P_Start');
                $section->addText('Dirección Habitual del Trabajo: ' . (optional($actividadLaboral)->direccion_trabajo ?? 'N/A'), 'valueStyle', 'P_Start');
                $section->addText('Horario: ' . (optional($actividadLaboral)->horario ?? 'N/A'), 'valueStyle', 'P_Start');
                $section->addText('Horas de Trabajo por Día: ' . (optional($actividadLaboral)->horas_x_dia ?? 'N/A'), 'valueStyle', 'P_Start');
                $section->addText('Remuneración Mensual Aproximada: ' . (optional($actividadLaboral)->rem_men_aprox ?? 'N/A'), 'valueStyle', 'P_Start');
                $section->addText('Teléfono: ' . (optional($actividadLaboral)->telefono_laboral ?? 'N/A'), 'valueStyle', 'P_Start');
            } else {
                $section->addText('¿Actividad Laboral Remunerada? NO', 'labelStyle', 'P_Start');
                $section->addText('No se registró actividad laboral remunerada.', 'valueStyle', 'P_Start');
            }
            $section->addTextBreak(2);

            // --- TAB 2: Datos Del Informante (Encargado) --- (Coincide con III. en el DOC)
            $section->addText('III. DATOS DEL: Informante      Solicitante      Denunciante', 'sectionTitleStyle', 'P_Start'); 
            $encargado = optional($adulto)->encargados; 
            if ($encargado) {
                $section->addText('Anónimo: ' . (optional($encargado)->anonimo ? 'Sí' : 'No'), 'valueStyle', 'P_Start'); 
                $section->addText('Datos de la persona que da parte.', 'valueStyle', 'P_Start'); 

                if (optional($encargado)->tipo_encargado === 'natural') {
                    $section->addText('Persona Natural:', 'subSectionTitleStyle', 'P_Start');
                    $personaNaturalEncargado = optional($encargado)->personaNatural;
                    if ($personaNaturalEncargado) {
                        $section->addText('   Primer Apellido: ' . (optional($personaNaturalEncargado)->primer_apellido ?? 'N/A'), 'valueStyle', 'P_Indent');
                        $section->addText('   Segundo Apellido: ' . (optional($personaNaturalEncargado)->segundo_apellido ?? ''), 'valueStyle', 'P_Indent');
                        $section->addText('   Nombres: ' . (optional($personaNaturalEncargado)->nombres ?? 'N/A'), 'valueStyle', 'P_Indent');
                        $section->addText('   Edad: ' . (optional($personaNaturalEncargado)->edad ?? 'N/A'), 'valueStyle', 'P_Indent');
                        $section->addText('   CI: ' . (optional($personaNaturalEncargado)->ci ?? 'N/A'), 'valueStyle', 'P_Indent');
                        $section->addText('   Teléfono: ' . (optional($personaNaturalEncargado)->telefono ?? 'N/A'), 'valueStyle', 'P_Indent');
                        $section->addText('   Dirección Domicilio (Comunidad): ' . (optional($personaNaturalEncargado)->direccion_domicilio ?? 'N/A'), 'valueStyle', 'P_Indent');
                        $section->addText('   Relación/Parentesco: ' . (optional($personaNaturalEncargado)->relacion_parentesco ?? 'N/A'), 'valueStyle', 'P_Indent');
                        $section->addText('   Dirección de Trabajo: ' . (optional($personaNaturalEncargado)->direccion_de_trabajo ?? 'N/A'), 'valueStyle', 'P_Indent');
                        $section->addText('   Ocupación: ' . (optional($personaNaturalEncargado)->ocupacion ?? 'N/A'), 'valueStyle', 'P_Indent');
                    } else {
                        $section->addText('   No se encontraron datos de Persona Natural para el informante.', 'valueStyle', 'P_Indent');
                    }
                } elseif (optional($encargado)->tipo_encargado === 'juridica') {
                    $section->addText('Persona Jurídica:', 'subSectionTitleStyle', 'P_Start');
                    $personaJuridicaEncargado = optional($encargado)->personaJuridica;
                    if ($personaJuridicaEncargado) {
                        $section->addText('   Nombre de Institución: ' . (optional($personaJuridicaEncargado)->nombre_institucion ?? 'N/A'), 'valueStyle', 'P_Indent');
                        $section->addText('   Dirección: ' . (optional($personaJuridicaEncargado)->direccion ?? 'N/A'), 'valueStyle', 'P_Indent');
                        $section->addText('   Teléfono: ' . (optional($personaJuridicaEncargado)->telefono_juridica ?? 'N/A'), 'valueStyle', 'P_Indent');
                        $section->addText('   Nombre del Funcionario Responsable: ' . (optional($personaJuridicaEncargado)->nombre_funcionario ?? 'N/A'), 'valueStyle', 'P_Indent');
                    } else {
                        $section->addText('   No se encontraron datos de Persona Jurídica para el informante.', 'valueStyle', 'P_Indent');
                    }
                }
            } else {
                $section->addText('No se registró información del informante.', 'valueStyle', 'P_Start');
            }
            $section->addTextBreak(2);

            // --- TAB 3: Datos del Ofensor(a) Denunciado(a) --- (Coincide con III.a y III.b en el DOC)
            $section->addText('a) Datos del Ofensor(a) Denunciado(a)', 'subSectionTitleStyle', 'P_Start');
            $denunciado = optional($adulto)->denunciado;
            if ($denunciado) {
                $personaNaturalDenunciado = optional($denunciado)->personaNatural;
                if ($personaNaturalDenunciado) {
                    $section->addText('Primer Apellido: ' . (optional($personaNaturalDenunciado)->primer_apellido ?? 'N/A'), 'valueStyle', 'P_Start');
                    $section->addText('Segundo Apellido: ' . (optional($personaNaturalDenunciado)->segundo_apellido ?? ''), 'valueStyle', 'P_Start');
                    $section->addText('Nombres: ' . (optional($personaNaturalDenunciado)->nombres ?? 'N/A'), 'valueStyle', 'P_Start');
                    $section->addText('Sexo: ' . (optional($denunciado)->sexo == 'M' ? 'Masculino' : (optional($denunciado)->sexo == 'F' ? 'Femenino' : 'N/A')), 'valueStyle', 'P_Start');
                    $section->addText('Edad: ' . (optional($personaNaturalDenunciado)->edad ?? 'N/A'), 'valueStyle', 'P_Start');
                    $section->addText('CI: ' . (optional($personaNaturalDenunciado)->ci ?? 'N/A'), 'valueStyle', 'P_Start');
                    $section->addText('Teléfono: ' . (optional($personaNaturalDenunciado)->telefono ?? 'N/A'), 'valueStyle', 'P_Start');
                    $section->addText('Dirección Domicilio (Comunidad): ' . (optional($personaNaturalDenunciado)->direccion_domicilio ?? 'N/A'), 'valueStyle', 'P_Start');
                    $section->addText('Relación/Parentesco: ' . (optional($personaNaturalDenunciado)->relacion_parentesco ?? 'N/A'), 'valueStyle', 'P_Start');
                    $section->addText('Dirección de Trabajo: ' . (optional($personaNaturalDenunciado)->direccion_de_trabajo ?? 'N/A'), 'valueStyle', 'P_Start');
                    $section->addText('Ocupación: ' . (optional($personaNaturalDenunciado)->ocupacion ?? 'N/A'), 'valueStyle', 'P_Start');
                } else {
                    $section->addText('No se encontraron datos de Persona Natural para el denunciado.', 'valueStyle', 'P_Start');
                }
                $section->addTextBreak(1);
                $section->addText('b) Descripción de los Hechos:', 'subSectionTitleStyle', 'P_Start');
                $section->addText((optional($denunciado)->descripcion_hechos ?? 'N/A'), 'valueStyle', 'P_Textarea');
            } else {
                $section->addText('No se registró información del ofensor(a) denunciado(a).', 'valueStyle', 'P_Start');
            }
            $section->addTextBreak(2);

            // --- TAB 4: Grupo Familiar De La Persona Adulta Mayor --- (Coincide con III.c en el DOC)
            $section->addText('c) Grupo Familiar de la Persona Adulta Mayor:', 'subSectionTitleStyle', 'P_Start');
            $grupoFamiliar = optional($adulto)->grupoFamiliar;
            if ($grupoFamiliar && $grupoFamiliar->count() > 0) {
                // Crear tabla para el grupo familiar
                // Ancho total de la tabla (aproximadamente 9500 TWIP para 100% en A4 con 1 pulgada de margen)
                $tableFamiliares = $section->addTable([
                    'width' => 9500, 
                    'unit' => TblWidth::TWIP, 
                    'borderSize' => 6, 
                    'borderColor' => '000000',
                    'cellMargin' => 80
                ]);
                $tableFamiliares->addRow();
                // Calcular anchos en TWIP (porcentaje * 9500 / 100)
                $tableFamiliares->addCell(475, ['bgColor' => 'DDDDDD'])->addText('N°', 'labelStyle', 'P_TableCell'); // 5% -> 475 TWIP
                $tableFamiliares->addCell(1235, ['bgColor' => 'DDDDDD'])->addText('Apellido Paterno', 'labelStyle', 'P_TableCell'); // 13% -> 1235 TWIP
                $tableFamiliares->addCell(1235, ['bgColor' => 'DDDDDD'])->addText('Apellido Materno', 'labelStyle', 'P_TableCell'); // 13% -> 1235 TWIP
                $tableFamiliares->addCell(1330, ['bgColor' => 'DDDDDD'])->addText('Nombres', 'labelStyle', 'P_TableCell'); // 14% -> 1330 TWIP
                $tableFamiliares->addCell(855, ['bgColor' => 'DDDDDD'])->addText('Parentesco', 'labelStyle', 'P_TableCell'); // 9% -> 855 TWIP
                $tableFamiliares->addCell(475, ['bgColor' => 'DDDDDD'])->addText('Edad', 'labelStyle', 'P_TableCell'); // 5% -> 475 TWIP
                $tableFamiliares->addCell(950, ['bgColor' => 'DDDDDD'])->addText('Ocupación', 'labelStyle', 'P_TableCell'); // 10% -> 950 TWIP
                $tableFamiliares->addCell(1520, ['bgColor' => 'DDDDDD'])->addText('Dirección', 'labelStyle', 'P_TableCell'); // 16% -> 1520 TWIP
                $tableFamiliares->addCell(1425, ['bgColor' => 'DDDDDD'])->addText('Teléfono', 'labelStyle', 'P_TableCell'); // 15% -> 1425 TWIP

                foreach ($grupoFamiliar as $index => $familiar) {
                    $tableFamiliares->addRow();
                    $tableFamiliares->addCell(475)->addText($index + 1, 'valueStyle', 'P_TableCell');
                    $tableFamiliares->addCell(1235)->addText(optional($familiar)->apellido_paterno ?? 'N/A', 'valueStyle');
                    $tableFamiliares->addCell(1235)->addText(optional($familiar)->apellido_materno ?? 'N/A', 'valueStyle');
                    $tableFamiliares->addCell(1330)->addText(optional($familiar)->nombres ?? 'N/A', 'valueStyle');
                    $tableFamiliares->addCell(855)->addText(optional($familiar)->parentesco ?? 'N/A', 'valueStyle');
                    $tableFamiliares->addCell(475)->addText(optional($familiar)->edad ?? 'N/A', 'valueStyle', 'P_TableCell');
                    $tableFamiliares->addCell(950)->addText(optional($familiar)->ocupacion ?? 'N/A', 'valueStyle');
                    $tableFamiliares->addCell(1520)->addText(optional($familiar)->direccion ?? 'N/A', 'valueStyle');
                    $tableFamiliares->addCell(1425)->addText(optional($familiar)->telefono ?? 'N/A', 'valueStyle');
                }
            } else {
                $section->addText('No se registró información del grupo familiar.', 'valueStyle', 'P_Start');
            }
            $section->addTextBreak(2);

            // --- TAB 5: Croquis Del Domicilio O Lugar De Referencia --- (Coincide con IV. en el DOC)
            $section->addText('IV. CROQUIS DEL DOMICILIO O LUGAR DE REFERENCIA DEL ADULTO MAYOR:', 'sectionTitleStyle', 'P_Start');
            $croquis = optional($adulto)->croquis;
            if ($croquis) {
                $section->addText('Nombre y Apellidos del Denunciante: ' . trim((optional($croquis)->nombre_denunciante ?? '') . ' ' . (optional($croquis)->apellidos_denunciante ?? '')), 'valueStyle', 'P_Start');
                $section->addText('C.I: ' . (optional($croquis)->ci_denunciante ?? 'N/A'), 'valueStyle', 'P_Start');

                // Aseguramos que la ruta_imagen incluya el subdirectorio si no lo tiene ya
                $imageFilename = optional($croquis)->ruta_imagen;
                $fullImagePathInStorage = '';

                if ($imageFilename) {
                    if (!str_starts_with($imageFilename, 'croquis_images/')) {
                        $fullImagePathInStorage = 'croquis_images/' . $imageFilename;
                    } else {
                        $fullImagePathInStorage = $imageFilename;
                    }
                }
                
                Log::info('Ruta de imagen de croquis a buscar en Storage: ' . $fullImagePathInStorage);

                if ($fullImagePathInStorage && Storage::disk('public')->exists($fullImagePathInStorage)) { 
                    $imagePath = Storage::disk('public')->path($fullImagePathInStorage);
                    Log::info('Ruta física del croquis encontrada: ' . $imagePath);
                    try {
                        $section->addTextBreak(1);
                        $section->addImage(
                            $imagePath,
                            [
                                'width'            => 400, // Ajusta el ancho de la imagen
                                'height'           => 200, // Ajusta el alto para mantener proporción
                                'alignment'        => Jc::CENTER,
                                'wrappingStyle'    => 'square',
                            ]
                        );
                        $section->addText(' [Imagen del Croquis] ', 'valueStyle', 'P_Center');
                    } catch (\Exception $e) {
                        Log::error('Error al insertar imagen del croquis (ruta: ' . $imagePath . '): ' . $e->getMessage());
                        $section->addText(' [Error al cargar imagen del croquis] ', 'valueStyle', 'P_Center');
                    }
                } else {
                    $section->addText('No se adjuntó imagen de croquis o la imagen no fue encontrada.', 'valueStyle', 'P_Center');
                    Log::warning('Imagen de croquis no encontrada para adulto ID: ' . $id_adulto . ' en ruta de Storage: ' . ($fullImagePathInStorage ?? 'No hay ruta definida'));
                }
            } else {
                $section->addText('No se registró información del croquis.', 'valueStyle', 'P_Start');
            }
            $section->addTextBreak(2);
            // --- Firmas --- (Coincide con Firmas del Documento)
            $tableFirmas = $section->addTable([
                'borderColor' => 'FFFFFF',
                'borderSize' => 0,
                'cellMargin' => 0,
                'alignment' => Jc::CENTER,
                'width' => 9500, // Ancho total en TWIP
                'unit' => TblWidth::TWIP,
            ]);

            $tableFirmas->addRow();
            $tableFirmas->addCell(4750)->addText('__________________________________', 'valueStyle', 'P_Center'); // 50%
            $tableFirmas->addRow();
            $tableFirmas->addCell(4750)->addText('FIRMA DEL ADULTO MAYOR', 'signatureStyle', 'P_Center');
            $tableFirmas->addRow();

            // --- TAB 6: Seguimiento del Caso --- (Coincide con V. en el DOC)
            $section->addText('V. SEGUIMIENTO DEL CASO:', 'sectionTitleStyle', 'P_Start');
            $seguimientos = optional($adulto)->seguimientos;
            if ($seguimientos && $seguimientos->count() > 0) {
                $tableSeguimientos = $section->addTable([
                    'width' => 9500, // Usamos TWIP
                    'unit' => TblWidth::TWIP, 
                    'borderSize' => 6, 
                    'borderColor' => '000000',
                    'cellMargin' => 80
                ]);
                $tableSeguimientos->addRow();
                // Calcular anchos en TWIP
                $tableSeguimientos->addCell(475, ['bgColor' => 'DDDDDD'])->addText('N°', 'labelStyle', 'P_TableCell'); // 5% -> 475 TWIP
                $tableSeguimientos->addCell(950, ['bgColor' => 'DDDDDD'])->addText('Fecha', 'labelStyle', 'P_TableCell'); // 10% -> 950 TWIP
                $tableSeguimientos->addCell(2660, ['bgColor' => 'DDDDDD'])->addText('Acción Realizada', 'labelStyle', 'P_TableCell'); // 28% -> 2660 TWIP
                $tableSeguimientos->addCell(2660, ['bgColor' => 'DDDDDD'])->addText('Resultado Obtenido', 'labelStyle', 'P_TableCell'); // 28% -> 2660 TWIP
                $tableSeguimientos->addCell(2755, ['bgColor' => 'DDDDDD'])->addText('Nombre del/la Funcionario(a) que Realizo la Acción', 'labelStyle', 'P_TableCell'); // 29% -> 2755 TWIP

                foreach ($seguimientos as $index => $seguimiento) {
                    $tableSeguimientos->addRow();
                    $tableSeguimientos->addCell(475)->addText(optional($seguimiento)->nro ?? 'N/A', 'valueStyle', 'P_TableCell');
                    $tableSeguimientos->addCell(950)->addText(optional($seguimiento)->fecha ? Carbon::parse($seguimiento->fecha)->format('d/m/Y') : 'N/A', 'valueStyle');
                    $tableSeguimientos->addCell(2660)->addText(optional($seguimiento)->accion_realizada ?? 'N/A', 'valueStyle');
                    $tableSeguimientos->addCell(2660)->addText(optional($seguimiento)->resultado_obtenido ?? 'N/A', 'valueStyle');
                    $funcionarioSeguimiento = optional(optional($seguimiento)->usuario)->persona;
                    $tableSeguimientos->addCell(2755)->addText(trim((optional($funcionarioSeguimiento)->nombres ?? '') . ' ' . (optional($funcionarioSeguimiento)->primer_apellido ?? '') . ' ' . (optional($funcionarioSeguimiento)->segundo_apellido ?? '')), 'valueStyle');
                }
            } else {
                $section->addText('No se registraron seguimientos para este caso.', 'valueStyle', 'P_Start');
            }
            $section->addTextBreak(2);

            // --- TAB 7: Intervención --- (Coincide con VI. en el DOC)
            $section->addText('VI. INTERVENCIÓN DE LA INSTITUCIÓN.', 'sectionTitleStyle', 'P_Start');
            $latestSeguimiento = $adulto->seguimientos->sortByDesc('fecha')->first();
            $intervencion = optional($latestSeguimiento)->intervencion;
            
            if ($intervencion) {
                $section->addText('Resuelto: ¿Cómo?: ' . (optional($intervencion)->resuelto_descripcion ?? 'N/A'), 'valueStyle', 'P_Textarea');
                $section->addText('No Resultado: ¿Por qué?: ' . (optional($intervencion)->no_resultado ?? 'N/A'), 'valueStyle', 'P_Start');
                $section->addText('Derivado a otra institución: ¿Por qué?: ' . (optional($intervencion)->derivacion_institucion ?? 'N/A'), 'valueStyle', 'P_Start');
                $section->addTextBreak(1);
                
                $section->addText('Derivaciones y Resultados:', 'labelStyle', 'P_Start'); 
                $section->addText('   Derivado y en seguimiento legal: ' . (optional($intervencion)->der_seguimiento_legal ?? 'N/A'), 'valueStyle', 'P_Indent');
                $section->addText('   Derivado y en seguimiento psicológico: ' . (optional($intervencion)->der_seguimiento_psi ?? 'N/A'), 'valueStyle', 'P_Indent');
                $section->addText('   Derivado y resuelto en otra institución: ' . (optional($intervencion)->der_resuelto_externo ?? 'N/A'), 'valueStyle', 'P_Indent');
                $section->addText('   Derivado a otra institución y no resuelto: ' . (optional($intervencion)->der_noresuelto_externo ?? 'N/A'), 'valueStyle', 'P_Indent');
                $section->addText('Abandonado por la Victima - ¿Qué paso?: ' . (optional($intervencion)->abandono_victima ?? 'N/A'), 'valueStyle', 'P_Textarea');
                $section->addText('Resuelto mediante conciliación según Justicia Indígena Originaria: ' . (optional($intervencion)->resuelto_conciliacion_jio ?? 'N/A'), 'valueStyle', 'P_Start');
                
                $section->addText('Fecha: ' . (optional($intervencion)->fecha_intervencion ? Carbon::parse($intervencion->fecha_intervencion)->format('d/m/Y') : 'N/A'), 'valueStyle', 'P_Start');
            } else {
                $section->addText('No se registró información de intervención.', 'valueStyle', 'P_Start');
            }
            $section->addTextBreak(2);

            // --- TAB 8: Anexo Al Numeral III --- (Coincide con ANEXO AL NUMERAL III. en el DOC)
            $section->addText('ANEXO AL NUMERAL III.', 'sectionTitleStyle', 'P_Start');
            $anexosN3 = optional($adulto)->anexoN3;
            if ($anexosN3 && $anexosN3->count() > 0) {
                foreach ($anexosN3 as $index => $anexo3) {
                    $personaNaturalAnexo3 = optional($anexo3)->personaNatural; 
                    if ($personaNaturalAnexo3) {
                        $section->addText('Registro N° ' . ($index + 1) . ':', 'subSectionTitleStyle', 'P_Start');
                        $section->addText('Primer Apellido: ' . (optional($personaNaturalAnexo3)->primer_apellido ?? 'N/A'), 'valueStyle', 'P_Indent');
                        $section->addText('Segundo Apellido: ' . (optional($personaNaturalAnexo3)->segundo_apellido ?? ''), 'valueStyle', 'P_Indent');
                        $section->addText('Nombres: ' . (optional($personaNaturalAnexo3)->nombres ?? 'N/A'), 'valueStyle', 'P_Indent');
                        // $section->addText('Sexo: ' . (optional($personaNaturalAnexo3)->sexo == 'M' ? 'M' : (optional($personaNaturalAnexo3)->sexo == 'F' ? 'F' : 'N/A')), 'valueStyle', 'P_Indent');
                        $section->addText('Edad: ' . (optional($personaNaturalAnexo3)->edad ?? 'N/A'), 'valueStyle', 'P_Indent');
                        $section->addText('CI: ' . (optional($personaNaturalAnexo3)->ci ?? 'N/A'), 'valueStyle', 'P_Indent');
                        $section->addText('Teléfono: ' . (optional($personaNaturalAnexo3)->telefono ?? 'N/A'), 'valueStyle', 'P_Indent');
                        $section->addText('Dirección Domicilio (Comunidad): ' . (optional($personaNaturalAnexo3)->direccion_domicilio ?? 'N/A'), 'valueStyle', 'P_Indent');
                        $section->addText('Relación/Parentesco: ' . (optional($personaNaturalAnexo3)->relacion_parentesco ?? 'N/A'), 'valueStyle', 'P_Indent');
                        $section->addText('Dirección de Trabajo: ' . (optional($personaNaturalAnexo3)->direccion_de_trabajo ?? 'N/A'), 'valueStyle', 'P_Indent');
                        $section->addText('Ocupación: ' . (optional($personaNaturalAnexo3)->ocupacion ?? 'N/A'), 'valueStyle', 'P_Indent');
                        $section->addTextBreak(1);
                    }
                }
            } else {
                $section->addText('No se registraron anexos al numeral III.', 'valueStyle', 'P_Start');
            }
            $section->addTextBreak(2);

            // --- TAB 9: Anexo Al Numeral V --- (Coincide con ANEXO AL NUMERAL V. en el DOC)
            $section->addText('ANEXO AL NUMERAL V.', 'sectionTitleStyle', 'P_Start');
            $anexosN5 = optional($adulto)->anexoN5;
            if ($anexosN5 && $anexosN5->count() > 0) {
                 // Crear tabla para Anexo V
                $tableAnexo5 = $section->addTable([
                    'width' => 9500, // Usamos TWIP
                    'unit' => TblWidth::TWIP, 
                    'borderSize' => 6, 
                    'borderColor' => '000000',
                    'cellMargin' => 80
                ]);
                $tableAnexo5->addRow();
                // Calcular anchos en TWIP
                $tableAnexo5->addCell(475, ['bgColor' => 'DDDDDD'])->addText('N°', 'labelStyle', 'P_TableCell'); // 5% -> 475
                $tableAnexo5->addCell(950, ['bgColor' => 'DDDDDD'])->addText('Fecha', 'labelStyle', 'P_TableCell'); // 10% -> 950
                $tableAnexo5->addCell(2660, ['bgColor' => 'DDDDDD'])->addText('Acción Realizada', 'labelStyle', 'P_TableCell'); // 28% -> 2660
                $tableAnexo5->addCell(2660, ['bgColor' => 'DDDDDD'])->addText('Resultado Obtenido', 'labelStyle', 'P_TableCell'); // 28% -> 2660
                $tableAnexo5->addCell(2755, ['bgColor' => 'DDDDDD'])->addText('Nombre del/la Funcionario(a) que Realizo la Acción', 'labelStyle', 'P_TableCell'); // 29% -> 2755

                foreach ($anexosN5 as $index => $anexo5) {
                    $tableAnexo5->addRow();
                    $tableAnexo5->addCell(475)->addText(optional($anexo5)->numero ?? 'N/A', 'valueStyle', 'P_TableCell');
                    $tableAnexo5->addCell(950)->addText(optional($anexo5)->fecha ? Carbon::parse($anexo5->fecha)->format('d/m/Y') : 'N/A', 'valueStyle');
                    $tableAnexo5->addCell(2660)->addText(optional($anexo5)->accion_realizada ?? 'N/A', 'valueStyle');
                    $tableAnexo5->addCell(2660)->addText(optional($anexo5)->resultado_obtenido ?? 'N/A', 'valueStyle');
                    $funcionarioAnexo5 = optional(optional($anexo5)->usuario)->persona;
                    $tableAnexo5->addCell(2755)->addText(trim((optional($funcionarioAnexo5)->nombres ?? '') . ' ' . (optional($funcionarioAnexo5)->primer_apellido ?? '') . ' ' . (optional($funcionarioAnexo5)->segundo_apellido ?? '')), 'valueStyle');
                }
            } else {
                $section->addText('No se registraron anexos al numeral V.', 'valueStyle', 'P_Start');
            }
            $section->addTextBreak(3); 

            // --- Firmas --- (Coincide con Firmas del Documento)
            $tableFirmas = $section->addTable([
                'borderColor' => 'FFFFFF',
                'borderSize' => 0,
                'cellMargin' => 0,
                'alignment' => Jc::CENTER,
                'width' => 9500, // Ancho total en TWIP
                'unit' => TblWidth::TWIP,
            ]);

            $tableFirmas->addRow();
            $tableFirmas->addCell(4750)->addText('__________________________________', 'valueStyle', 'P_Center'); // 50%
            $tableFirmas->addCell(4750)->addText('__________________________________', 'valueStyle', 'P_Center'); // 50%
            $tableFirmas->addRow();
            $tableFirmas->addCell(4750)->addText('FIRMA DEL PROFESIONAL', 'signatureStyle', 'P_Center');
            $tableFirmas->addCell(4750)->addText('FIRMA DEL USUARIO(A)', 'signatureStyle', 'P_Center');

            // Preparar el archivo para la descarga
            $objWriter = IOFactory::createWriter($phpWord, 'Word2007'); 

            $nombreAdulto = (optional($persona)->nombres ?? '') . '_' . (optional($persona)->primer_apellido ?? '');
            $fileName = 'ficha_proteccion_' . (optional($adulto)->nro_caso ?? $adulto->id_adulto) . '_' . $nombreAdulto . '_' . Carbon::now()->format('Ymd') . '.docx';
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
            Log::error('Error al generar Word de la Ficha de Protección individual (id_adulto: ' . $id_adulto . '): ' . $e->getMessage(), ['exception' => $e]);
            return back()->with('error', 'Ocurrió un error al generar el Word de la Ficha de Protección: ' . $e->getMessage());
        }
    }
    public function exportarFichaProteccionPdfIndividual(int $id_adulto)
    {
        try {
            // Cargar AdultoMayor con todas las relaciones necesarias, incluyendo seguimientos y sus intervenciones
            $adulto = AdultoMayor::with([
                'persona', 
                'actividadLaboral',
                'encargados.personaNatural',
                'encargados.personaJuridica',
                'denunciado.personaNatural',
                'grupoFamiliar',
                'croquis',
                'seguimientos.usuario.persona', 
                'seguimientos.intervencion', // <--- Carga la intervención a través del seguimiento
                'anexoN3.personaNatural',
                'anexoN5.usuario.persona',
            ])->findOrFail($id_adulto);

            // Accedemos a encargados directamente, ya que es hasOne y devuelve un objeto o null
            $informante = $adulto->encargados; 
            // Acceder a la relación denuncianteCroquis directamente, ya que es hasOne
            $denuncianteCroquis = $adulto->denuncianteCroquis; 

            // Lógica para obtener la Intervención del último seguimiento que tenga una.
            $intervencion = null;
            // Ordena los seguimientos por fecha de creación descendente y busca la primera intervención
            foreach ($adulto->seguimientos->sortByDesc('created_at') as $seg) {
                if ($seg->intervencion) {
                    $intervencion = $seg->intervencion;
                    break; // Una vez encontrada, salimos
                }
            }

            $data = [
                'adulto' => $adulto,
                'informante' => $informante,
                'denuncianteCroquis' => $denuncianteCroquis,
                'intervencion' => $intervencion, // Pasamos la intervención encontrada a la vista
            ];

            // Cargar la vista Blade y generar el PDF
            $pdf = Pdf::loadView('Proteccion.pdf_ficha_proteccion', $data);

            // Configurar el nombre del archivo
            $nombreAdulto = mb_strtoupper(trim(
                (optional($adulto->persona)->nombres ?? '') . '_' . 
                (optional($adulto->persona)->primer_apellido ?? '')
            ));
            $fileName = 'FICHA_PROTECCION_' . mb_strtoupper(optional($adulto)->nro_caso ?? 'N_A') . '_' . $nombreAdulto . '_' . Carbon::now()->format('Ymd_His') . '.pdf';
            $fileName = str_replace([' ', '/', '\\', ':', '*', '?', '"', '<', '>', '|'], '_', $fileName);
            $fileName = substr($fileName, 0, 200); 

            // Retornar el PDF para descarga
            return $pdf->download($fileName);

        } catch (\Exception $e) {
            Log::error('Error al generar PDF de la Ficha de Protección individual (id_adulto: ' . $id_adulto . '): ' . $e->getMessage(), ['exception' => $e]);
            return back()->with('error', 'Ocurrió un error al generar el PDF de la Ficha de Protección: ' . $e->getMessage());
        }
    }
}
