<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AdultoMayor;
use App\Models\{ActividadLaboral, Encargado, PersonaNatural, PersonaJuridica, Denunciado, GrupoFamiliar, Croquis, SeguimientoCaso, Intervencion, AnexoN3, AnexoN5, Persona}; // Asegúrate de importar Persona
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class RegistrarCasoController extends Controller
{
    /**
     * Lista de adultos mayores registrados.
     */
    public function index()
    {
        // Cargar el AdultoMayor con todas sus relaciones para que la lógica de la vista
        // sobre si ya tiene un caso de protección sea eficiente.
        $adultos = AdultoMayor::with([
            'persona',
            'actividadLaboral',
            'encargados',
            'denunciado',
            'grupoFamiliar',
            'croquis',
            'seguimientos',
            'anexoN3',
            'anexoN5'
        ])->paginate(10); // Añadido paginación de 10 elementos por página

        return view('Proteccion.indexPro', compact('adultos'));
    }

    /**
     * Muestra el formulario en tabs para INICIAR el registro de un nuevo caso
     * para un Adulto Mayor ya existente.
     * Los campos de las pestañas aparecerán vacíos si no hay datos de caso previos.
     *
     * @param int $id_adulto
     * @param string|null $active_tab Pestaña activa por defecto
     */
    public function registerNewCaseForm($id_adulto, $active_tab = null)
    {
        // Carga el AdultoMayor con todas sus relaciones,
        // esto es necesario para que los formularios de las pestañas
        // puedan intentar acceder a los datos, aunque estén vacíos inicialmente.
        $adulto = AdultoMayor::with([
            'persona',
            'actividadLaboral',
            'encargados.personaNatural',
            'encargados.personaJuridica',
            'denunciado.personaNatural',
            'grupoFamiliar',
            'croquis',
            'seguimientos.intervencion',
            'anexoN3.personaNatural',
            'anexoN5.usuarios'
        ])->findOrFail($id_adulto);

        // Intenta obtener la Intervención del último seguimiento para pre-llenar si existe.
        // Si es un caso realmente nuevo, esto estará vacío.
        $latestSeguimiento = $adulto->seguimientos->sortByDesc('created_at')->first();
        $intervencionData = [];
        if ($latestSeguimiento && $latestSeguimiento->intervencion) {
            $intervencionData = $latestSeguimiento->intervencion->toArray();
        }
        $adulto->intervencion_data = $intervencionData;

        // Determina la pestaña activa: desde la URL, luego desde la sesión, luego el valor por defecto.
        $activeTab = $active_tab ?? session('active_tab', 'actividad');
        // Almacena la pestaña activa en la sesión para que se mantenga en los redirects.
        session(['active_tab' => $activeTab]);

        $modoEdicion = false; // ESTO ES CLAVE: Indica que estamos en modo "registro de caso"

        return view('Proteccion.registrarCaso', compact('adulto', 'activeTab', 'modoEdicion'));
    }


    /**
     * Muestra el formulario en tabs para EDITAR un caso existente.
     * Idéntico a tu método 'show' anterior, ahora con un nombre más semántico.
     *
     * @param int $id_adulto
     * @param string|null $active_tab Pestaña activa por defecto
     */
    public function edit($id_adulto, $active_tab = null)
    {
        // Carga todas las relaciones necesarias para pre-llenar los formularios.
        $adulto = AdultoMayor::with([
            'persona',
            'actividadLaboral',
            'encargados.personaNatural',
            'encargados.personaJuridica',
            'denunciado.personaNatural',
            'grupoFamiliar',
            'croquis',
            // Asegúrate de precargar la intervención a través de los seguimientos
            'seguimientos.intervencion',
            'anexoN3.personaNatural',
            'anexoN5.usuario.persona' 
        ])->findOrFail($id_adulto);

        // Intenta obtener la Intervención del último seguimiento para pre-llenar si existe.
        $latestSeguimiento = $adulto->seguimientos->sortByDesc('created_at')->first();
        $intervencionData = []; // Inicializamos como un array vacío

        if ($latestSeguimiento && $latestSeguimiento->intervencion) {
            // Convierte el modelo Intervencion a un array
            $intervencionData = $latestSeguimiento->intervencion->toArray();

            // *** ESTO ES LO CLAVE: Formatear la fecha si existe y es un objeto Carbon ***
            if (isset($latestSeguimiento->intervencion->fecha_intervencion) && $latestSeguimiento->intervencion->fecha_intervencion instanceof \Carbon\Carbon) {
                // Formateamos la fecha a 'YYYY-MM-DD', el formato que espera el input type="date"
                $intervencionData['fecha_intervencion'] = $latestSeguimiento->intervencion->fecha_intervencion->format('Y-m-d');
            } else {
                // Si no es un objeto Carbon (ej. es null o una cadena no válida), asegúrate de que sea una cadena vacía
                $intervencionData['fecha_intervencion'] = '';
            }
        } else {
            // Si no hay intervención o seguimiento, aseguramos que 'fecha_intervencion' esté presente pero vacía
            $intervencionData['fecha_intervencion'] = '';
        }
        
        // Asignamos el array de datos de intervención (incluyendo la fecha formateada)
        $adulto->intervencion_data = $intervencionData;

        // Determina la pestaña activa: desde la URL, luego desde la sesión, luego el valor por defecto.
        $activeTab = $active_tab ?? session('active_tab', 'actividad');
        // Almacena la pestaña activa en la sesión para que se mantenga en los redirects.
        session(['active_tab' => $activeTab]);

        $modoEdicion = true; // ESTO ES CLAVE: Indica que estamos en modo "edición de caso"

        return view('Proteccion.registrarCaso', compact('adulto', 'activeTab', 'modoEdicion'));
    }


    /**
     * Muestra el detalle completo de un caso.
     *
     * @param int $id_adulto
     */
    public function showDetalle($id_adulto)
    {
        try {
            $adulto = AdultoMayor::with([
                'persona',
                'actividadLaboral',
                'encargados.personaNatural',
                'encargados.personaJuridica',
                'grupoFamiliar',
                'croquis',
                'seguimientos.usuario',
                'seguimientos.intervencion',
                'denunciado.personaNatural',
                'anexoN3.personaNatural', // Asegura que PersonaNatural de AnexoN3 se cargue
                'anexoN5.usuarios',
            ])->findOrFail($id_adulto);

            // CORRECCIÓN AQUÍ: No necesitas ->first() si 'encargados' es una relación hasOne.
            // Si no hay un encargado, $adulto->encargados será null.
            $encargado = $adulto->encargados;

            return view('Proteccion.verDetalleCaso', compact('adulto', 'encargado'));

        } catch (ModelNotFoundException $e) {
            Log::error("Adulto Mayor no encontrado con ID: {$id_adulto}. Error: " . $e->getMessage());
            return redirect()->route('legal.reportes_proteccion.index')->with('error', 'El caso que intentas ver no existe o ha sido eliminado.');
        } catch (\Exception $e) {
            Log::error('Error al cargar detalle del caso: ' . $e->getMessage(), ['id_adulto' => $id_adulto, 'trace' => $e->getTraceAsString()]);
            return back()->with('error', 'Ocurrió un error al cargar el detalle del caso: ' . $e->getMessage());
        }
    }

    /**
     * Guarda/actualiza los datos de la Actividad Laboral.
     *
     * @param Request $request
     * @param int $id_adulto
     */
    public function storeActividad(Request $request, $id_adulto)
    {
        // 1. Detectar si la pestaña fue omitida
        if ($request->has('_skip_actividad_laboral') && $request->input('_skip_actividad_laboral') === '1') {
            DB::beginTransaction();
            try {
                // Si el usuario elige omitir, eliminamos cualquier registro existente
                // para ActividadLaboral asociado a este adulto.
                ActividadLaboral::where('id_adulto', $id_adulto)->delete();
                DB::commit();
                Log::info("Actividad Laboral omitida y registro eliminado para el adulto ID: $id_adulto");
                return redirect()->route('legal.caso.edit', ['id_adulto' => $id_adulto, 'active_tab' => 'encargado'])
                                 ->with('success', 'Actividad Laboral omitida exitosamente.');
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error al intentar eliminar Actividad Laboral al omitir: ' . $e->getMessage());
                return back()->withErrors(['general_error' => 'Ocurrió un error al omitir la Actividad Laboral.'])
                             ->withInput()->with('active_tab', 'actividad');
            }
        }

        // 2. Si no se omitió, proceder con la validación y guardado/actualización
        $rules = [
            // nombre_actividad ya es nullable en la migración.
            // Aquí definimos que si se envía, debe ser string, pero no es requerido.
            'nombre_actividad'  => 'nullable|string|max:255',
            'direccion_trabajo' => 'nullable|string|max:255',
            'telefono_laboral'  => 'nullable|string|max:20',
            'horario'           => 'nullable|string|max:50',
            'horas_x_dia'       => 'nullable|string|max:50',
            'rem_men_aprox'     => 'nullable|string|max:100',
        ];

        try {
            $request->validate($rules, [], [
                'nombre_actividad'  => 'Nombre de Actividad Laboral',
                'direccion_trabajo' => 'Dirección de Trabajo',
                'telefono_laboral'  => 'Teléfono Laboral',
                'horario'           => 'Horario',
                'horas_x_dia'       => 'Horas por día',
                'rem_men_aprox'     => 'Remuneración Mensual Aproximada',
            ]);

            DB::beginTransaction();

            // Buscar y actualizar o crear la Actividad Laboral.
            // firstOrNew es adecuado aquí. Si no se encontró y el usuario no omitió, se crea.
            $actividadLaboral = ActividadLaboral::firstOrNew(['id_adulto' => $id_adulto]);

            // Fill solo si se proporcionaron datos (no null, no cadena vacía)
            // Se puede hacer un filtro más explícito si quieres que solo se guarden campos no vacíos
            $dataToFill = $request->only([
                'nombre_actividad', 'direccion_trabajo', 'telefono_laboral',
                'horario', 'horas_x_dia', 'rem_men_aprox'
            ]);
            
            // Si todos los campos recibidos están vacíos, y el registro ya existe, lo eliminamos.
            // Si no existe, no creamos uno vacío.
            $allFieldsEmpty = true;
            foreach ($dataToFill as $value) {
                if (!empty($value)) {
                    $allFieldsEmpty = false;
                    break;
                }
            }

            if ($allFieldsEmpty) {
                // Si todos los campos enviados están vacíos, y ya existe un registro, lo eliminamos.
                // Si no existe, simplemente no lo creamos.
                if ($actividadLaboral->exists) {
                    $actividadLaboral->delete();
                    Log::info("Actividad Laboral vacía, registro eliminado para el adulto ID: $id_adulto");
                } else {
                    // No hay registro y todos los campos están vacíos, no hay nada que guardar.
                    Log::info("Actividad Laboral vacía, no se creó registro para el adulto ID: $id_adulto");
                }
            } else {
                // Si hay al menos un campo con datos, guardamos/actualizamos.
                $actividadLaboral->fill($dataToFill);
                $actividadLaboral->id_adulto = $id_adulto; // Asegura la relación
                $actividadLaboral->save();
                Log::info("Datos de Actividad Laboral registrados/actualizados para el adulto ID: $id_adulto");
            }

            DB::commit();
            return redirect()->route('legal.caso.edit', ['id_adulto' => $id_adulto, 'active_tab' => 'encargado'])
                             ->with('success', 'Actividad Laboral guardada/omitida exitosamente.');

        } catch (ValidationException $e) {
            DB::rollBack();
            Log::error('Error de validación en Actividad Laboral: ', ['errors' => $e->errors()]);
            return back()->withErrors($e->errors())->withInput()->with('active_tab', 'actividad');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error inesperado al guardar Actividad Laboral: ' . $e->getMessage());
            return back()->withErrors(['general_error' => 'Ocurrió un error al guardar la Actividad Laboral.'])
                         ->withInput()->with('active_tab', 'actividad');
        }
    }

    /**
     * Guarda/actualiza los datos del Encargado.
     *
     * @param Request $request
     * @param int $id_adulto
     */
    public function storeEncargado(Request $request, $id_adulto)
    {
        // Ya no necesitamos determinar si ya existe un Encargado para ignorar su CI,
        // porque el CI ya no será único.
        $encargadoExistente = Encargado::where('id_adulto', $id_adulto)->first();
        $personaNaturalIdToIgnore = null; // Mantener por si acaso, pero ya no se usa para la unicidad del CI.

        // 1. Construir la regla dinámica para el CI de Persona Natural (Encargado).
        // La regla 'unique' ha sido eliminada.
        $ciRule = 'nullable|string|max:20';

        // 2. Definir las reglas de validación completas.
        $rules = [
            'tipo_encargado' => 'required|in:natural,juridica',
        ];

        if ($request->tipo_encargado === 'natural') {
            $rules = array_merge($rules, [
                'encargado_natural.nombres'             => 'required|string|max:255',
                'encargado_natural.primer_apellido'     => 'required|string|max:100',
                'encargado_natural.segundo_apellido'    => 'nullable|string|max:100',
                'encargado_natural.edad'                => 'required|integer|min:1|max:120',
                'encargado_natural.ci'                  => $ciRule, // Usamos la regla dinámica del CI (ahora solo nullable|string|max:20)
                'encargado_natural.telefono'            => 'nullable|string|max:20',
                'encargado_natural.direccion_domicilio' => 'nullable|string|max:255',
                'encargado_natural.relacion_parentesco' => 'nullable|string|max:100',
                'encargado_natural.direccion_de_trabajo' => 'nullable|string|max:255',
                'encargado_natural.ocupacion'           => 'nullable|string|max:100',
            ]);
        } else {
            $rules = array_merge($rules, [
                'nombre_institucion' => 'required|string|max:255',
                'direccion'          => 'required|string|max:255',
                'telefono_juridica'  => 'required|string|max:20',
                'nombre_funcionario' => 'required|string|max:255',
            ]);
        }

        try {
            $request->validate($rules, [], [
                'tipo_encargado'                     => 'Tipo de Encargado',
                'encargado_natural.nombres'          => 'Nombres del Encargado (Natural)',
                'encargado_natural.primer_apellido'  => 'Primer Apellido del Encargado (Natural)',
                'encargado_natural.ci'               => 'CI del Encargado (Natural)',
                // El mensaje personalizado para 'ci.unique' ha sido eliminado.
                'nombre_institucion'                 => 'Nombre de Institución (Encargado Jurídica)',
                'direccion'                          => 'Dirección (Encargado Jurídica)',
                'telefono_juridica'                  => 'Teléfono (Encargado Jurídica)',
                'nombre_funcionario'                 => 'Nombre de Funcionario (Encargado Jurídica)',
            ]);

            DB::beginTransaction();

            // La validación de duplicidad de CI para Persona Natural en AnexoN3 ha sido eliminada.
            // Si necesitas alguna otra validación de AnexoN3 que no dependa de la unicidad del CI,
            // deberás añadirla aquí.

            $encargado = Encargado::firstOrNew(['id_adulto' => $id_adulto]);
            $encargado->tipo_encargado = $request->tipo_encargado;
            $encargado->save();

            if ($request->tipo_encargado === 'natural') {
                // Si el tipo cambia de Jurídica a Natural, eliminamos la PersonaJuridica anterior
                if ($encargado->personaJuridica) {
                    $encargado->personaJuridica->delete();
                }

                $encargadoPersonaData = $request->input('encargado_natural');
                // Asegura que esta PersonaNatural no se asocia como denunciado.
                $encargadoPersonaData['id_denunciado'] = null;

                // Buscar o crear la PersonaNatural asociada a este Encargado
                // firstOrNew busca por id_encargado, si no existe, crea una nueva instancia.
                // Si ya existe una persona natural asociada a este encargado, la actualiza.
                $personaNatural = PersonaNatural::firstOrNew(['id_encargado' => $encargado->id_encargado]);
                $personaNatural->fill($encargadoPersonaData);
                $personaNatural->id_encargado = $encargado->id_encargado; // Asegurar la vinculación
                $personaNatural->save();

            } else { // tipo_encargado === 'juridica'
                // Si el tipo cambia de Natural a Jurídica, eliminamos la PersonaNatural anterior
                if ($encargado->personaNatural) {
                    $encargado->personaNatural->delete();
                }

                $personaJuridica = PersonaJuridica::firstOrNew(['id_encargado' => $encargado->id_encargado]);
                $personaJuridica->fill($request->only([
                    'nombre_institucion', 'direccion', 'telefono_juridica', 'nombre_funcionario'
                ]));
                $personaJuridica->id_encargado = $encargado->id_encargado;
                $personaJuridica->save();
            }

            DB::commit();
            Log::info("Datos de Encargado (Tipo: {$request->tipo_encargado}) registrados/actualizados para el adulto ID: $id_adulto");
            return redirect()->route('legal.caso.edit', ['id_adulto' => $id_adulto, 'active_tab' => 'denunciado'])
                             ->with('success', 'Encargado guardado exitosamente.');

        } catch (ValidationException $e) {
            DB::rollBack();
            Log::error('Error de validación en Encargado: ', ['errors' => $e->errors()]);
            return back()->withErrors($e->errors())->withInput()->with('active_tab', 'encargado');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error inesperado al guardar Encargado: ' . $e->getMessage());
            return back()->withErrors(['general_error' => 'Ocurrió un error al guardar el Encargado.'])
                         ->withInput()->with('active_tab', 'encargado');
        }
    }
    /**
     * Guarda/actualiza los datos del Denunciado.
     *
     * @param Request $request
     * @param int $id_adulto
     */
    public function storeDenunciado(Request $request, $id_adulto)
    {
        // 1. Determinar si ya existe un Denunciado para este AdultoMayor.
        // Esto nos permite saber si estamos creando uno nuevo o actualizando uno existente.
        // ¡Esta línea es crucial para definir $denunciadoExistente!
        $denunciadoExistente = Denunciado::where('id_adulto', $id_adulto)->first();
        $personaNaturalIdToIgnore = null; // Se mantiene, aunque ya no se usa para la unicidad del CI.

        // Si ya hay un Denunciado y está asociado a una PersonaNatural, obtenemos su ID.
        // Esto es para la lógica de actualizar la PersonaNatural existente, no para la unicidad del CI.
        if ($denunciadoExistente && $denunciadoExistente->id_natural) {
            $personaNaturalIdToIgnore = $denunciadoExistente->id_natural;
        }

        // 2. Construir la regla para el CI:
        //    - 'nullable': Permite que el campo CI sea opcional (puede ser nulo).
        //    - 'string|max:20': Debe ser una cadena y con un máximo de 20 caracteres.
        // La regla 'unique' ha sido eliminada, como solicitaste.
        $ciRule = 'nullable|string|max:20';

        // 3. Definir las reglas de validación completas para todos los campos.
        $rules = [
            'denunciado_natural.nombres'             => 'required|string|max:255',
            'denunciado_natural.primer_apellido'     => 'required|string|max:100',
            'denunciado_natural.segundo_apellido'    => 'nullable|string|max:100',
            'denunciado_natural.edad'                => 'required|integer|min:1|max:120',
            'denunciado_natural.ci'                  => $ciRule, // Usamos la regla dinámica del CI (ahora solo nullable|string|max:20)
            'denunciado_natural.telefono'            => 'nullable|string|max:20',
            'denunciado_natural.direccion_domicilio' => 'nullable|string|max:255',
            'denunciado_natural.relacion_parentesco' => 'nullable|string|max:100',
            'denunciado_natural.direccion_de_trabajo' => 'nullable|string|max:255',
            'denunciado_natural.ocupacion'           => 'nullable|string|max:100',
            'sexo'                                   => 'required|in:M,F',
            'descripcion_hechos'                     => 'required|string|max:1000',
        ];

        try {
            // 4. Validar la solicitud con las reglas definidas.
            $request->validate($rules, [], [
                'denunciado_natural.nombres'         => 'Nombres del Denunciado',
                'denunciado_natural.primer_apellido' => 'Primer Apellido del Denunciado',
                'denunciado_natural.ci'              => 'CI del Denunciado',
                'sexo'                               => 'Sexo del Denunciado',
                'descripcion_hechos'                 => 'Descripción de los hechos',
            ]);

            DB::beginTransaction();

            $denunciadoPersonaData = $request->input('denunciado_natural');
            // Asegura que esta PersonaNatural no se asocia como encargado.
            $denunciadoPersonaData['id_encargado'] = null;

            $personaNatural = null;

            // Si el denunciado ya existe y tiene una persona natural asociada, la actualizamos.
            // De lo contrario, creamos una nueva persona natural.
            if ($denunciadoExistente && $denunciadoExistente->id_natural) {
                $personaNatural = PersonaNatural::find($denunciadoExistente->id_natural);
                if (!$personaNatural) {
                    throw new \Exception('Persona Natural asociada al denunciado no encontrada para actualizar.');
                }
                $personaNatural->fill($denunciadoPersonaData);
                $personaNatural->save();
            } else {
                $personaNatural = PersonaNatural::create($denunciadoPersonaData);
            }

            // 5. Crear o actualizar el registro de Denunciado.
            $denunciado = Denunciado::firstOrNew(['id_adulto' => $id_adulto]);
            $denunciado->id_natural = $personaNatural->id_natural; // Vincula la PersonaNatural
            $denunciado->sexo = $request->sexo; // Sexo específico para la tabla 'denunciado'
            $denunciado->descripcion_hechos = $request->descripcion_hechos;
            $denunciado->save();

            DB::commit();
            Log::info("Datos de Denunciado registrados/actualizados para el adulto ID: $id_adulto (Persona Natural ID: {$personaNatural->id_natural})");
            return redirect()->route('legal.caso.edit', ['id_adulto' => $id_adulto, 'active_tab' => 'grupo'])
                             ->with('success', 'Denunciado guardado exitosamente.');

        } catch (ValidationException $e) {
            DB::rollBack();
            Log::error('Error de validación en Denunciado: ', ['errors' => $e->errors()]);
            return back()->withErrors($e->errors())->withInput()->with('active_tab', 'denunciado');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error inesperado al guardar Denunciado: ' . $e->getMessage());
            return back()->withErrors(['general_error' => 'Ocurrió un error al guardar el Denunciado.'])
                         ->withInput()->with('active_tab', 'denunciado');
        }
    }

    /**
     * Guarda/actualiza los datos del Grupo Familiar.
     *
     * @param Request $request
     * @param int $id_adulto
     */
    public function storeGrupoFamiliar(Request $request, $id_adulto)
    {
        $rules = [
            'familiares'                      => 'nullable|array',
            'familiares.*.id_familiar'        => 'nullable|integer|exists:grupo_familiar,id_familiar', // Para identificar registros existentes
            'familiares.*.apellido_paterno'   => 'required|string|max:100',
            'familiares.*.apellido_materno'   => 'nullable|string|max:100',
            'familiares.*.nombres'            => 'required|string|max:255',
            'familiares.*.parentesco'         => 'required|string|max:100',
            'familiares.*.edad'               => 'required|integer|min:0|max:120',
            'familiares.*.ocupacion'          => 'nullable|string|max:100',
            'familiares.*.direccion'          => 'nullable|string|max:255',
            'familiares.*.telefono'           => 'nullable|string|max:20',
        ];

        try {
            $request->validate($rules, [], [
                'familiares.*.apellido_paterno' => 'Apellido Paterno del Familiar',
                'familiares.*.nombres'          => 'Nombres del Familiar',
                'familiares.*.parentesco'       => 'Parentesco del Familiar',
                'familiares.*.edad'             => 'Edad del Familiar',
            ]);

            DB::beginTransaction();

            $currentFamiliarIds = collect($request->input('familiares', []))
                                ->filter(fn($f) => isset($f['id_familiar']))
                                ->pluck('id_familiar')
                                ->toArray(); // Convertir a array para whereNotIn

            // Eliminar familiares antiguos que no están en la lista actual
            GrupoFamiliar::where('id_adulto', $id_adulto)
                         ->whereNotIn('id_familiar', $currentFamiliarIds)
                         ->delete();

            foreach ($request->input('familiares', []) as $familiarData) {
                if (isset($familiarData['id_familiar']) && $familiarData['id_familiar']) {
                    // Actualizar familiar existente
                    $familiar = GrupoFamiliar::where('id_adulto', $id_adulto)
                                             ->where('id_familiar', $familiarData['id_familiar'])
                                             ->first();
                    if ($familiar) {
                        $familiar->fill($familiarData);
                        $familiar->save();
                    }
                } else {
                    // Crear nuevo familiar
                    GrupoFamiliar::create(array_merge($familiarData, ['id_adulto' => $id_adulto]));
                }
            }

            DB::commit();
            Log::info("Datos de Grupo Familiar registrados/actualizados para el adulto ID: $id_adulto");
            return redirect()->route('legal.caso.edit', ['id_adulto' => $id_adulto, 'active_tab' => 'croquis'])
                             ->with('success', 'Grupo Familiar guardado exitosamente.');

        } catch (ValidationException $e) {
            DB::rollBack();
            Log::error('Error de validación en Grupo Familiar: ', ['errors' => $e->errors()]);
            return back()->withErrors($e->errors())->withInput()->with('active_tab', 'grupo');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error inesperado al guardar Grupo Familiar: ' . $e->getMessage());
            return back()->withErrors(['general_error' => 'Ocurrió un error al guardar el Grupo Familiar.'])
                         ->withInput()->with('active_tab', 'grupo');
        }
    }

    /**
     * Guarda/actualiza los datos del Croquis.
     *
     * @param Request $request
     * @param int $id_adulto
     */
    public function storeCroquis(Request $request, $id_adulto)
    {
        $rules = [
            'croquis.nombre_denunciante'    => 'required|string|max:255',
            'croquis.apellidos_denunciante' => 'required|string|max:255',
            'croquis.ci_denunciante'        => 'required|string|max:20',
            // Nueva regla de validación para el archivo de imagen
            'image_file'                    => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // 2MB max
        ];

        try {
            $request->validate($rules, [], [
                'croquis.nombre_denunciante'    => 'Nombre del Denunciante (Croquis)',
                'croquis.apellidos_denunciante' => 'Apellidos del Denunciante (Croquis)',
                'croquis.ci_denunciante'        => 'CI del Denunciante (Croquis)',
                'image_file'                    => 'Imagen del Croquis', // Mensaje amigable para el error
            ]);

            DB::beginTransaction();

            $croquis = Croquis::firstOrNew(['id_adulto' => $id_adulto]);
            $croquisData = $request->input('croquis');
            $croquis->fill([
                'nombre_denunciante'  => $croquisData['nombre_denunciante'] ?? null,
                'apellidos_denunciante' => $croquisData['apellidos_denunciante'] ?? null,
                'ci_denunciante'      => $croquisData['ci_denunciante'] ?? null,
            ]);

            // Manejo de la subida de la imagen
            if ($request->hasFile('image_file')) {
                // Eliminar la imagen anterior si existe
                if ($croquis->ruta_imagen) {
                    Storage::disk('public')->delete($croquis->ruta_imagen);
                    Log::info("Imagen de croquis anterior eliminada: {$croquis->ruta_imagen}");
                }

                // Guardar la nueva imagen
                $image = $request->file('image_file');
                // Generar un nombre único para el archivo
                $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                // Almacenar en la carpeta 'public/croquis_images'
                $path = $image->storeAs('croquis_images', $filename, 'public'); // 'public' disk
                $croquis->ruta_imagen = $path; // Guardar la ruta relativa en la base de datos
                Log::info("Nueva imagen de croquis guardada: {$path}");
            } elseif ($request->input('remove_image')) {
                // Si el checkbox de 'eliminar imagen' está marcado
                if ($croquis->ruta_imagen) {
                    Storage::disk('public')->delete($croquis->ruta_imagen);
                    Log::info("Imagen de croquis eliminada por solicitud: {$croquis->ruta_imagen}");
                    $croquis->ruta_imagen = null; // Eliminar la referencia en la base de datos
                }
            }
            // Si no se envía un archivo nuevo y no se marca 'remove_image',
            // la ruta_imagen existente se mantiene.


            $croquis->save();

            DB::commit();
            Log::info("Datos de Croquis registrados/actualizados para el adulto ID: $id_adulto");
            return redirect()->route('legal.caso.edit', ['id_adulto' => $id_adulto, 'active_tab' => 'seguimiento'])
                             ->with('success', 'Croquis guardado exitosamente.');

        } catch (ValidationException $e) {
            DB::rollBack();
            Log::error('Error de validación en Croquis: ', ['errors' => $e->errors()]);
            return back()->withErrors($e->errors())->withInput()->with('active_tab', 'croquis');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error inesperado al guardar Croquis: ' . $e->getMessage());
            return back()->withErrors(['general_error' => 'Ocurrió un error al guardar el Croquis.'])
                         ->withInput()->with('active_tab', 'croquis');
        }
    }

    /**
     * Guarda/actualiza los datos del Seguimiento del Caso.
     *
     * @param Request $request
     * @param int $id_adulto
     */
    public function storeSeguimiento(Request $request, $id_adulto)
    {
        $rules = [
            'seguimientos'                  => 'required|array', // El array de seguimientos es obligatorio
            'seguimientos.*.id_seg'         => 'nullable|integer|exists:seguimiento_caso,id_seg', // Para identificar registros existentes
            'seguimientos.*.nro'            => 'required|string|max:20',
            'seguimientos.*.fecha'          => 'required|date',
            'seguimientos.*.accion_realizada' => 'required|string|max:1000',
            'seguimientos.*.resultado_obtenido' => 'required|string|max:1000',
        ];

        try {
            $request->validate($rules, [], [
                'seguimientos.*.nro'                => 'Nro Seguimiento',
                'seguimientos.*.fecha'              => 'Fecha Seguimiento',
                'seguimientos.*.accion_realizada'   => 'Acción Realizada',
                'seguimientos.*.resultado_obtenido' => 'Resultado Obtenido',
            ]);

            DB::beginTransaction();

            $currentSeguimientoIds = collect($request->input('seguimientos', []))
                                    ->filter(fn($s) => isset($s['id_seg']))
                                    ->pluck('id_seg')
                                    ->toArray();

            SeguimientoCaso::where('id_adulto', $id_adulto)
                           ->whereNotIn('id_seg', $currentSeguimientoIds)
                           ->delete();

            foreach ($request->input('seguimientos', []) as $seguimientoData) {
                if (isset($seguimientoData['id_seg']) && $seguimientoData['id_seg']) {
                    // Actualizar seguimiento existente
                    $seguimiento = SeguimientoCaso::where('id_adulto', $id_adulto)
                                                   ->where('id_seg', $seguimientoData['id_seg'])
                                                   ->first();
                    if ($seguimiento) {
                        $seguimiento->fill($seguimientoData);
                        $seguimiento->id_usuario = Auth::id(); // Asegura que id_usuario se asigna
                        $seguimiento->save();
                    }
                } else {
                    // Crear nuevo seguimiento
                    SeguimientoCaso::create(array_merge($seguimientoData, [
                        'id_adulto'  => $id_adulto,
                        'id_usuario' => Auth::id() // Asigna el ID del usuario autenticado
                    ]));
                }
            }

            DB::commit();
            Log::info("Datos de Seguimiento de Caso registrados/actualizados para el adulto ID: $id_adulto");
            return redirect()->route('legal.caso.edit', ['id_adulto' => $id_adulto, 'active_tab' => 'intervencion'])
                             ->with('success', 'Seguimiento de Caso guardado exitosamente.');

        } catch (ValidationException $e) {
            DB::rollBack();
            Log::error('Error de validación en Seguimiento de Caso: ', ['errors' => $e->errors()]);
            return back()->withErrors($e->errors())->withInput()->with('active_tab', 'seguimiento');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error inesperado al guardar Seguimiento de Caso: ' . $e->getMessage());
            return back()->withErrors(['general_error' => 'Ocurrió un error al guardar el Seguimiento de Caso.'])
                         ->withInput()->with('active_tab', 'seguimiento');
        }
    }

    /**
     * Guarda/actualiza los datos de Intervención.
     *
     * @param Request $request
     * @param int $id_adulto
     */
    public function storeIntervencion(Request $request, $id_adulto)
    {
        $rules = [
            'intervencion.resuelto_descripcion'      => 'nullable|string|max:1000',
            'intervencion.no_resultado'              => 'nullable|string|max:255',
            'intervencion.derivacion_institucion'    => 'nullable|string|max:255',
            'intervencion.der_seguimiento_legal'     => 'nullable|string|max:255',
            'intervencion.der_seguimiento_psi'       => 'nullable|string|max:255',
            'intervencion.der_resuelto_externo'      => 'nullable|string|max:255',
            'intervencion.der_noresuelto_externo'    => 'nullable|string|max:255',
            'intervencion.abandono_victima'          => 'nullable|string|max:255',
            'intervencion.resuelto_conciliacion_jio' => 'nullable|string|max:255',
            'intervencion.fecha_intervencion'        => 'required|date',
        ];

        try {
            $request->validate($rules, [], [
                'intervencion.resuelto_descripcion'      => 'Descripción de Resuelto',
                'intervencion.no_resultado'              => 'No Resultado (Motivo)',
                'intervencion.derivacion_institucion'    => 'Derivado a otra institución (Motivo)',
                'intervencion.der_seguimiento_legal'     => 'Derivado y en seguimiento legal',
                'intervencion.der_seguimiento_psi'       => 'Derivado y en seguimiento psicológico',
                'intervencion.der_resuelto_externo'      => 'Derivado y resuelto en otra institución',
                'intervencion.der_noresuelto_externo'    => 'Derivado a otra institución y no resuelto',
                'intervencion.abandono_victima'          => 'Abandono por la víctima',
                'intervencion.resuelto_conciliacion_jio' => 'Resuelto mediante conciliación según Justicia Indígena Originaria',
                'intervencion.fecha_intervencion'        => 'Fecha de Intervención',
            ]);

            DB::beginTransaction();

            // *** MODIFICACIÓN CLAVE AQUÍ ***
            // Ordena explícitamente por 'id_seg' en orden descendente para asegurar el ID más alto.
            $latestSeguimiento = SeguimientoCaso::where('id_adulto', $id_adulto)
                                                ->orderBy('id_seg', 'desc') // <-- Cambio
                                                ->first();

            if (!$latestSeguimiento) {
                throw new \Exception('No se puede registrar una Intervención sin un Seguimiento de Caso previo.');
            }

            $intervencionData = $request->input('intervencion');
            // Busca o crea la intervención asociada a este id_seg específico
            $intervencion = Intervencion::firstOrNew(['id_seg' => $latestSeguimiento->id_seg]);
            $intervencion->fill($intervencionData);
            $intervencion->id_seg = $latestSeguimiento->id_seg; // Asegura que el FK está correctamente vinculado
            $intervencion->save();

            DB::commit();
            Log::info("Datos de Intervención registrados/actualizados para el adulto ID: $id_adulto (vinculado a SeguimientoCaso ID: {$latestSeguimiento->id_seg})");
            return redirect()->route('legal.caso.edit', ['id_adulto' => $id_adulto, 'active_tab' => 'anexo3'])
                             ->with('success', 'Intervención guardada exitosamente.');

        } catch (ValidationException $e) {
            DB::rollBack();
            Log::error('Error de validación en Intervención: ', ['errors' => $e->errors()]);
            return back()->withErrors($e->errors())->withInput()->with('active_tab', 'intervencion');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error inesperado al guardar Intervención: ' . $e->getMessage());
            return back()->withErrors(['general_error' => 'Ocurrió un error al guardar la Intervención. Detalles: '.$e->getMessage()])
                             ->withInput()->with('active_tab', 'intervencion');
        }
    }

    /**
     * Guarda/actualiza los datos del Anexo N3.
     *
     * @param Request $request
     * @param int $id_adulto
     */
    public function storeAnexoN3(Request $request, $id_adulto)
    {
        $rules = [
            'anexos_n3'                      => 'nullable|array',
            'anexos_n3.*.primer_apellido'    => 'required|string|max:100',
            'anexos_n3.*.segundo_apellido'   => 'nullable|string|max:100',
            'anexos_n3.*.nombres'            => 'required|string|max:255',
            'anexos_n3.*.sexo'               => 'required|in:M,F',
            'anexos_n3.*.edad'               => 'required|integer|min:1|max:120',
            'anexos_n3.*.ci'                 => 'required|string|max:20',
            'anexos_n3.*.telefono'           => 'nullable|string|max:20',
            'anexos_n3.*.direccion_domicilio' => 'nullable|string|max:255',
            'anexos_n3.*.relacion_parentesco' => 'nullable|string|max:100',
            'anexos_n3.*.direccion_de_trabajo' => 'nullable|string|max:255',
            'anexos_n3.*.ocupacion'          => 'nullable|string|max:100',
        ];

        try {
            $request->validate($rules, [], [
                'anexos_n3.*.primer_apellido' => 'Primer Apellido de Persona Anexo N3',
                'anexos_n3.*.nombres'         => 'Nombres de Persona Anexo N3',
                'anexos_n3.*.sexo'            => 'Sexo de Persona Anexo N3',
                'anexos_n3.*.edad'            => 'Edad de Persona Anexo N3',
                'anexos_n3.*.ci'              => 'CI de Persona Anexo N3',
            ]);

            DB::beginTransaction();

            // Validar duplicidad de CIs en Anexo N3 y con Encargado
            $encargado = Encargado::where('id_adulto', $id_adulto)->where('tipo_encargado', 'natural')->first();
            $encargadoCI = $encargado && $encargado->personaNatural ? $encargado->personaNatural->ci : null;

            $existingAnexoN3CIs = AnexoN3::where('id_adulto', $id_adulto)
                ->join('persona_natural', 'anexo_n3.id_natural', '=', 'persona_natural.id_natural')
                ->pluck('persona_natural.ci')
                ->toArray();

            // foreach ($request->input('anexos_n3', []) as $index => $anexo3Data) {
            //     $ci = $anexo3Data['ci'];
            //     // Verificar si el CI coincide con el del Encargado
            //     if ($encargadoCI && $ci === $encargadoCI) {
            //         throw ValidationException::withMessages([
            //             "anexos_n3.$index.ci" => ['El CI ya está registrado en Encargado (Persona Natural) para este caso.']
            //         ]);
            //     }
            //     // Verificar duplicidad dentro de los Anexos N3 enviados
            //     $ciCount = collect($request->input('anexos_n3'))->pluck('ci')->filter(fn($c) => $c === $ci)->count();
            //     if ($ciCount > 1) {
            //         throw ValidationException::withMessages([
            //             "anexos_n3.$index.ci" => ['El CI está duplicado dentro de los Anexos N3 enviados.']
            //         ]);
            //     }
            // }

            // Eliminar Anexos N3 existentes
            AnexoN3::where('id_adulto', $id_adulto)->delete();

            foreach ($request->input('anexos_n3', []) as $anexo3Data) {
                $personaNaturalData = $anexo3Data;
                $personaNaturalData['id_encargado'] = null;
                $personaNatural = PersonaNatural::firstOrNew(['ci' => $personaNaturalData['ci']]);
                $personaNatural->fill($personaNaturalData);
                $personaNatural->save();

                AnexoN3::create([
                    'id_natural' => $personaNatural->id_natural,
                    'id_adulto'  => $id_adulto,
                    'sexo'       => $anexo3Data['sexo'],
                ]);
            }

            DB::commit();
            Log::info("Datos de Anexo N3 procesados para el adulto ID: $id_adulto");
            return redirect()->route('legal.caso.edit', ['id_adulto' => $id_adulto, 'active_tab' => 'anexo5'])
                             ->with('success', 'Anexo N3 guardado exitosamente.');

        } catch (ValidationException $e) {
            DB::rollBack();
            Log::error('Error de validación en Anexo N3: ', ['errors' => $e->errors()]);
            return back()->withErrors($e->errors())->withInput()->with('active_tab', 'anexo3');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error inesperado al guardar Anexo N3: ' . $e->getMessage());
            return back()->withErrors(['general_error' => 'Ocurrió un error al guardar el Anexo N3.'])
                         ->withInput()->with('active_tab', 'anexo3');
        }
    }

    /**
     * Guarda/actualiza los datos del Anexo N5.
     *
     * @param Request $request
     * @param int $id_adulto
     */
    public function storeAnexoN5(Request $request, $id_adulto)
    {
        $rules = [
            'anexos_n5'                     => 'nullable|array',
            'anexos_n5.*.nro_an5'           => 'nullable|integer', // Para identificar registros existentes (si se envía)
            'anexos_n5.*.numero'            => 'required|string|max:255',
            'anexos_n5.*.fecha'             => 'required|date',
            'anexos_n5.*.accion_realizada'  => 'required|string|max:1000',
            'anexos_n5.*.resultado_obtenido' => 'nullable|string|max:1000',
        ];

        try {
            $request->validate($rules, [], [
                'anexos_n5.*.numero'            => 'Número de Anexo N5',
                'anexos_n5.*.fecha'             => 'Fecha de Anexo N5',
                'anexos_n5.*.accion_realizada'  => 'Acción Realizada (Anexo N5)',
                'anexos_n5.*.resultado_obtenido' => 'Resultado Obtenido (Anexo N5)',
            ]);

            DB::beginTransaction();

            $currentAnexo5NroAn5s = collect($request->input('anexos_n5', []))
                                    ->filter(fn($an5) => isset($an5['nro_an5']) && $an5['nro_an5'])
                                    ->pluck('nro_an5')
                                    ->toArray();

            // Eliminar Anexos N5 antiguos que no están en la lista actual
            AnexoN5::where('id_adulto', $id_adulto)
                   ->whereNotIn('nro_an5', $currentAnexo5NroAn5s)
                   ->delete();

            foreach ($request->input('anexos_n5', []) as $anexo5Data) {
                if (isset($anexo5Data['nro_an5']) && $anexo5Data['nro_an5']) {
                    // Actualizar Anexo N5 existente
                    $anexo5 = AnexoN5::where('id_adulto', $id_adulto)
                                     ->where('nro_an5', $anexo5Data['nro_an5'])
                                     ->first();
                    if ($anexo5) {
                        $anexo5->fill($anexo5Data);
                        $anexo5->id_usuario = Auth::id(); // Asigna el ID del usuario autenticado
                        $anexo5->id_adulto = $id_adulto; // Asegura la vinculación con el adulto
                        $anexo5->save();
                    }
                } else {
                    // Crear nuevo Anexo N5
                    AnexoN5::create(array_merge($anexo5Data, [
                        'id_adulto'  => $id_adulto,
                        'id_usuario' => Auth::id() // Asigna el ID del usuario autenticado
                    ]));
                }
            }

            DB::commit();
            Log::info("Datos de Anexo N5 registrados/actualizados para el adulto ID: $id_adulto");
            // Redirige al índice después de guardar el último anexo
            return redirect()->route('legal.caso.index')->with('success', 'Datos del caso (incluyendo Anexo N5) registrados exitosamente.');

        } catch (ValidationException $e) {
            DB::rollBack();
            Log::error('Error de validación en Anexo N5: ', ['errors' => $e->errors()]);
            return back()->withErrors($e->errors())->withInput()->with('active_tab', 'anexo5');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error inesperado al guardar Anexo N5: ' . $e->getMessage());
            return back()->withErrors(['general_error' => 'Ocurrió un error al guardar los datos del Anexo N5. Detalles: '.$e->getMessage()])
                         ->withInput()->with('active_tab', 'anexo5');
        }
    }
    //FUNCION PARA ELIMINAR EL REGISTRO DE CASO DE UN ADULTO MAYOR
    public function destroy($id_adulto)
    {
        DB::beginTransaction(); // Inicia una transacción de base de datos
        try {
            // Cargar el AdultoMayor con las relaciones necesarias para la eliminación
            $adulto = AdultoMayor::with([
                'actividadLaboral', 
                'encargados.personaNatural', // Cargar PersonaNatural para encargado si es tipo natural
                'encargados.personaJuridica', // Cargar PersonaJuridica para encargado si es tipo juridica
                'denunciado.personaNatural', // Cargar PersonaNatural para denunciado
                'croquis',
            ])->findOrFail($id_adulto);
            
            // Coleccionar IDs de PersonaNatural que *podrían* quedar huérfanas
            $potential_orphan_persona_natural_ids = [];

            // 1. Eliminar Actividad Laboral (hasOne)
            if ($adulto->actividadLaboral) {
                $adulto->actividadLaboral->delete();
                Log::info("Actividad Laboral eliminada para Adulto Mayor ID: {$id_adulto}");
            }

            // 2. Eliminar Encargado (hasOne)
            if ($adulto->encargados) {
                if ($adulto->encargados->tipo_encargado === 'natural' && $adulto->encargados->personaNatural) {
                    $potential_orphan_persona_natural_ids[] = $adulto->encargados->personaNatural->id_natural;
                    Log::info("ID de PersonaNatural de Encargado (natural) añadido a potenciales huérfanos: {$adulto->encargados->personaNatural->id_natural}");
                } elseif ($adulto->encargados->tipo_encargado === 'juridica' && $adulto->encargados->personaJuridica) {
                    $adulto->encargados->personaJuridica->delete(); 
                    Log::info("Persona Jurídica eliminada para Encargado de Adulto Mayor ID: {$id_adulto}");
                }
                $adulto->encargados->delete(); 
                Log::info("Encargado eliminado para Adulto Mayor ID: {$id_adulto}");
            }

            // 3. Eliminar Denunciado (hasOne)
            if ($adulto->denunciado) {
                if ($adulto->denunciado->personaNatural) {
                    $potential_orphan_persona_natural_ids[] = $adulto->denunciado->personaNatural->id_natural;
                    Log::info("ID de PersonaNatural de Denunciado añadido a potenciales huérfanos: {$adulto->denunciado->personaNatural->id_natural}");
                }
                $adulto->denunciado->delete(); 
                Log::info("Denunciado eliminado para Adulto Mayor ID: {$id_adulto}");
            }

            // 4. Eliminar Grupo Familiar (hasMany)
            // Ya que 'grupo_familiar' no tiene relación directa con 'persona_natural',
            // no hay IDs de persona natural que recolectar de esta tabla para limpieza.
            GrupoFamiliar::where('id_adulto', $id_adulto)->delete();
            Log::info("Todos los miembros del Grupo Familiar eliminados para Adulto Mayor ID: {$id_adulto}");

            // 5. Eliminar Croquis y su imagen asociada (hasOne)
            if ($adulto->croquis) {
                if ($adulto->croquis->ruta_imagen) {
                    Storage::disk('public')->delete($adulto->croquis->ruta_imagen);
                    Log::info("Imagen de croquis eliminada: {$adulto->croquis->ruta_imagen}");
                }
                $adulto->croquis->delete();
                Log::info("Croquis eliminado para Adulto Mayor ID: {$id_adulto}");
            }

            // 6. Eliminar Seguimientos e Intervenciones (seguimientos es hasMany)
            $seguimientoIds = SeguimientoCaso::where('id_adulto', $id_adulto)->pluck('id_seg')->toArray();
            if (!empty($seguimientoIds)) {
                Intervencion::whereIn('id_seg', $seguimientoIds)->delete();
                Log::info("Todas las Intervenciones eliminadas para Seguimientos de Adulto Mayor ID: {$id_adulto}");
            }
            SeguimientoCaso::where('id_adulto', $id_adulto)->delete();
            Log::info("Todos los Seguimientos eliminados para Adulto Mayor ID: {$id_adulto}");

            // 7. Eliminar Anexo N3 (hasMany)
            // Asumiendo que la tabla 'anexo_n3' SÍ tiene una columna 'id_natural'
            // que apunta a la tabla 'persona_natural', ya que la relación está en PersonaNatural.php
            $anexo3NaturalIds = AnexoN3::where('id_adulto', $id_adulto)
                                       ->pluck('id_natural')
                                       ->toArray();
            $potential_orphan_persona_natural_ids = array_merge($potential_orphan_persona_natural_ids, $anexo3NaturalIds);
            AnexoN3::where('id_adulto', $id_adulto)->delete();
            Log::info("Todos los Anexos N3 eliminados para Adulto Mayor ID: {$id_adulto}");

            // 8. Eliminar Anexo N5 (hasMany)
            AnexoN5::where('id_adulto', $id_adulto)->delete();
            Log::info("Todos los Anexos N5 eliminados para Adulto Mayor ID: {$id_adulto}");

            // --- LÓGICA DE LIMPIEZA DE PERSONA NATURAL HUÉRFANA ---
            $unique_potential_orphan_persona_natural_ids = array_unique($potential_orphan_persona_natural_ids);

            foreach ($unique_potential_orphan_persona_natural_ids as $natural_id) {
                $personaNatural = PersonaNatural::find($natural_id);
                if ($personaNatural) {
                    if ($personaNatural->isOrphan()) {
                        $personaNatural->delete();
                        Log::info("PersonaNatural ID: {$natural_id} eliminada por ser huérfana.");
                    } else {
                        Log::info("PersonaNatural ID: {$natural_id} conservada: aún tiene referencias en otros módulos.");
                    }
                }
            }
            // --- FIN LÓGICA DE LIMPIEZA ---

            DB::commit();
            Log::info("Todos los datos del módulo de protección para el Adulto Mayor ID: $id_adulto han sido eliminados exitosamente. El registro del Adulto Mayor y la Persona asociada se han mantenido intactos.");
            return redirect()->route('legal.reportes_proteccion.index')->with('success', 'Módulo de protección del caso eliminado exitosamente. El registro del Adulto Mayor y la Persona han sido conservados.');

        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            Log::error("Adulto Mayor no encontrado para eliminar módulo de protección con ID: {$id_adulto}. Error: " . $e->getMessage());
            return redirect()->route('legal.reportes_proteccion.index')->with('error', 'El caso que intentas eliminar no existe.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al eliminar el módulo de protección del caso: ' . $e->getMessage(), ['id_adulto' => $id_adulto, 'trace' => $e->getTraceAsString()]);
            return back()->with('error', 'Ocurrió un error al eliminar el módulo de protección del caso: ' . $e->getMessage());
        }
    }
    
    // public function destroy($id_adulto)
    //     {
    //         DB::beginTransaction();
    //         try {
    //             $adulto = AdultoMayor::with([
    //                 'actividadLaboral', 
    //                 'encargados.personaNatural', 
    //                 'encargados.personaJuridica',
    //                 'denunciado.personaNatural', 
    //                 'croquis',
    //             ])->findOrFail($id_adulto);
                
    //             // 1. Eliminar Actividad Laboral (hasOne)
    //             if ($adulto->actividadLaboral) {
    //                 $adulto->actividadLaboral->delete();
    //                 Log::info("Actividad Laboral eliminada para Adulto Mayor ID: {$id_adulto}");
    //             }

    //             // 2. Eliminar Encargado (hasOne)
    //             // NO eliminamos PersonaNatural aquí; lo haremos en la lógica de limpieza de huérfanos al final.
    //             if ($adulto->encargados) {
    //                 if ($adulto->encargados->tipo_encargado === 'juridica' && $adulto->encargados->personaJuridica) {
    //                     $adulto->encargados->personaJuridica->delete(); // Elimina PersonaJuridica si no hay cascada
    //                 }
    //                 $adulto->encargados->delete(); 
    //                 Log::info("Encargado eliminado para Adulto Mayor ID: {$id_adulto}");
    //             }

    //             // 3. Eliminar Denunciado (hasOne)
    //             // NO eliminamos PersonaNatural aquí; lo haremos en la lógica de limpieza de huérfanos al final.
    //             if ($adulto->denunciado) {
    //                 $adulto->denunciado->delete();
    //                 Log::info("Denunciado eliminado para Adulto Mayor ID: {$id_adulto}");
    //             }

    //             // 4. Eliminar Grupo Familiar (hasMany)
    //             GrupoFamiliar::where('id_adulto', $id_adulto)->delete();
    //             Log::info("Todos los miembros del Grupo Familiar eliminados para Adulto Mayor ID: {$id_adulto}");

    //             // 5. Eliminar Croquis y su imagen asociada (hasOne)
    //             if ($adulto->croquis) {
    //                 if ($adulto->croquis->ruta_imagen) {
    //                     Storage::disk('public')->delete($adulto->croquis->ruta_imagen);
    //                     Log::info("Imagen de croquis eliminada: {$adulto->croquis->ruta_imagen}");
    //                 }
    //                 $adulto->croquis->delete();
    //                 Log::info("Croquis eliminado para Adulto Mayor ID: {$id_adulto}");
    //             }

    //             // 6. Eliminar Seguimientos e Intervenciones (seguimientos es hasMany)
    //             $seguimientoIds = SeguimientoCaso::where('id_adulto', $id_adulto)->pluck('id_seg')->toArray();
    //             if (!empty($seguimientoIds)) {
    //                 Intervencion::whereIn('id_seg', $seguimientoIds)->delete();
    //                 Log::info("Todas las Intervenciones eliminadas para Seguimientos de Adulto Mayor ID: {$id_adulto}");
    //             }
    //             SeguimientoCaso::where('id_adulto', $id_adulto)->delete();
    //             Log::info("Todos los Seguimientos eliminados para Adulto Mayor ID: {$id_adulto}");

    //             // 7. Eliminar Anexo N3 y sus PersonaNatural asociadas (si son huérfanas)
    //             $anexo3_naturals_to_delete_ids = AnexoN3::where('id_adulto', $id_adulto)
    //                                                     ->pluck('id_natural')
    //                                                     ->toArray();

    //             AnexoN3::where('id_adulto', $id_adulto)->delete();
    //             Log::info("Todos los Anexos N3 eliminados para Adulto Mayor ID: {$id_adulto}");

    //             // Lógica de limpieza de PersonaNatural huérfanas
    //             foreach ($anexo3_naturals_to_delete_ids as $natural_id) {
    //                 $personaNatural = PersonaNatural::find($natural_id);
    //                 if ($personaNatural) {
    //                     // Verificar si esta PersonaNatural está referenciada por alguna otra entidad
    //                     // que NO sea el AnexoN3 recién eliminado para ESTE AdultoMayor.
    //                     // (Necesitamos asegurar que PersonaNatural tiene las relaciones inversas:
    //                     // 'denunciado', 'encargado', 'anexosN3')

    //                     $isReferencedByOtherAnexoN3 = $personaNatural->anexosN3()->where('id_adulto', '!=', $id_adulto)->exists();
    //                     $isReferencedByDenunciado = $personaNatural->denunciado()->exists();
    //                     // Para Encargado, PersonaNatural tiene id_encargado. Si id_encargado es not null, significa que es un encargado.
    //                     $isReferencedByEncargado = !is_null($personaNatural->id_encargado);
                        
    //                     // Si no está referenciada por NINGUNA de estas, la eliminamos.
    //                     if (!$isReferencedByOtherAnexoN3 && !$isReferencedByDenunciado && !$isReferencedByEncargado) {
    //                         $personaNatural->delete();
    //                         Log::info("PersonaNatural ID: {$natural_id} eliminada al no estar referenciada por otros módulos o AnexosN3 de otros adultos.");
    //                     } else {
    //                         Log::info("PersonaNatural ID: {$natural_id} no eliminada: aún está referenciada por otra entidad o AnexoN3 de otro adulto.");
    //                     }
    //                 }
    //             }

    //             // 8. Eliminar Anexo N5 (hasMany)
    //             AnexoN5::where('id_adulto', $id_adulto)->delete();
    //             Log::info("Todos los Anexos N5 eliminados para Adulto Mayor ID: {$id_adulto}");

    //             DB::commit();
    //             Log::info("Todos los datos del módulo de protección para el Adulto Mayor ID: $id_adulto han sido eliminados exitosamente. El registro del Adulto Mayor y la Persona asociada se han mantenido intactos.");
    //             return redirect()->route('legal.reportes_proteccion.index')->with('success', 'Módulo de protección del caso eliminado exitosamente. El registro del Adulto Mayor y la Persona han sido conservados.');

    //         } catch (\Exception $e) {
    //             DB::rollBack();
    //             Log::error('Error al eliminar el módulo de protección del caso: ' . $e->getMessage(), ['id_adulto' => $id_adulto, 'trace' => $e->getTraceAsString()]);
    //             return back()->with('error', 'Ocurrió un error al eliminar el módulo de protección del caso: ' . $e->getMessage());
    //         }
    //     }
    // public function destroy($id_adulto)
    // {
    //     DB::beginTransaction(); // Inicia una transacción de base de datos
    //     try {
    //         // Cargar el AdultoMayor con las relaciones que son hasOne
    //         // Las relaciones hasMany se eliminarán directamente con where()->delete()
    //         $adulto = AdultoMayor::with([
    //             'actividadLaboral', 
    //             'encargados', 
    //             'denunciado', 
    //             'croquis'
    //         ])->findOrFail($id_adulto);
            
    //         // 1. Eliminar Actividad Laboral (hasOne)
    //         if ($adulto->actividadLaboral) {
    //             $adulto->actividadLaboral->delete();
    //             Log::info("Actividad Laboral eliminada para Adulto Mayor ID: {$id_adulto}");
    //         }

    //         // 2. Eliminar Encargado (hasOne)
    //         if ($adulto->encargados) {
    //             // Si Encargado tiene personaNatural o personaJuridica y no hay cascada en DB
    //             // (La clave foránea de Encargado a PersonaNatural/Juridica debería tener onDelete('cascade') para esto)
    //             if ($adulto->encargados->tipo_encargado === 'natural' && $adulto->encargados->personaNatural) {
    //                 $adulto->encargados->personaNatural->delete();
    //             } elseif ($adulto->encargados->tipo_encargado === 'juridica' && $adulto->encargados->personaJuridica) {
    //                 $adulto->encargados->personaJuridica->delete();
    //             }
    //             $adulto->encargados->delete(); 
    //             Log::info("Encargado eliminado para Adulto Mayor ID: {$id_adulto}");
    //         }

    //         // 3. Eliminar Denunciado (hasOne)
    //         if ($adulto->denunciado) {
    //             // Si Denunciado tiene personaNatural y no hay cascada en DB
    //             // (La clave foránea de Denunciado a PersonaNatural debería tener onDelete('cascade') para esto)
    //             if ($adulto->denunciado->personaNatural) {
    //                 $adulto->denunciado->personaNatural->delete();
    //             }
    //             $adulto->denunciado->delete();
    //             Log::info("Denunciado eliminado para Adulto Mayor ID: {$id_adulto}");
    //         }

    //         // 4. Eliminar Grupo Familiar (hasMany)
    //         // (La clave foránea de GrupoFamiliar a AdultoMayor debería tener onDelete('cascade'))
    //         GrupoFamiliar::where('id_adulto', $id_adulto)->delete();
    //         Log::info("Todos los miembros del Grupo Familiar eliminados para Adulto Mayor ID: {$id_adulto}");

    //         // 5. Eliminar Croquis y su imagen asociada (hasOne)
    //         if ($adulto->croquis) {
    //             if ($adulto->croquis->ruta_imagen) {
    //                 Storage::disk('public')->delete($adulto->croquis->ruta_imagen);
    //                 Log::info("Imagen de croquis eliminada: {$adulto->croquis->ruta_imagen}");
    //             }
    //             $adulto->croquis->delete();
    //             Log::info("Croquis eliminado para Adulto Mayor ID: {$id_adulto}");
    //         }

    //         // 6. Eliminar Seguimientos e Intervenciones (seguimientos es hasMany)
    //         // Primero, eliminamos las intervenciones asociadas a los seguimientos.
    //         $seguimientoIds = SeguimientoCaso::where('id_adulto', $id_adulto)->pluck('id_seg')->toArray();
    //         if (!empty($seguimientoIds)) {
    //             // (La clave foránea de Intervencion a SeguimientoCaso debería tener onDelete('cascade'))
    //             Intervencion::whereIn('id_seg', $seguimientoIds)->delete();
    //             Log::info("Todas las Intervenciones eliminadas para Seguimientos de Adulto Mayor ID: {$id_adulto}");
    //         }
    //         // Ahora eliminamos los Seguimientos
    //         // (La clave foránea de SeguimientoCaso a AdultoMayor debería tener onDelete('cascade'))
    //         SeguimientoCaso::where('id_adulto', $id_adulto)->delete();
    //         Log::info("Todos los Seguimientos eliminados para Adulto Mayor ID: {$id_adulto}");

    //         // 7. Eliminar Anexo N3 (hasMany)
    //         // Con la migración corregida, cuando AnexoN3 se elimina, su PersonaNatural asociada se debería ir por cascada.
    //         AnexoN3::where('id_adulto', $id_adulto)->delete();
    //         Log::info("Todos los Anexos N3 eliminados para Adulto Mayor ID: {$id_adulto}");

    //         // 8. Eliminar Anexo N5 (hasMany)
    //         // (La clave foránea de AnexoN5 a AdultoMayor debería tener onDelete('cascade'))
    //         AnexoN5::where('id_adulto', $id_adulto)->delete();
    //         Log::info("Todos los Anexos N5 eliminados para Adulto Mayor ID: {$id_adulto}");

    //         DB::commit();
    //         Log::info("Todos los datos del módulo de protección para el Adulto Mayor ID: $id_adulto han sido eliminados exitosamente. El registro del Adulto Mayor y la Persona asociada se han mantenido intactos.");
    //         return redirect()->route('legal.reportes_proteccion.index')->with('success', 'Módulo de protección del caso eliminado exitosamente. El registro del Adulto Mayor y la Persona han sido conservados.');

    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         Log::error('Error al eliminar el módulo de protección del caso: ' . $e->getMessage(), ['id_adulto' => $id_adulto, 'trace' => $e->getTraceAsString()]);
    //         return back()->with('error', 'Ocurrió un error al eliminar el módulo de protección del caso: ' . $e->getMessage());
    //     }
    // }
}