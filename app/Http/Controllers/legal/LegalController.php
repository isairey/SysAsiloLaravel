<?php

namespace App\Http\Controllers\legal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\Persona;
use App\Models\AdultoMayor;
use Carbon\Carbon;

use App\Models\SeguimientoCaso; // Asegúrate de que este sea el modelo correcto para casos de protección
use App\Models\Orientacion;     

class LegalController extends Controller
{
    /**
     * Muestra el dashboard principal del área Legal.
     */
    public function dashboard()
    {
        // Conteo de pacientes
        $totalPacientes = AdultoMayor::count();

        // Conteo de casos de protección (contando casos únicos por adulto_mayor_id para evitar duplicados si hay múltiples seguimientos)
        $casosProteccion = SeguimientoCaso::distinct('id_adulto')->count();

        // Conteo de fichas de orientación
        $fichasOrientacion = Orientacion::count();

        // Pasar los datos a la vista
        return view('pages.legal.dashboard', [
            'totalPacientes' => $totalPacientes,
            'casosProteccion' => $casosProteccion,
            'fichasOrientacion' => $fichasOrientacion,
        ]);
    }

    //======================================================================
    // MÉTODOS PARA GESTIÓN DE ADULTO MAYOR (LÓGICA REPLICADA DE ADMIN)
    //======================================================================

    /**
     * Muestra la lista de adultos mayores.
     */
    public function adultoMayorIndex()
    {
        try {
            $adultosMayores = AdultoMayor::with('persona')
                ->join('persona', 'adulto_mayor.ci', '=', 'persona.ci')
                ->select('adulto_mayor.*', 'persona.*', 'adulto_mayor.fecha as fecha_registro')
                ->orderBy('persona.primer_apellido', 'asc')
                ->orderBy('persona.nombres', 'asc')
                ->paginate(10);

            // !! IMPORTANTE: Asegúrate de crear esta vista en:
            // resources/views/pages/legal/adultomayor/index.blade.php
            return view('pages.legal.adultomayor.index', compact('adultosMayores'));

        } catch (\Exception $e) {
            Log::error('Error al cargar listado de adultos mayores para rol Legal: ' . $e->getMessage());
            return redirect()->route('legal.dashboard')
                             ->with('error', 'Error al cargar el listado de adultos mayores.');
        }
    }

    /**
     * Muestra el formulario para registrar un nuevo adulto mayor.
     */
    public function adultoMayorCreate()
    {
        // !! IMPORTANTE: Asegúrate de crear esta vista en:
        // resources/views/pages/legal/adultomayor/create.blade.php
        return view('pages.legal.adultomayor.create');
    }


    /**
     * Almacena un nuevo adulto mayor en la base de datos.
     */
    public function adultoMayorStore(Request $request)
    {
        Log::info('Iniciando registro de adulto mayor (Rol: Legal)', ['data' => $request->all()]);

        $validator = Validator::make($request->all(), [
            'nombres' => 'required|string|max:255',
            'primer_apellido' => 'required|string|max:255',
            'segundo_apellido' => 'nullable|string|max:255',
            'ci' => 'required|string|max:20|unique:persona,ci',
            'fecha_nacimiento' => 'required|date|before_or_equal:today',
            'sexo' => 'required|string|in:F,M,O',
            'estado_civil' => 'required|string|in:casado,divorciado,soltero,otro',
            'domicilio' => 'required|string|max:255',
            'telefono' => 'required|string|max:20',
            'zona_comunidad' => 'nullable|string|max:100',
            'discapacidad' => 'nullable|string',
            'vive_con' => 'nullable|string|max:200',
            'migrante' => 'nullable|in:0,1',
            'nro_caso' => 'nullable|string|max:50|unique:adulto_mayor,nro_caso',
            'fecha' => 'required|date',
        ], [
            'ci.unique' => 'Este CI ya ha sido registrado.',
            'nro_caso.unique' => 'Este número de caso ya ha sido registrado.',
            // ... (otros mensajes de error que tenías)
        ]);

        if ($validator->fails()) {
            Log::warning('Validación falló para registro de adulto mayor (Rol: Legal)', ['errors' => $validator->errors()]);
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $edad = Carbon::parse($request->fecha_nacimiento)->age;

            $persona = Persona::create([
                'ci' => $request->ci,
                'primer_apellido' => $request->primer_apellido,
                'segundo_apellido' => $request->segundo_apellido,
                'nombres' => $request->nombres,
                'sexo' => $request->sexo,
                'fecha_nacimiento' => $request->fecha_nacimiento,
                'edad' => $edad,
                'estado_civil' => $request->estado_civil,
                'domicilio' => $request->domicilio,
                'telefono' => $request->telefono,
                'zona_comunidad' => $request->zona_comunidad,
            ]);
            Log::info('Persona creada (Rol: Legal)', ['id' => $persona->id]);

            $adultoMayorData = [
                'ci' => $request->ci,
                'discapacidad' => $request->discapacidad,
                'vive_con' => $request->vive_con,
                'migrante' => $request->migrante == '1',
                'fecha' => $request->fecha,
            ];
            if ($request->filled('nro_caso')) {
                $adultoMayorData['nro_caso'] = $request->nro_caso;
            }
            $adultoMayor = AdultoMayor::create($adultoMayorData);
            Log::info('Adulto Mayor creado (Rol: Legal)', ['id' => $adultoMayor->id_adulto ?? $adultoMayor->id]);

            DB::commit();
            Log::info('Transacción completada (Rol: Legal)');

            // Redirección a la ruta del listado de LEGAL
            return redirect()->route('legal.gestionar-adultomayor.index')
                             ->with('success', 'Adulto Mayor registrado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error registrando adulto mayor (Rol: Legal): ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->back()
                             ->withErrors(['error_registro' => 'Ocurrió un error interno al registrar. Detalles: ' . $e->getMessage()])
                             ->withInput();
        }
    }

    /**
     * Muestra el formulario para editar un Adulto Mayor.
     */
    public function adultoMayorEdit($ci)
    {
        try {
            $adultoMayor = Persona::with('adultoMayor')->where('ci', $ci)->firstOrFail();

            // !! IMPORTANTE: Asegúrate de crear esta vista en:
            // resources/views/pages/legal/adultomayor/edit.blade.php
            return view('pages.legal.adultomayor.edit', compact('adultoMayor'));
        } catch (\Exception $e) {
            Log::error("Error cargando adulto mayor para editar (Rol: Legal, CI: {$ci}): " . $e->getMessage());
            return redirect()->route('legal.gestionar-adultomayor.index')
                             ->with('error', 'Error al cargar los datos del adulto mayor.');
        }
    }

    /**
     * Actualiza un Adulto Mayor en la base de datos.
     */
    public function adultoMayorUpdate(Request $request, $ci_original)
    {
        $persona = Persona::where('ci', $ci_original)->firstOrFail();
        $idAdultoMayor = $persona->adultoMayor->id_adulto; // Asume que la PK se llama id_adulto

        $validator = Validator::make($request->all(), [
            'nombres' => 'required|string|max:255',
            'primer_apellido' => 'required|string|max:255',
            'ci' => 'required|string|max:20|unique:persona,ci,' . $persona->id,
            'fecha_nacimiento' => 'required|date|before_or_equal:today',
            'nro_caso' => 'nullable|string|max:50|unique:adulto_mayor,nro_caso,' . $idAdultoMayor . ',id_adulto',
            // ... (resto de validaciones)
        ], [
            'ci.unique' => 'Este CI ya ha sido registrado por otra persona.',
            'nro_caso.unique' => 'Este número de caso ya está en uso.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        DB::beginTransaction();
        try {
            // Actualizar Persona
            $persona->update([
                'ci' => $request->ci,
                'primer_apellido' => $request->primer_apellido,
                'segundo_apellido' => $request->segundo_apellido,
                'nombres' => $request->nombres,
                'sexo' => $request->sexo,
                'fecha_nacimiento' => $request->fecha_nacimiento,
                'edad' => Carbon::parse($request->fecha_nacimiento)->age,
                'estado_civil' => $request->estado_civil,
                'domicilio' => $request->domicilio,
                'telefono' => $request->telefono,
                'zona_comunidad' => $request->zona_comunidad,
            ]);

            // Actualizar AdultoMayor
            $adultoMayorData = [
                'ci' => $request->ci,
                'discapacidad' => $request->discapacidad,
                'vive_con' => $request->vive_con,
                'migrante' => $request->migrante == '1',
                'fecha' => $request->fecha,
                'nro_caso' => $request->filled('nro_caso') ? $request->nro_caso : null,
            ];
            $persona->adultoMayor()->update($adultoMayorData);

            DB::commit();
            
            return redirect()->route('legal.gestionar-adultomayor.index')
                             ->with('success', 'Adulto mayor actualizado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error actualizando adulto mayor (Rol: Legal, CI: {$ci_original}): " . $e->getMessage());
            return redirect()->back()
                             ->withErrors(['error_actualizacion' => 'Error al actualizar: ' . $e->getMessage()])
                             ->withInput();
        }
    }

    /**
     * Elimina un Adulto Mayor de la base de datos.
     */
    public function adultoMayorDestroy($ci)
    {
        DB::beginTransaction();
        try {
            $persona = Persona::where('ci', $ci)->first();
            if (!$persona) {
                return redirect()->route('legal.gestionar-adultomayor.index')
                                 ->with('error', 'Persona no encontrada.');
            }

            // El modelo Persona debería tener un evento 'deleting' para eliminar
            // el registro de adulto_mayor asociado automáticamente.
            // Si no, elimina manualmente primero:
            // AdultoMayor::where('ci', $ci)->delete();
            
            $persona->delete();

            DB::commit();
            return redirect()->route('legal.gestionar-adultomayor.index')
                             ->with('success', 'Registro del Adulto Mayor eliminado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error eliminando adulto mayor (Rol: Legal, CI: {$ci}): " . $e->getMessage());
            return redirect()->route('legal.gestionar-adultomayor.index')
                             ->with('error', 'Error al eliminar el registro.');
        }
    }

    //======================================================================
    // MÉTODOS PARA MÓDULO DE PROTECCIÓN
    //======================================================================

    /**
     * Muestra la lista de casos de protección.
     * Responde a la ruta 'legal.proteccion.index'
     */
    public function proteccionIndex()
    {
        // Lógica para obtener y mostrar la lista de casos
        // $casos = CasoProteccion::paginate(10);
        // return view('pages.legal.proteccion.index', compact('casos'));

        // Vista de marcador de posición
        return "Página para ver Casos de Protección (Legal)";
    }

    /**
     * Muestra el formulario para crear un nuevo caso.
     * Responde a la ruta 'legal.proteccion.create'
     */
    public function proteccionCreate()
    {
        // return view('pages.legal.proteccion.create');
        
        // Vista de marcador de posición
        return "Formulario para registrar un nuevo Caso de Protección (Legal)";
    }

    /**
     * Guarda un nuevo caso en la base de datos.
     */
    public function proteccionStore(Request $request)
    {
        // Lógica de validación y almacenamiento del caso
        // ...
        // return redirect()->route('legal.proteccion.index')->with('success', 'Caso registrado exitosamente.');
    }

    /**
     * Muestra los detalles de un caso específico.
     */
    public function proteccionShow($id)
    {
        // Lógica para encontrar y mostrar un caso
        // $caso = CasoProteccion::findOrFail($id);
        // return view('pages.legal.proteccion.show', compact('caso'));
    }

    /**
     * Muestra el formulario para editar un caso.
     */
    public function proteccionEdit($id)
    {
        // Lógica para encontrar el caso y mostrar el formulario de edición
        // $caso = CasoProteccion::findOrFail($id);
        // return view('pages.legal.proteccion.edit', compact('caso'));
    }

    /**
     * Actualiza un caso en la base de datos.
     */
    public function proteccionUpdate(Request $request, $id)
    {
        // Lógica para validar y actualizar el caso
        // ...
        // return redirect()->route('legal.proteccion.index')->with('success', 'Caso actualizado.');
    }

    /**
     * Elimina un caso de la base de datos.
     */
    public function proteccionDestroy($id)
    {
        // Lógica para eliminar el caso
        // ...
        // return redirect()->route('legal.proteccion.index')->with('success', 'Caso eliminado.');
    }
    
    /**
     * Muestra la página de reportes de protección.
     */
    public function proteccionReportes()
    {
        // Lógica para generar y mostrar reportes
        // return view('pages.legal.proteccion.reportes');

        // Vista de marcador de posición
        return "Página de Reportes de Protección (Legal)";
    }
}
