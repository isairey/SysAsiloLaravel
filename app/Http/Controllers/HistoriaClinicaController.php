<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AdultoMayor;
use App\Models\HistoriaClinica;
use App\Models\ExamenComplementario;
use App\Models\MedicamentoRecetado;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class HistoriaClinicaController extends Controller
{
    public function index(Request $request)
    {
        $adultos = AdultoMayor::with('persona', 'latestHistoriaClinica')->paginate(10);
        return view('Medico.indexHC', compact('adultos'));
    }

    public function register($id_adulto, $activeTab = 'historia')
    {
        $adulto = AdultoMayor::with('persona')->findOrFail($id_adulto);
        $modoEdicion = false;
        $historiaClinica = null;
        $examenesComplementarios = collect(); 
        $medicamentosRecetados = collect(); 

        $activeTab = 'historia';
        session(['active_tab' => $activeTab]);

        return view('Medico.registrarHistoriaClinica', compact('adulto', 'modoEdicion', 'historiaClinica', 'examenesComplementarios', 'medicamentosRecetados', 'activeTab'));
    }

    public function storeHistoriaClinica(Request $request, $id_adulto)
    {
        $request->validate([
            'municipio_nombre' => 'nullable|string|max:255',
            'establecimiento' => 'nullable|string|max:255',
            'antecedentes_personales' => 'nullable|string',
            'antecedentes_familiares' => 'nullable|string',
            'estado_actual' => 'nullable|string',
            'tipo_consulta' => 'string|in:N,R',
            'ocupacion' => 'nullable|string',
            'grado_instruccion' => 'nullable|string|max:255',
            'lugar_nacimiento_provincia' => 'nullable|string|max:255',
            'lugar_nacimiento_departamento' => 'nullable|string|max:255',
            'domicilio_actual' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            $historiaClinica = HistoriaClinica::create([
                'municipio_nombre' => $request->municipio_nombre,
                'establecimiento' => $request->establecimiento,
                'antecedentes_personales' => $request->antecedentes_personales,
                'antecedentes_familiares' => $request->antecedentes_familiares,
                'estado_actual' => $request->estado_actual,
                'tipo_consulta' => $request->tipo_consulta,
                'ocupacion' => $request->ocupacion,
                'grado_instruccion' => $request->grado_instruccion,
                'lugar_nacimiento_provincia' => $request->lugar_nacimiento_provincia,
                'lugar_nacimiento_departamento' => $request->lugar_nacimiento_departamento,
                'domicilio_actual' => $request->domicilio_actual,
                'id_usuario' => Auth::id(),
                'id_adulto' => $id_adulto,
            ]);

            DB::commit();

            return redirect()->route('responsable.enfermeria.medico.historia_clinica.edit', [
                'id_historia' => $historiaClinica->id_historia,
                'active_tab' => 'examenes'
            ])->with('success', 'Datos de Historia Clínica guardados. Continúa con los Exámenes Complementarios.');

        } catch (ValidationException $e) {
            DB::rollBack();
            Log::error('Error de validación al guardar Historia Clínica: ', ['errors' => $e->errors()]);
            return back()->withErrors($e->errors())->withInput()->with('active_tab', 'historia');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error inesperado al guardar Historia Clínica: ' . $e->getMessage());
            return back()->withErrors(['general_error' => 'Ocurrió un error al guardar la Historia Clínica: ' . $e->getMessage()])
                         ->withInput()->with('active_tab', 'historia');
        }
    }

    public function updateHistoriaClinica(Request $request, $id_historia)
    {
        $request->validate([
            'municipio_nombre' => 'nullable|string|max:255',
            'establecimiento' => 'nullable|string|max:255',
            'antecedentes_personales' => 'nullable|string',
            'antecedentes_familiares' => 'nullable|string',
            'estado_actual' => 'nullable|string',
            'tipo_consulta' => 'string|in:N,R',
            'ocupacion' => 'nullable|string',
            'grado_instruccion' => 'nullable|string|max:255',
            'lugar_nacimiento_provincia' => 'nullable|string|max:255',
            'lugar_nacimiento_departamento' => 'nullable|string|max:255',
            'domicilio_actual' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            $historiaClinica = HistoriaClinica::findOrFail($id_historia);
            $historiaClinica->update([
                'municipio_nombre' => $request->municipio_nombre,
                'establecimiento' => $request->establecimiento,
                'antecedentes_personales' => $request->antecedentes_personales,
                'antecedentes_familiares' => $request->antecedentes_familiares,
                'estado_actual' => $request->estado_actual,
                'tipo_consulta' => $request->tipo_consulta,
                'ocupacion' => $request->ocupacion,
                'grado_instruccion' => $request->grado_instruccion,
                'lugar_nacimiento_provincia' => $request->lugar_nacimiento_provincia,
                'lugar_nacimiento_departamento' => $request->lugar_nacimiento_departamento,
                'domicilio_actual' => $request->domicilio_actual,
            ]);

            DB::commit();

            return redirect()->route('responsable.enfermeria.medico.historia_clinica.edit', [
                'id_historia' => $historiaClinica->id_historia,
                'active_tab' => 'examenes'
            ])->with('success', 'Historia Clínica actualizada. Continúa con los Exámenes Complementarios.');

        } catch (ValidationException $e) {
            DB::rollBack();
            Log::error('Error de validación al actualizar Historia Clínica: ', ['errors' => $e->errors()]);
            return back()->withErrors($e->errors())->withInput()->with('active_tab', 'historia');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error inesperado al actualizar Historia Clínica: ' . $e->getMessage());
            return back()->withErrors(['general_error' => 'Ocurrió un error al actualizar la Historia Clínica: ' . $e->getMessage()])
                         ->withInput()->with('active_tab', 'historia');
        }
    }


    public function storeExamenesComplementarios(Request $request, $id_historia)
    {
        $request->validate([
            'presion_arterial' => 'nullable|string|max:255',
            'temperatura' => 'nullable|string|max:255',
            'peso_corporal' => 'nullable|string|max:255',
            'resultado_prueba' => 'nullable|string|max:255',
            'diagnostico' => 'nullable|string|max:255',
            'medicamentos.*.id_medicamento_recetado' => 'nullable|integer', // 'exists' validation can be problematic for 'null' IDs
            'medicamentos.*.nombre_medicamento' => 'nullable|string|max:255',
            'medicamentos.*.cantidad_recetada' => 'nullable|integer|min:0',
            'medicamentos.*.cantidad_dispensada' => 'nullable|integer|min:0',
            'medicamentos.*.valor_unitario' => 'nullable|numeric|min:0',
            'medicamentos.*.total' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $historiaClinica = HistoriaClinica::findOrFail((int)$id_historia);
            $id_adulto = $historiaClinica->id_adulto;

            Log::debug('Incoming request data for ExamenesComplementarios:', $request->all());
            Log::debug('Processing for id_historia: ' . $historiaClinica->id_historia);

            // 1. Manejar el registro de datos generales del examen (presión, temperatura, etc.)
            $generalExamenData = [
                'presion_arterial' => $request->presion_arterial,
                'temperatura' => $request->temperatura,
                'peso_corporal' => $request->peso_corporal,
                'resultado_prueba' => $request->resultado_prueba,
                'diagnostico' => $request->diagnostico,
                'id_historia' => $historiaClinica->id_historia,
                'id_usuario' => Auth::id(),
                'id_adulto' => $id_adulto,
            ];

            $generalExamenRecord = ExamenComplementario::updateOrCreate(
                ['id_historia' => $historiaClinica->id_historia],
                $generalExamenData
            );
            Log::debug("General examen record processed. ID: {$generalExamenRecord->id_examen}. Action: " . ($generalExamenRecord->wasRecentlyCreated ? 'Created' : 'Updated'));


            // 2. Procesar los registros de medicamentos enviados desde el formulario (tabla medicamentos_recetados)
            $submittedMedicamentoIds = [];

            $currentDbMedicationIds = MedicamentoRecetado::where('id_historia', $historiaClinica->id_historia)
                                                          ->pluck('id_medicamento_recetado')
                                                          ->toArray();
            Log::debug('Current DB medication IDs before processing request (for id_historia ' . $historiaClinica->id_historia . '): ', $currentDbMedicationIds);


            if ($request->has('medicamentos') && is_array($request->medicamentos)) {
                Log::debug('Processing ' . count($request->medicamentos) . ' medication items from request.');
                foreach ($request->medicamentos as $index => $medicamentoData) {
                    $nombreMedicamento = trim($medicamentoData['nombre_medicamento'] ?? '');

                    $hasMedicationData = !empty($nombreMedicamento) ||
                                         !empty($medicamentoData['cantidad_recetada']) ||
                                         !empty($medicamentoData['cantidad_dispensada']) ||
                                         !empty($medicamentoData['valor_unitario']) ||
                                         !empty($medicamentoData['total']);

                    if ($hasMedicationData) {
                        $medicamentoId = (int)($medicamentoData['id_medicamento_recetado'] ?? 0);

                        Log::debug("Item {$index}: Received ID: {$medicamentoId}, Name: '{$nombreMedicamento}'");

                        $processedMedicamento = null;
                        $isNew = false;

                        if ($medicamentoId > 0) {
                            // Intentar encontrar un medicamento existente vinculado a esta historia y con este ID
                            $processedMedicamento = MedicamentoRecetado::where('id_medicamento_recetado', $medicamentoId)
                                                                        ->where('id_historia', $historiaClinica->id_historia)
                                                                        ->first();
                            if (!$processedMedicamento) {
                                // Si no se encuentra (ej. ID incorrecto o no pertenece a esta historia), crear uno nuevo
                                Log::warning("Intento de actualizar un ID de medicamento inexistente o no vinculado: {$medicamentoId}. Creando uno nuevo en su lugar.");
                                $processedMedicamento = new MedicamentoRecetado();
                                $isNew = true;
                            }
                        } else {
                            // Para nuevos medicamentos (ID es 0 o nulo), siempre crear una nueva instancia
                            $processedMedicamento = new MedicamentoRecetado();
                            $isNew = true;
                        }

                        // Llenar el modelo con los datos (común para nuevos y existentes)
                        $processedMedicamento->fill([
                            'nombre_medicamento' => $nombreMedicamento,
                            'cantidad_recetada' => $medicamentoData['cantidad_recetada'] ?? null,
                            'cantidad_dispensada' => $medicamentoData['cantidad_dispensada'] ?? null,
                            'valor_unitario' => $medicamentoData['valor_unitario'] ?? null,
                            'total' => $medicamentoData['total'] ?? null,
                            'id_historia' => $historiaClinica->id_historia, // Siempre vincular a la historia actual
                            'id_usuario' => Auth::id(),
                            'id_adulto' => $id_adulto,
                        ]);

                        $processedMedicamento->save(); // Guardar el registro

                        $submittedMedicamentoIds[] = $processedMedicamento->id_medicamento_recetado;
                        Log::debug("Medication processed. ID: {$processedMedicamento->id_medicamento_recetado}. Action: " . ($isNew ? 'Created' : 'Updated') . ". ID added to list: {$processedMedicamento->id_medicamento_recetado}");

                    } else {
                        Log::debug("Saltando elemento de medicamento en el índice {$index} ya que no contiene datos relevantes (nombre_medicamento vacío).");
                    }
                }
            } else {
                Log::debug('No se encontró el array de datos de medicamentos en la solicitud o está vacío.');
            }

            Log::debug('Lista final de IDs de medicamentos enviados a mantener: ', $submittedMedicamentoIds);

            $idsToDelete = array_diff($currentDbMedicationIds, $submittedMedicamentoIds);

            if (!empty($idsToDelete)) {
                Log::debug('IDs de medicamentos a eliminar: ', $idsToDelete);
                MedicamentoRecetado::whereIn('id_medicamento_recetado', $idsToDelete)->delete();
                Log::debug('Medicamentos eliminados exitosamente que no estaban en los IDs enviados.');
            } else {
                Log::debug('No hay medicamentos para eliminar basado en los IDs enviados.');
            }

            DB::commit();

            return redirect()->route('responsable.enfermeria.medico.historia_clinica.index')->with('success', 'Exámenes Complementarios y Medicamentos guardados exitosamente.');

        } catch (ValidationException $e) {
            DB::rollBack();
            Log::error('Error de validación al guardar Exámenes Complementarios: ', ['errors' => $e->errors()]);
            return back()->withErrors($e->errors())->withInput()->with('active_tab', 'examenes');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error inesperado al guardar Exámenes Complementarios: ' . $e->getMessage());
            return back()->withErrors(['general_error' => 'Ocurrió un error al guardar los Exámenes Complementarios: ' . $e->getMessage()])
                         ->withInput()->with('active_tab', 'examenes');
        }
    }


    public function edit($id_historia, $activeTab = 'historia')
    {
        $historiaClinica = HistoriaClinica::with('adulto.persona', 'examenesComplementarios', 'medicamentosRecetados')->findOrFail($id_historia);
        $adulto = $historiaClinica->adulto;
        
        $examenesComplementarios = $historiaClinica->examenesComplementarios; 
        $medicamentosRecetados = $historiaClinica->medicamentosRecetados;

        $modoEdicion = true;

        if ($medicamentosRecetados->isEmpty()) {
            $medicamentosRecetados = collect([new MedicamentoRecetado()]);
        }

        session(['active_tab' => $activeTab]);

        return view('Medico.registrarHistoriaClinica', compact('adulto', 'modoEdicion', 'historiaClinica', 'examenesComplementarios', 'medicamentosRecetados', 'activeTab'));
    }

    public function showDetalle($id_historia, Request $request)
    {
        // Eager load las relaciones necesarias: adulto (con persona), examenesComplementarios,
        // medicamentosRecetados Y el usuario (con persona)
        $historiaClinica = HistoriaClinica::with('adulto.persona', 'examenesComplementarios', 'medicamentosRecetados', 'usuario.persona')->findOrFail($id_historia);
        
        // Obtener el tab activo de la URL, por defecto 'historia'
        $activeTab = $request->query('active_tab', 'historia');

        // Los datos para los partials
        $examenesComplementarios = $historiaClinica->examenesComplementarios;
        $medicamentosRecetados = $historiaClinica->medicamentosRecetados;

        // Devuelve la nueva vista de detalles con los datos
        return view('Medico.verDetallesHistoria', compact('historiaClinica', 'examenesComplementarios', 'medicamentosRecetados', 'activeTab'));
    }

    public function destroy($id_historia)
    {
        try {
            $historiaClinica = HistoriaClinica::findOrFail($id_historia);
            $historiaClinica->delete();

            return redirect()->route('responsable.enfermeria.medico.historia_clinica.index')->with('success', 'Historia Clínica eliminada exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al eliminar la Historia Clínica: ' . $e->getMessage());
        }
    }
}
