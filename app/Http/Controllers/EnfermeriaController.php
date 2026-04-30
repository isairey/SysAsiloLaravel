<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AdultoMayor;
use App\Models\Enfermeria; // Importar el modelo Enfermeria
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class EnfermeriaController extends Controller
{
    /**
     * Muestra el listado de adultos mayores con su última ficha de enfermería.
     * Esta será la vista principal del módulo de Enfermería.
     * Corresponde a views/Medico/indexEnfermeria.blade.php
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $search = $request->query('search'); // Obtener el término de búsqueda

        $adultosQuery = AdultoMayor::with('persona', 'latestEnfermeria');

        // Aplicar filtro de búsqueda si existe un término
        if ($search) {
            $adultosQuery->whereHas('persona', function ($query) use ($search) {
                $query->where('ci', 'like', '%' . $search . '%')
                      ->orWhere('nombres', 'like', '%' . $search . '%')
                      ->orWhere('primer_apellido', 'like', '%' . $search . '%')
                      ->orWhere('segundo_apellido', 'like', '%' . $search . '%');
            });
        }

        $adultos = $adultosQuery->paginate(10)->appends(['search' => $search]); // Paginación con el término de búsqueda

        // Obtener estadísticas
        $totalAdultosMayores = AdultoMayor::count();
        $totalFichasEnfermeria = Enfermeria::count(); // Obtener el total de fichas de enfermería

        return view('Medico.indexEnfermeria', compact('adultos', 'search', 'totalAdultosMayores', 'totalFichasEnfermeria')); // Pasar las estadísticas
    }

    /**
     * Muestra el formulario para registrar una nueva ficha de enfermería para un adulto mayor.
     *
     * @param int $id_adulto El ID del adulto mayor.
     * @return \Illuminate\View\View
     */
    public function create($id_adulto)
    {
        $adulto = AdultoMayor::with('persona')->findOrFail($id_adulto);
        $modoEdicion = false;
        $fichaEnfermeria = null;
        return view('Medico.registrarAtencionEnfermeria', compact('adulto', 'modoEdicion', 'fichaEnfermeria'));
    }

    /**
     * Almacena una nueva ficha de enfermería.
     *
     * @param Request $request
     * @param int $id_adulto El ID del adulto mayor.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, $id_adulto)
    {
        $request->validate([
            'presion_arterial' => 'nullable|string|max:255',
            'frecuencia_cardiaca' => 'nullable|string|max:255',
            'frecuencia_respiratoria' => 'nullable|string|max:255',
            'pulso' => 'nullable|string|max:255',
            'temperatura' => 'nullable|string|max:255',
            'control_oximetria' => 'nullable|string|max:255',
            'inyectables' => 'nullable|string',
            'peso_talla' => 'nullable|string|max:255',
            'orientacion_alimentacion' => 'nullable|string',
            'lavado_oidos' => 'nullable|string',
            'orientacion_tratamiento' => 'nullable|string',
            'curacion' => 'nullable|string',
            'adm_medicamentos' => 'nullable|string',
            'derivacion' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            $fichaEnfermeria = Enfermeria::create([
                'id_adulto' => $id_adulto,
                'presion_arterial' => $request->presion_arterial,
                'frecuencia_cardiaca' => $request->frecuencia_cardiaca,
                'frecuencia_respiratoria' => $request->frecuencia_respiratoria,
                'pulso' => $request->pulso,
                'temperatura' => $request->temperatura,
                'control_oximetria' => $request->control_oximetria,
                'inyectables' => $request->inyectables,
                'peso_talla' => $request->peso_talla,
                'orientacion_alimentacion' => $request->orientacion_alimentacion,
                'lavado_oidos' => $request->lavado_oidos,
                'orientacion_tratamiento' => $request->orientacion_tratamiento,
                'curacion' => $request->curacion,
                'adm_medicamentos' => $request->adm_medicamentos,
                'derivacion' => $request->derivacion,
                'id_usuario' => Auth::id(),
            ]);

            DB::commit();
            return redirect()->route('responsable.enfermeria.enfermeria.index')->with('success', 'Ficha de Enfermería registrada exitosamente.');

        } catch (ValidationException $e) {
            DB::rollBack();
            Log::error('Error de validación al guardar Ficha de Enfermería: ', ['errors' => $e->errors()]);
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error inesperado al guardar Ficha de Enfermería: ' . $e->getMessage());
            return back()->withErrors(['general_error' => 'Ocurrió un error al guardar la Ficha de Enfermería: ' . $e->getMessage()])
                         ->withInput();
        }
    }

    /**
     * Muestra el formulario para editar una ficha de enfermería existente.
     *
     * @param int $cod_enf El ID (cod_enf) de la ficha de enfermería.
     * @return \Illuminate\View\View
     */
    public function edit($cod_enf)
    {
        $fichaEnfermeria = Enfermeria::with('adulto.persona')->findOrFail($cod_enf);
        $adulto = $fichaEnfermeria->adulto;
        $modoEdicion = true;
        return view('Medico.registrarAtencionEnfermeria', compact('adulto', 'modoEdicion', 'fichaEnfermeria'));
    }

    /**
     * Actualiza una ficha de enfermería existente.
     *
     * @param Request $request
     * @param int $cod_enf El ID (cod_enf) de la ficha de enfermería.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $cod_enf)
    {
        $request->validate([
            'presion_arterial' => 'nullable|string|max:255',
            'frecuencia_cardiaca' => 'nullable|string|max:255',
            'frecuencia_respiratoria' => 'nullable|string|max:255',
            'pulso' => 'nullable|string|max:255',
            'temperatura' => 'nullable|string|max:255',
            'control_oximetria' => 'nullable|string|max:255',
            'inyectables' => 'nullable|string',
            'peso_talla' => 'nullable|string|max:255',
            'orientacion_alimentacion' => 'nullable|string',
            'lavado_oidos' => 'nullable|string',
            'orientacion_tratamiento' => 'nullable|string',
            'curacion' => 'nullable|string',
            'adm_medicamentos' => 'nullable|string',
            'derivacion' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            $fichaEnfermeria = Enfermeria::findOrFail($cod_enf);
            $fichaEnfermeria->update([
                'presion_arterial' => $request->presion_arterial,
                'frecuencia_cardiaca' => $request->frecuencia_cardiaca,
                'frecuencia_respiratoria' => $request->frecuencia_respiratoria,
                'pulso' => $request->pulso,
                'temperatura' => $request->temperatura,
                'control_oximetria' => $request->control_oximetria,
                'inyectables' => $request->inyectables,
                'peso_talla' => $request->peso_talla,
                'orientacion_alimentacion' => $request->orientacion_alimentacion,
                'lavado_oidos' => $request->lavado_oidos,
                'orientacion_tratamiento' => $request->orientacion_tratamiento,
                'curacion' => $request->curacion,
                'adm_medicamentos' => $request->adm_medicamentos,
                'derivacion' => $request->derivacion,
            ]);

            DB::commit();
            return redirect()->route('responsable.enfermeria.enfermeria.index')->with('success', 'Ficha de Enfermería actualizada exitosamente.');

        } catch (ValidationException $e) {
            DB::rollBack();
            Log::error('Error de validación al actualizar Ficha de Enfermería: ', ['errors' => $e->errors()]);
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error inesperado al actualizar Ficha de Enfermería: ' . $e->getMessage());
            return back()->withErrors(['general_error' => 'Ocurrió un error al actualizar la Ficha de Enfermería: ' . $e->getMessage()])
                         ->withInput();
        }
    }

    /**
     * Muestra los detalles de una ficha de enfermería específica.
     *
     * @param int $cod_enf El ID (cod_enf) de la ficha de enfermería.
     * @return \Illuminate\View\View
     */
    public function show($id_adulto) // Cambiado el parámetro a $id_adulto
    {
        try {
            // Cargar el AdultoMayor con todas sus fichas de enfermería y la información del usuario que las creó.
            $adulto = AdultoMayor::with([
                'persona',
                'enfermerias.usuario.persona' // Asumiendo que la relación es 'enfermerias' en AdultoMayor
            ])->findOrFail($id_adulto);

            // Ordenar las fichas por fecha de creación descendente para mostrarlas de la más nueva a la más antigua
            $fichasEnfermeria = $adulto->enfermerias->sortByDesc('created_at');

            // Pasamos el objeto $adulto completo y la colección de fichas de enfermería
            return view('Medico.VerDetallesEnfermeria', compact('adulto', 'fichasEnfermeria'));

        } catch (ModelNotFoundException $e) {
            Log::error("Adulto Mayor no encontrado con ID: {$id_adulto}. Error: " . $e->getMessage());
            return redirect()->route('responsable.enfermeria.enfermeria.index')->with('error', 'El adulto mayor no existe o ha sido eliminado.');
        } catch (\Exception $e) {
            Log::error('Error al cargar detalles de fichas de enfermería: ' . $e->getMessage(), ['id_adulto' => $id_adulto, 'trace' => $e->getTraceAsString()]);
            return back()->with('error', 'Ocurrió un error al cargar las fichas de enfermería: ' . $e->getMessage());
        }
    }

    /**
     * Elimina una ficha de enfermería.
     *
     * @param int $cod_enf El ID (cod_enf) de la ficha de enfermería.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($cod_enf)
    {
        try {
            $fichaEnfermeria = Enfermeria::findOrFail($cod_enf);
            $fichaEnfermeria->delete();
            return redirect()->route('responsable.enfermeria.enfermeria.index')->with('success', 'Ficha de Enfermería eliminada exitosamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar la Ficha de Enfermería: ' . $e->getMessage());
        }
    }
}
