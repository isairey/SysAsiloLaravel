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
use Illuminate\Database\Eloquent\ModelNotFoundException; // Importar esta excepción

class FisioKineController extends Controller
{
    /**
     * Muestra el listado de Adultos Mayores para Fisioterapia (para registro de nuevas fichas).
     * Incluye la última ficha de fisioterapia para mostrar los botones de CRUD contextuales.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function indexFisio(Request $request)
    {
        $search = $request->query('search');
        $adultos = collect();
        $totalAdultos = AdultoMayor::count();

        try {
            $query = AdultoMayor::with('persona', 'latestFisioterapia', 'fisioterapias'); // Cargar todas las fichas para el cálculo de exists()
                                                                                        // y también latestFisioterapia para los botones del index

            if ($search) {
                $query->whereHas('persona', function ($q) use ($search) {
                    $q->where('nombres', 'like', '%' . $search . '%')
                      ->orWhere('primer_apellido', 'like', '%' . $search . '%')
                      ->orWhere('segundo_apellido', 'like', '%' . $search . '%')
                      ->orWhere('ci', 'like', '%' . $search . '%');
                });
            }

            $adultos = $query->orderBy('created_at', 'desc')->paginate(10)->appends($request->query());

            Log::info('Datos de Adultos en indexFisio (después de la consulta):', [
                'count_total' => $totalAdultos,
                'count_paginated' => $adultos->count(),
                'is_empty' => $adultos->isEmpty(),
                'search_term' => $search
            ]);

        } catch (\Exception $e) {
            Log::error('Error en FisioKineController@indexFisio: ' . $e->getMessage(), ['exception' => $e]);
            return view('Medico.indexFisio', compact('adultos', 'search', 'totalAdultos'))
                         ->with('error', 'Ocurrió un error al cargar los adultos mayores para fisioterapia. Por favor, intente de nuevo más tarde.');
        }

        return view('Medico.indexFisio', compact('adultos', 'search', 'totalAdultos'));
    }

    /**
     * Muestra el formulario para registrar una nueva ficha de Fisioterapia para un Adulto Mayor.
     *
     * @param int $id_adulto
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function createFisio($id_adulto)
    {
        Log::info('Intentando cargar formulario createFisio para id_adulto:', ['id_adulto_recibido' => $id_adulto]);

        $usesSoftDeletes = in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses(AdultoMayor::class));
        Log::info('AdultoMayor model uses SoftDeletes:', ['status' => $usesSoftDeletes]);
        Log::info('AdultoMayor primary key:', ['key' => (new AdultoMayor())->getKeyName()]);


        try {
            $adulto = AdultoMayor::with('persona');
            if ($usesSoftDeletes) {
                $adulto = $adulto->withTrashed();
            }
            
            $adulto = $adulto->findOrFail($id_adulto);
            
            Log::info('Adulto encontrado en createFisio:', ['adulto_id' => $adulto->id_adulto, 'adulto_nombre' => optional($adulto->persona)->nombres]);

            $fisioterapia = new Fisioterapia();
            $historiaClinica = HistoriaClinica::where('id_adulto', $id_adulto)->first();

            return view('Medico.registrarFichaFisio', compact('adulto', 'fisioterapia', 'historiaClinica'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Error: AdultoMayor no encontrado en createFisio para ID: ' . $id_adulto, ['exception' => $e]);
            return back()->with('error', 'No se pudo cargar el formulario de Fisioterapia. El adulto mayor no fue encontrado o está inactivo.');
        } catch (\Exception $e) {
            Log::error('Error inesperado en FisioKineController@createFisio: ' . $e->getMessage(), ['id_adulto' => $id_adulto, 'exception' => $e]);
            return back()->with('error', 'Ocurrió un error inesperado al cargar el formulario de Fisioterapia.');
        }
    }

    /**
     * Almacena una nueva ficha de Fisioterapia en la base de datos.
     *
     * @param Request $request
     * @param int $id_adulto
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeFisio(Request $request, $id_adulto)
    {
        $request->validate([
            'num_emergencia' => 'nullable|string|max:255',
            'enfermedades_actuales' => 'nullable|string',
            'alergias' => 'nullable|string',
            'fecha_programacion' => 'required|date', // Se recomienda que la fecha de programación sea requerida.
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio', // Asegura que la fecha fin no sea anterior a la de inicio.
            'numero_sesiones' => 'nullable|integer|min:0', // Asegura que sea un número entero no negativo.
            'motivo_consulta' => 'nullable|string',
            'solicitud_atencion' => 'nullable|string',
            'equipos' => 'nullable|string|max:255',
            'id_historia' => 'nullable|exists:historia_clinica,id_historia',
        ]);

        DB::beginTransaction();
        try {
            $adulto = AdultoMayor::findOrFail($id_adulto);

            Fisioterapia::create([
                'id_adulto' => $adulto->id_adulto,
                'id_usuario' => Auth::id(),
                'id_historia' => $request->id_historia,
                'num_emergencia' => $request->num_emergencia,
                'enfermedades_actuales' => $request->enfermedades_actuales,
                'alergias' => $request->alergias,
                'fecha_programacion' => $request->fecha_programacion,
                'fecha_inicio' => $request->fecha_inicio, // <-- Campo nuevo
                'fecha_fin' => $request->fecha_fin,       // <-- Campo nuevo
                'numero_sesiones' => $request->numero_sesiones, // <-- Campo nuevo
                'motivo_consulta' => $request->motivo_consulta,
                'solicitud_atencion' => $request->solicitud_atencion,
                'equipos' => $request->equipos,
            ]);


            DB::commit();

            return redirect()->route('responsable.fisioterapia.fisiokine.indexFisio')->with('success', 'Ficha de Fisioterapia registrada exitosamente.');

        } catch (ValidationException $e) {
            DB::rollBack();
            Log::error('Error de validación al guardar Ficha de Fisioterapia: ', ['errors' => $e->errors()]);
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error inesperado al guardar Ficha de Fisioterapia: ' . $e->getMessage(), ['exception' => $e]);
            return back()->with('error', 'Ocurrió un error al guardar la Ficha de Fisioterapia: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Muestra el formulario para editar una ficha de Fisioterapia.
     *
     * @param int $cod_fisio
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function editFisio($cod_fisio)
    {
        try {
            // Asegúrate de que el modelo Fisioterapia no usa softDeletes si no los manejas en esta vista
            $fisioterapia = Fisioterapia::with('adulto.persona', 'historiaClinica')->findOrFail($cod_fisio);
            $adulto = $fisioterapia->adulto;
            $historiaClinica = $fisioterapia->historiaClinica;

            return view('Medico.registrarFichaFisio', compact('fisioterapia', 'adulto', 'historiaClinica'));
        } catch (\Exception $e) {
            Log::error('Error en FisioKineController@editFisio: ' . $e->getMessage(), ['cod_fisio' => $cod_fisio, 'exception' => $e]);
            return back()->with('error', 'No se pudo cargar la ficha de Fisioterapia para edición.');
        }
    }

    /**
     * Actualiza una ficha de Fisioterapia existente.
     *
     * @param Request $request
     * @param int $cod_fisio
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateFisio(Request $request, $cod_fisio)
    {
       $request->validate([
            'num_emergencia' => 'nullable|string|max:255',
            'enfermedades_actuales' => 'nullable|string',
            'alergias' => 'nullable|string',
            'fecha_programacion' => 'required|date',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'numero_sesiones' => 'nullable|integer|min:0',
            'motivo_consulta' => 'nullable|string',
            'solicitud_atencion' => 'nullable|string',
            'equipos' => 'nullable|string|max:255',
            'id_historia' => 'nullable|exists:historia_clinica,id_historia',
        ]);

        DB::beginTransaction();
        try {
            $fisioterapia = Fisioterapia::findOrFail($cod_fisio);

            $fisioterapia->update([
                'id_historia' => $request->id_historia,
                'num_emergencia' => $request->num_emergencia,
                'enfermedades_actuales' => $request->enfermedades_actuales,
                'alergias' => $request->alergias,
                'fecha_programacion' => $request->fecha_programacion,
                'fecha_inicio' => $request->fecha_inicio, // <-- Campo nuevo
                'fecha_fin' => $request->fecha_fin,       // <-- Campo nuevo
                'numero_sesiones' => $request->numero_sesiones, // <-- Campo nuevo
                'motivo_consulta' => $request->motivo_consulta,
                'solicitud_atencion' => $request->solicitud_atencion,
                'equipos' => $request->equipos,
            ]);

            DB::commit();

            return redirect()->route('responsable.fisioterapia.fisiokine.indexFisio')->with('success', 'Ficha de Fisioterapia actualizada exitosamente.');

        } catch (ValidationException $e) {
            DB::rollBack();
            Log::error('Error de validación al actualizar Ficha de Fisioterapia: ', ['errors' => $e->errors()]);
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error inesperado al actualizar Ficha de Fisioterapia: ' . $e->getMessage(), ['exception' => $e]);
            return back()->with('error', 'Ocurrió un error al actualizar la Ficha de Fisioterapia: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Muestra los detalles de TODAS las fichas de Fisioterapia para un Adulto Mayor específico.
     *
     * @param int $id_adulto El ID del adulto mayor.
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function showFisio($id_adulto) // Cambiado el parámetro a $id_adulto
    {
        try {
            // Cargar el AdultoMayor con todas sus fichas de fisioterapia y la información del usuario que las creó,
            // y la historia clínica asociada a cada ficha (si existe).
            $adulto = AdultoMayor::with([
                'persona',
                'fisioterapias.usuario.persona', // Asumiendo que la relación es 'fisioterapias' en AdultoMayor
                'fisioterapias.historiaClinica' // Y que Fisioterapia tiene relación con HistoriaClinica
            ])->findOrFail($id_adulto);

            // Ordenar las fichas por fecha de creación descendente para mostrarlas de la más nueva a la más antigua
            $fichasFisioterapia = $adulto->fisioterapias->sortByDesc('created_at');

            // Pasamos el objeto $adulto completo y la colección de fichas de fisioterapia
            return view('Medico.verDetallesFisio', compact('adulto', 'fichasFisioterapia'));

        } catch (ModelNotFoundException $e) {
            Log::error("Adulto Mayor no encontrado con ID: {$id_adulto} en showFisio. Error: " . $e->getMessage());
            return redirect()->route('responsable.fisioterapia.fisiokine.indexFisio')->with('error', 'El adulto mayor no existe o ha sido eliminado.');
        } catch (\Exception $e) {
            Log::error('Error al cargar detalles de fichas de fisioterapia: ' . $e->getMessage(), ['id_adulto' => $id_adulto, 'trace' => $e->getTraceAsString()]);
            return back()->with('error', 'Ocurrió un error al cargar las fichas de fisioterapia: ' . $e->getMessage());
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
            $idAdulto = $fisioterapia->id_adulto; // Obtener el id_adulto antes de eliminar
            $fisioterapia->delete();
            // Redirigir de vuelta a la vista de detalles de todas las fichas del adulto mayor
            return redirect()->route('responsable.fisioterapia.fisiokine.showFisio', ['id_adulto' => $idAdulto])->with('success', 'Ficha de Fisioterapia eliminada exitosamente.');
        } catch (\Exception $e) {
            Log::error('Error al eliminar Ficha de Fisioterapia: ' . $e->getMessage(), ['cod_fisio' => $cod_fisio, 'exception' => $e]);
            return back()->with('error', 'Error al eliminar la Ficha de Fisioterapia: ' . $e->getMessage());
        }
    }

    // --- MÉTODOS PARA KINESIOLOGÍA (MANEJADOS EN ESTE CONTROLADOR) ---

    /**
     * Muestra el listado de Adultos Mayores para Kinesiología (para registro de nuevas fichas).
     * Incluye la última ficha de kinesiología para mostrar los botones de CRUD contextuales.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function indexKine(Request $request)
    {
        $search = $request->query('search');
        $adultos = collect();
        $totalAdultos = AdultoMayor::count();

        try {
            $query = AdultoMayor::with('persona', 'latestKinesiologia', 'kinesiologias'); // Cargar todas las fichas para exists()
                                                                                       // y latestKinesiologia para los botones del index

            if ($search) {
                $query->whereHas('persona', function ($q) use ($search) {
                    $q->where('nombres', 'like', '%' . $search . '%')
                      ->orWhere('primer_apellido', 'like', '%' . $search . '%')
                      ->orWhere('segundo_apellido', 'like', '%' . $search . '%')
                      ->orWhere('ci', 'like', '%' . $search . '%');
                });
            }

            $adultos = $query->orderBy('created_at', 'desc')->paginate(10)->appends($request->query());

            Log::info('Datos de Adultos en indexKine (después de la consulta):', [
                'count_total' => $totalAdultos,
                'count_paginated' => $adultos->count(),
                'is_empty' => $adultos->isEmpty(),
                'adultos_data_first_5' => $adultos->take(5)->toArray(),
                'search_term' => $search
            ]);

        } catch (\Exception $e) {
            Log::error('Error en FisioKineController@indexKine: ' . $e->getMessage(), ['exception' => $e]);
            return view('Medico.indexKine', compact('adultos', 'search', 'totalAdultos'))
                         ->with('error', 'Ocurrió un error al cargar los adultos mayores para kinesiología. Por favor, intente de nuevo más tarde.');
        }

        return view('Medico.indexKine', compact('adultos', 'search', 'totalAdultos'));
    }

    /**
     * Muestra el formulario para registrar una nueva ficha de Kinesiología para un Adulto Mayor.
     *
     * @param int $id_adulto
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function createKine($id_adulto)
    {
        Log::info('Intentando cargar formulario createKine para id_adulto:', ['id_adulto_recibido' => $id_adulto]);

        $usesSoftDeletes = in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses(AdultoMayor::class));
        Log::info('AdultoMayor model uses SoftDeletes:', ['status' => $usesSoftDeletes]);
        Log::info('AdultoMayor primary key:', ['key' => (new AdultoMayor())->getKeyName()]);

        try {
            // Cargar adulto y su relación con persona
            $adulto = AdultoMayor::with('persona');
            if ($usesSoftDeletes) {
                $adulto = $adulto->withTrashed();
            }
            $adulto = $adulto->findOrFail($id_adulto);

            Log::info('Adulto encontrado en createKine:', ['adulto_id' => $adulto->id_adulto, 'adulto_nombre' => optional($adulto->persona)->nombres]);

            // Cargar la historia clínica asociada al adulto mayor
            $historiaClinica = HistoriaClinica::where('id_adulto', $id_adulto)->first();

            $kinesiologia = new Kinesiologia();
            return view('Medico.registrarFichaKine', compact('adulto', 'kinesiologia', 'historiaClinica'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Error: AdultoMayor no encontrado en createKine para ID: ' . $id_adulto, ['exception' => $e]);
            return back()->with('error', 'No se pudo cargar el formulario de Kinesiología. El adulto mayor no fue encontrado o está inactivo.');
        } catch (\Exception $e) {
            Log::error('Error inesperado en FisioKineController@createKine: ' . $e->getMessage(), ['id_adulto' => $id_adulto, 'exception' => $e]);
            return back()->with('error', 'Ocurrió un error inesperado al cargar el formulario de Kinesiología.');
        }
    }

    /**
     * Almacena una nueva ficha de Kinesiología en la base de datos.
     *
     * @param Request $request
     * @param int $id_adulto
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeKine(Request $request, $id_adulto)
    {
        // Validación solo para los campos específicos de Kinesiología y el id_historia
        $request->validate([
            'id_adulto' => 'required|exists:adulto_mayor,id_adulto',
            'id_historia' => 'nullable|exists:historia_clinica,id_historia',
            'entrenamiento_funcional' => 'boolean',
            'gimnasio_maquina' => 'boolean',
            'aquafit' => 'boolean',
            'hidroterapia' => 'boolean',
            'manana' => 'boolean',
            'tarde' => 'boolean',
        ]);

        DB::beginTransaction();
        try {
            $adulto = AdultoMayor::findOrFail($id_adulto);

            Kinesiologia::create([
                'id_adulto' => $adulto->id_adulto,
                'id_usuario' => Auth::id(),
                'id_historia' => $request->id_historia,
                'entrenamiento_funcional' => $request->has('entrenamiento_funcional'),
                'gimnasio_maquina' => $request->has('gimnasio_maquina'),
                'aquafit' => $request->has('aquafit'),
                'hidroterapia' => $request->has('hidroterapia'),
                'manana' => $request->has('manana'),
                'tarde' => $request->has('tarde'),
                // Los campos de texto/fecha de Fisioterapia NO se usan en este formulario de Kinesiología.
                // Se establecen a null si la columna es nullable para evitar errores de NOT NULL.
                'num_emergencia' => null,
                'enfermedades_actuales' => null,
                'alergias' => null,
                'fecha_programacion' => null,
                'motivo_consulta' => null,
                'solicitud_atencion' => null,
                'equipos' => null,
            ]);

            DB::commit();

            return redirect()->route('responsable.kinesiologia.fisiokine.indexKine')->with('success', 'Ficha de Kinesiología registrada exitosamente.');

        } catch (ValidationException $e) {
            DB::rollBack();
            Log::error('Error de validación al guardar Ficha de Kinesiología: ', ['errors' => $e->errors()]);
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error inesperado al guardar Ficha de Kinesiología: ' . $e->getMessage(), ['exception' => $e]);
            return back()->with('error', 'Ocurrió un error al guardar la Ficha de Kinesiología: ' . $e->getMessage())->withInput();
        }
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
            $historiaClinica = $kinesiologia->historiaClinica; // Asegúrate de cargar la historia clínica

            return view('Medico.registrarFichaKine', compact('kinesiologia', 'adulto', 'historiaClinica'));
        } catch (\Exception $e) {
            Log::error('Error en FisioKineController@editKine: ' . $e->getMessage(), ['cod_kine' => $cod_kine, 'exception' => $e]);
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
        // Validación solo para los campos específicos de Kinesiología
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
                // Los campos de texto/fecha de Fisioterapia NO se actualizan desde este formulario.
                // Se mantienen sus valores existentes en la base de datos si existen.
            ]);

            DB::commit();

            return redirect()->route('responsable.kinesiologia.fisiokine.indexKine')->with('success', 'Ficha de Kinesiología actualizada exitosamente.');

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
     * Muestra los detalles de TODAS las fichas de Kinesiología para un Adulto Mayor específico.
     *
     * @param int $id_adulto El ID del adulto mayor.
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function showKine($id_adulto) // Cambiado el parámetro a $id_adulto
    {
        try {
            // Cargar el AdultoMayor con todas sus fichas de kinesiología y la información del usuario que las creó,
            // y la historia clínica asociada a cada ficha (si existe).
            $adulto = AdultoMayor::with([
                'persona',
                'kinesiologias.usuario.persona', // Asumiendo que la relación es 'kinesiologias' en AdultoMayor
                'kinesiologias.historiaClinica' // Y que Kinesiologia tiene relación con HistoriaClinica
            ])->findOrFail($id_adulto);

            // Ordenar las fichas por fecha de creación descendente para mostrarlas de la más nueva a la más antigua
            $fichasKinesiologia = $adulto->kinesiologias->sortByDesc('created_at');

            // Pasamos el objeto $adulto completo y la colección de fichas de kinesiología
            return view('Medico.verDetallesKine', compact('adulto', 'fichasKinesiologia'));

        } catch (ModelNotFoundException $e) {
            Log::error("Adulto Mayor no encontrado con ID: {$id_adulto} en showKine. Error: " . $e->getMessage());
            return redirect()->route('responsable.kinesiologia.fisiokine.indexKine')->with('error', 'El adulto mayor no existe o ha sido eliminado.');
        } catch (\Exception $e) {
            Log::error('Error al cargar detalles de fichas de kinesiología: ' . $e->getMessage(), ['id_adulto' => $id_adulto, 'trace' => $e->getTraceAsString()]);
            return back()->with('error', 'Ocurrió un error al cargar las fichas de kinesiología: ' . $e->getMessage());
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
            $idAdulto = $kinesiologia->id_adulto; // Obtener el id_adulto antes de eliminar
            $kinesiologia->delete();
            // Redirigir de vuelta a la vista de detalles de todas las fichas del adulto mayor
            return redirect()->route('responsable.kinesiologia.fisiokine.showKine', ['id_adulto' => $idAdulto])->with('success', 'Ficha de Kinesiología eliminada exitosamente.');
        } catch (\Exception $e) {
            Log::error('Error al eliminar Ficha de Kinesiología: ' . $e->getMessage(), ['cod_kine' => $cod_kine, 'exception' => $e]);
            return back()->with('error', 'Error al eliminar la Ficha de Kinesiología: ' . $e->getMessage());
        }
    }
}