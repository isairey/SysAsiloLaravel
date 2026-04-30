<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Persona; // Asegúrate que el namespace sea correcto
use App\Models\Rol;     // Asegúrate que el namespace sea correcto
use App\Models\AdultoMayor;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator; // Usar Validator para más control
use Illuminate\Support\Facades\Auth; // <-- LÍNEA IMPORTANTE: Importa la clase Auth
use Carbon\Carbon; // Para calcular la edad
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    /**
     * Muestra el dashboard con estadísticas y la tabla de usuarios.
     */
    public function dashboard()
    {
        // Obtener todos los usuarios con sus relaciones
        $users = User::with(['persona', 'rol'])
                    ->orderBy('created_at', 'desc')
                    ->get();

        // Estadísticas
        $totalUsers    = $users->count();
        $activeUsers   = $users->where('active', true)->count();
        $inactiveUsers = $users->where('active', false)->count();
        $lockedUsers   = User::where('active', false)
                                ->whereNotNull('temporary_lockout_until')
                                ->count();

        return view('Admin.dashboard', compact(
            'users',
            'totalUsers',
            'activeUsers',
            'inactiveUsers',
            'lockedUsers'
        ));
    }

    /**
     * Muestra la lista de usuarios por separado (si la necesitas).
     */
    public function listUsers()
    {
        $users = User::with(['persona', 'rol'])
                    ->orderBy('created_at', 'desc')
                    ->get();

        return view('Admin.users', compact('users'));
    }

    /**
     * Activa o desactiva un usuario.
     */
    public function toggleActive(User $user)
    {
        $user->active = !$user->active;

        if (! $user->active) {
            $user->temporary_lockout_until = null;
            $user->login_attempts = 0;
            $user->last_failed_login_at = null;
        }

        $user->save();

        $status = $user->active ? 'activado' : 'desactivado';
        Log::info("Admin cambió estado de usuario {$user->ci} a {$status}");

        return back()->with('success', "Usuario {$status} exitosamente.");
    }

    /** Mostrar formularios de registro *
    public function showRegisterAsistenteSocial()
    {
        return view('Admin.registerUsers.registerAsistsocial.registerAsistsocial');
    }*/

    public function showRegisterLegal()
    {
        return view('Admin.registerUsers.registerLegal.registerLeg');
    }

    public function showRegisterAdultoMayor()
    {
        return view('Admin.registerUsers.registerPaciente.registerPac');
    }

    public function showRegisterResponsableSalud() // Este método muestra el formulario
    {
        // Podrías pasar roles si el campo de rol fuera un select dinámico
        // $roles = Rol::all();
        // return view('Admin.registerUsers.registerResponsable.registerRes', compact('roles'));
        return view('Admin.registerUsers.registerResponsable.registerRes');
    }

    // ==================================================================
    // ===               INICIO: SOLUCIÓN DEL ERROR                   ===
    // ==================================================================
    /**
     * Almacena un nuevo usuario con el rol 'legal' en la base de datos.
     * ESTE ES EL MÉTODO QUE FALTABA Y SOLUCIONA EL ERROR.
     */
    public function storeUsuarioLegal(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // Pestaña 1: Datos Personales (tabla 'persona')
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
            'area_especialidad_legal' => 'required|string|in:Derecho,Psicologia,Asistente Social,otro',

            // Pestaña 2: Datos de Usuario (tabla 'users')
            'id_rol' => 'required|integer|exists:rol,id_rol',
            'password' => 'required|string|min:8|confirmed',
            'terms_acceptance' => 'accepted' // Importante para validar el checkbox
        ], [
            // Mensajes personalizados
            'nombres.required' => 'El campo nombres es obligatorio.',
            'primer_apellido.required' => 'El campo primer apellido es obligatorio.',
            'ci.required' => 'El campo CI es obligatorio.',
            'ci.unique' => 'Este CI ya ha sido registrado.',
            'fecha_nacimiento.required' => 'La fecha de nacimiento es obligatoria.',
            'fecha_nacimiento.before_or_equal' => 'La fecha de nacimiento no puede ser futura.',
            'sexo.required' => 'El campo sexo es obligatorio.',
            'estado_civil.required' => 'El estado civil es obligatorio.',
            'domicilio.required' => 'El domicilio es obligatorio.',
            'telefono.required' => 'El teléfono es obligatorio.',
            'area_especialidad_legal.required' => 'El área de especialidad legal es obligatoria.',
            'area_especialidad_legal.in' => 'El área de especialidad seleccionada no es válida.',
            'id_rol.required' => 'El rol es obligatorio.',
            'id_rol.exists' => 'El rol seleccionado no es válido.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'La confirmación de contraseña no coincide.',
            'terms_acceptance.accepted' => 'Debe aceptar los términos y condiciones.'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                              ->withErrors($validator)
                              ->withInput();
        }

        DB::beginTransaction();

        try {
            // Calcular edad
            $edad = Carbon::parse($request->fecha_nacimiento)->age;

            // 1. Crear Persona
            Persona::create([
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
                'area_especialidad_legal' => $request->area_especialidad_legal,
                'area_especialidad' => null, // Aseguramos que el otro campo de especialidad sea nulo
            ]);

            // 2. Crear Usuario
            User::create([
                'ci' => $request->ci,
                'id_rol' => $request->id_rol,
                'name' => $request->nombres . ' ' . $request->primer_apellido,
                'password' => Hash::make($request->password),
                'active' => true,
                'login_attempts' => 0,
            ]);

            DB::commit();

            return redirect()->route('admin.dashboard')
                              ->with('success', 'Usuario Legal registrado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error registrando usuario legal: ' . $e->getMessage() . ' en ' . $e->getFile() . ':' . $e->getLine());
            return redirect()->back()
                              ->withErrors(['error_registro' => 'Ocurrió un error interno al registrar al usuario. Por favor, inténtelo más tarde.'])
                              ->withInput();
        }
    }
    // ==================================================================
    // ===                 FIN: SOLUCIÓN DEL ERROR                    ===
    // ==================================================================


public function storeAdultoMayor(Request $request)
    {
        Log::info('Iniciando registro de adulto mayor', ['data' => $request->all()]);

        // =========================================================================
        // === INICIO: MODIFICACIÓN EN VALIDACIÓN ===
        // =========================================================================
        $validator = Validator::make($request->all(), [
            // Pestaña 1: Datos Personales (tabla 'persona')
            'nombres' => 'required|string|max:255',
            'primer_apellido' => 'required|string|max:255',
            'segundo_apellido' => 'nullable|string|max:255',
            'ci' => [
                'required',
                'string',
                'max:25', // Se aumenta el límite por si acaso
                'regex:/^[a-zA-Z0-9-]+$/', // Permite letras (mayúsculas/minúsculas), números y guiones
                'unique:persona,ci'
            ],
            'fecha_nacimiento' => 'required|date|before_or_equal:today',
            'sexo' => 'required|string|in:F,M,O',
            'estado_civil' => 'required|string|in:casado,divorciado,soltero,otro',
            'domicilio' => 'required|string|max:255',
            'telefono' => 'required|string|max:20',
            'zona_comunidad' => 'nullable|string|max:100',

            // Pestaña 2: Datos específicos de adulto mayor (tabla 'adulto_mayor')
            'discapacidad' => 'nullable|string',
            'vive_con' => 'nullable|string|max:200',
            'migrante' => 'required|in:0,1',
            'origen_migracion' => 'nullable|string|max:255|required_if:migrante,1',
            'nro_caso' => 'nullable|string|max:50|unique:adulto_mayor,nro_caso',
            'fecha' => 'required|date',
        ], [
            // Mensajes personalizados
            'nombres.required' => 'El campo nombres es obligatorio.',
            'primer_apellido.required' => 'El campo primer apellido es obligatorio.',
            'ci.required' => 'El campo CI es obligatorio.',
            'ci.unique' => 'Este CI ya ha sido registrado.',
            'ci.regex' => 'El formato del CI solo puede contener letras, números y guiones.', // Mensaje para el nuevo formato
            'fecha_nacimiento.required' => 'La fecha de nacimiento es obligatoria.',
            'fecha_nacimiento.before_or_equal' => 'La fecha de nacimiento no puede ser futura.',
            'sexo.required' => 'El campo sexo es obligatorio.',
            'sexo.in' => 'El sexo debe ser Femenino, Masculino u Otro.',
            'estado_civil.required' => 'El estado civil es obligatorio.',
            'estado_civil.in' => 'El estado civil debe ser uno de los valores válidos.',
            'domicilio.required' => 'El domicilio es obligatorio.',
            'telefono.required' => 'El teléfono es obligatorio.',
            'nro_caso.unique' => 'Este número de caso ya ha sido registrado.',
            'fecha.required' => 'La fecha de registro es obligatoria.',
            'fecha.date' => 'La fecha de registro debe ser una fecha válida.',
            'origen_migracion.required_if' => 'El lugar de origen es obligatorio si la persona es migrante.',
        ]);
        if ($validator->fails()) {
            Log::warning('Validación falló para registro de adulto mayor', ['errors' => $validator->errors()]);
            return redirect()->back()
                            ->withErrors($validator)
                            ->withInput();
        }

        DB::beginTransaction();

        try {
            // Calcular edad
            $edad = Carbon::parse($request->fecha_nacimiento)->age;
            
            Log::info('Calculando edad', ['fecha_nacimiento' => $request->fecha_nacimiento, 'edad' => $edad]);

            // 1. Crear Persona
            // Si estás usando modelos Eloquent y tienen $fillable configurado, puedes usar create.
            // Si no, el Query Builder como lo tenías está bien, pero Persona::create es más idiomático de Eloquent.
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
                // 'created_at' y 'updated_at' usualmente son manejados por Eloquent automáticamente
            ]);

            Log::info('Persona creada exitosamente', ['persona_id' => $persona->id, 'ci' => $persona->ci]);

            // 2. Preparar datos para adulto_mayor
            $adultoMayorData = [
                'ci' => $request->ci,
                'discapacidad' => $request->discapacidad,
                'vive_con' => $request->vive_con,
                'migrante' => $request->migrante == '1' ? true : false,
                'origen_migracion' => $request->migrante == '1' ? $request->input('origen_migracion') : null, // <-- CAMPO AÑADIDO CON LÓGICA
                'fecha' => $request->fecha,
            ];

            // Solo agregar nro_caso si se proporcionó y no está vacío
            if ($request->filled('nro_caso')) {
                $adultoMayorData['nro_caso'] = $request->nro_caso;
            }

            // Si usas Eloquent para AdultoMayor y tiene una relación definida con Persona,
            // podrías hacer algo como $persona->adultoMayor()->create($adultoMayorData);
            // o si 'ci' es la clave primaria/única en adulto_mayor y también la FK:
            $adultoMayor = AdultoMayor::create($adultoMayorData);


            Log::info('Adulto mayor creado exitosamente', ['adulto_mayor_id' => $adultoMayor->id_adulto ?? $adultoMayor->id]); // Ajusta según tu PK

            DB::commit();

            Log::info('Transacción completada exitosamente');

            return redirect()->route('gestionar-adultomayor.index') // <--- ESTA ES LA LÍNEA CORRECTA
                 ->with('success', 'Adulto Mayor registrado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error registrando adulto mayor: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                // 'trace' => $e->getTraceAsString(), // Puede ser muy verboso para logs regulares
                'request_data' => $request->all()
            ]);
            
            return redirect()->back()
                            ->withErrors(['error_registro' => 'Ocurrió un error interno al registrar al adulto mayor. Por favor, intente de nuevo. Detalles: ' . $e->getMessage()])
                            ->withInput();
        }
    }

    /**
    * Mostrar listado de adultos mayores
    */
    public function gestionarAdultoMayorIndex()
    {
        try {
            // Se inicia la consulta con el Modelo Eloquent en lugar de DB::table()
            $adultosMayores = AdultoMayor::join('persona as p', 'adulto_mayor.ci', '=', 'p.ci')
                ->select([
                    'p.ci',
                    'p.nombres',
                    'p.primer_apellido',
                    'p.segundo_apellido',
                    'p.sexo',
                    'p.fecha_nacimiento',
                    'p.edad',
                    'p.estado_civil',
                    'p.domicilio',
                    'p.telefono',
                    'p.zona_comunidad',
                    'adulto_mayor.id_adulto', // Clave primaria de adulto_mayor
                    'adulto_mayor.discapacidad',
                    'adulto_mayor.vive_con',
                    'adulto_mayor.migrante',
                    'adulto_mayor.nro_caso',
                    'adulto_mayor.fecha as fecha_registro'
                ])
                ->orderBy('p.primer_apellido', 'asc')
                ->orderBy('p.nombres', 'asc')
                ->paginate(10); // Paginación

            return view('Admin.gestionarAdultoMayor.index', compact('adultosMayores'));
            
        } catch (\Exception $e) {
            Log::error('Error al cargar listado de adultos mayores: ' . $e->getMessage());
            return redirect()->route('admin.dashboard')
                             ->with('error', 'Error al cargar el listado de adultos mayores.');
        }
    }


    /**
     * Buscar adultos mayores (AJAX) - VERSIÓN OPTIMIZADA CON FULLTEXT SEARCH
     */
    public function buscarAdultoMayor(Request $request)
    {
        try {
            $busqueda = $request->get('busqueda', '');
            
            // Se inicia la consulta con el alias 'am'
            $query = AdultoMayor::from('adulto_mayor as am')
                // Se incluye withTrashed() y whereNull() para manejar correctamente el SoftDeletes con el alias
                ->withTrashed()
                ->join('persona as p', 'am.ci', '=', 'p.ci')
                ->whereNull('am.deleted_at')
                ->select([
                    'p.ci',
                    'p.nombres',
                    'p.primer_apellido',
                    'p.segundo_apellido',
                    'p.sexo',
                    'p.fecha_nacimiento',
                    'p.edad',
                    'p.estado_civil',
                    'p.domicilio',
                    'p.telefono',
                    'p.zona_comunidad',
                    'am.id_adulto',
                    'am.discapacidad',
                    'am.vive_con',
                    'am.migrante',
                    'am.nro_caso',
                    'am.fecha as fecha_registro'
                ]);

            if (!empty($busqueda)) {
                $query->where(function($q) use ($busqueda) {
                    // Búsqueda por CI: Sigue siendo rápida porque no usa un comodín inicial.
                    $q->where('p.ci', 'LIKE', $busqueda . '%');

                    // =========================================================================
                    // === INICIO: CAMBIO A BÚSQUEDA FULLTEXT ===
                    // =========================================================================
                    // Se usa `orWhereRaw` para ejecutar la consulta de texto completo nativa.
                    // `MATCH(...) AGAINST(...)` utiliza el índice FULLTEXT que creamos y es extremadamente rápido.
                    
                    $driver = DB::connection()->getDriverName();
                    
                    if ($driver === 'mysql') {
                        // Sintaxis para MySQL
                        $q->orWhereRaw(
                           'MATCH(p.nombres, p.primer_apellido, p.segundo_apellido) AGAINST(? IN BOOLEAN MODE)',
                           [$busqueda . '*'] // El '*' actúa como comodín para palabras que empiezan con el término.
                       );
                    } elseif ($driver === 'pgsql') {
                        // Sintaxis para PostgreSQL (requiere una configuración de tsvector diferente, pero esto funcionaría)
                        $q->orWhereRaw(
                            "to_tsvector('spanish', p.nombres || ' ' || p.primer_apellido || ' ' || p.segundo_apellido) @@ to_tsquery('spanish', ?)",
                            [$busqueda . ':*']
                        );
                    } else {
                        // Fallback para otras bases de datos como SQLite que no soportan FULLTEXT de la misma manera
                        $q->orWhere('p.nombres', 'ILIKE', '%' . $busqueda . '%')
                          ->orWhere('p.primer_apellido', 'ILIKE', '%' . $busqueda . '%')
                          ->orWhere('p.segundo_apellido', 'ILIKE', '%' . $busqueda . '%');
                    }
                    // =========================================================================
                    // === FIN: CAMBIO A BÚSQUEDA FULLTEXT ===
                    // =========================================================================
                });
            }

            $adultosMayores = $query->orderBy('p.primer_apellido', 'asc')
                                    ->orderBy('p.nombres', 'asc')
                                    ->paginate(10);

            return response()->json([
                'success' => true,
                'html' => view('Admin.gestionarAdultoMayor.partials.tabla-adultos', compact('adultosMayores'))->render(),
                'pagination' => $adultosMayores->links()->toHtml(),
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error en búsqueda de adultos mayores: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'Error en la búsqueda. Detalles: ' . $e->getMessage()
            ], 500);
        }
    }
    /**
    * Mostrar formulario de edición
    */
    public function editarAdultoMayor($ci) // Se recibe el CI de la persona
    {
        try {
             $adultoMayor = DB::table('persona as p')
                ->join('adulto_mayor as am', 'p.ci', '=', 'am.ci')
                ->select([
                    'p.*', // Todos los campos de persona
                    'am.id_adulto',
                    'am.discapacidad',
                    'am.vive_con',
                    'am.migrante',
                    'am.origen_migracion', // <-- CAMPO AÑADIDO
                    'am.nro_caso',
                    'am.fecha as fecha_registro_am'
                ])
                ->where('p.ci', $ci)
                ->first();

            if (!$adultoMayor) {
                return redirect()->route('admin.gestionar-adultomayor.index')
                                ->with('error', 'Adulto mayor no encontrado.');
            }
            
            // Convertir migrante a string '0' o '1' para el select del formulario si es necesario
            if (isset($adultoMayor->migrante)) {
                $adultoMayor->migrante = $adultoMayor->migrante ? '1' : '0';
            }


            return view('Admin.gestionarAdultoMayor.editar.editAdultoMayor', compact('adultoMayor'));
            
        } catch (\Exception $e) {
            Log::error('Error al cargar datos para edición: ' . $e->getMessage());
            return redirect()->route('admin.gestionar-adultomayor.index')
                            ->with('error', 'Error al cargar los datos del adulto mayor.');
        }
    }

    /**
    * Actualizar adulto mayor
    */
    public function actualizarAdultoMayor(Request $request, $ci_original)
    {
        $adultoMayorDb = DB::table('adulto_mayor')->where('ci', $ci_original)->first();

        if (!$adultoMayorDb) {
            return redirect()->back()
                             ->withErrors(['error_actualizacion' => 'Registro de Adulto Mayor no encontrado para el CI proporcionado.'])
                             ->withInput();
        }
        $idAdultoMayor = $adultoMayorDb->id_adulto;

        $validator = Validator::make($request->all(), [
            // Datos de persona
            'nombres' => 'required|string|max:255',
            'primer_apellido' => 'required|string|max:255',
            'segundo_apellido' => 'nullable|string|max:255',
            'ci' => [
                'required',
                'string',
                'max:25',
                'regex:/^[a-zA-Z0-9-]+$/', // Permite letras, números y guiones
                Rule::unique('persona')->ignore($ci_original, 'ci') // Ignora el CI actual al verificar unicidad
            ],
            'fecha_nacimiento' => 'required|date|before_or_equal:today',
            'sexo' => 'required|string|in:F,M,O',
            'estado_civil' => 'required|string|in:casado,divorciado,soltero,otro',
            'domicilio' => 'required|string|max:255',
            'telefono' => 'required|string|max:20',
            'zona_comunidad' => 'nullable|string|max:100',
            
            // Datos de adulto mayor
            'discapacidad' => 'nullable|string',
            'vive_con' => 'nullable|string|max:200',
            'migrante' => 'required|in:0,1',
            'origen_migracion' => 'nullable|string|max:255|required_if:migrante,1',
            'nro_caso' => 'nullable|string|max:50|unique:adulto_mayor,nro_caso,' . $idAdultoMayor . ',id_adulto',
            'fecha' => 'required|date',
        ], [
            // Mensajes de error
            'nombres.required' => 'El campo nombres es obligatorio.',
            'primer_apellido.required' => 'El campo primer apellido es obligatorio.',
            'ci.required' => 'El campo CI es obligatorio.',
            'ci.unique' => 'Este CI ya ha sido registrado por otra persona.',
            'ci.regex' => 'El formato del CI solo puede contener letras, números y guiones.',
            'fecha_nacimiento.required' => 'La fecha de nacimiento es obligatoria.',
            'fecha_nacimiento.before_or_equal' => 'La fecha de nacimiento no puede ser futura.',
            'sexo.required' => 'El campo sexo es obligatorio.',
            'estado_civil.required' => 'El estado civil es obligatorio.',
            'estado_civil.in' => 'El valor seleccionado para el estado civil no es válido.',
            'domicilio.required' => 'El domicilio es obligatorio.',
            'telefono.required' => 'El teléfono es obligatorio.',
            'nro_caso.unique' => 'Este número de caso ya ha sido registrado para otro adulto mayor.',
            'fecha.required' => 'La fecha de registro del adulto mayor es obligatoria.',
            'origen_migracion.required_if' => 'El lugar de origen es obligatorio si la persona es migrante.',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                             ->withErrors($validator)
                             ->withInput();
        }

        DB::beginTransaction();

        try {
            $edad = Carbon::parse($request->fecha_nacimiento)->age;

            // 1. Actualizar Persona
            DB::table('persona')
              ->where('ci', $ci_original)
              ->update([
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
                  'updated_at' => now()
              ]);

            // 2. Preparar y actualizar datos de adulto_mayor
            $adultoMayorData = [
                'ci' => $request->ci,
                'discapacidad' => $request->discapacidad,
                'vive_con' => $request->vive_con,
                'migrante' => $request->migrante == '1' ? true : false,
                'origen_migracion' => $request->migrante == '1' ? $request->input('origen_migracion') : null, // <-- CAMPO AÑADIDO CON LÓGICA
                'fecha' => $request->fecha,
                'nro_caso' => $request->filled('nro_caso') ? $request->nro_caso : null,
                'updated_at' => now()
            ];

            DB::table('adulto_mayor')
              ->where('id_adulto', $idAdultoMayor)
              ->update($adultoMayorData);

            DB::commit();

            return redirect()->route('gestionar-adultomayor.index')
                             ->with('success', 'Adulto mayor actualizado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error actualizando adulto mayor: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            
            return redirect()->back()
                             ->withErrors(['error_actualizacion' => 'Ocurrió un error inesperado al actualizar el registro. Por favor, intente de nuevo.'])
                             ->withInput();
        }
    }
        /**
     * Eliminar adulto mayor (con protección de rol implementada)
     */
    public function eliminarAdultoMayor($ci)
    {
        // =========================================================================
        // === INICIO: MEDIDA DE SEGURIDAD POR ROL ===
        // =========================================================================
        // 1. Se verifica si el usuario autenticado tiene el rol de 'admin'.
        if (optional(Auth::user()->rol)->nombre_rol !== 'admin') {
            // 2. Si no es 'admin', se registra el intento no autorizado y se redirige.
            Log::warning('Intento de eliminación NO AUTORIZADO de adulto mayor', [
                'user_id' => Auth::id(),
                'user_role' => optional(Auth::user()->rol)->nombre_rol ?? 'sin_rol',
                'remote_ip' => request()->ip(),
                'ci_adulto' => $ci
            ]);
            
            return redirect()->route('gestionar-adultomayor.index')
                             ->with('error', 'Acción no autorizada. No tiene permisos para eliminar registros.');
        }
        // =========================================================================
        // === FIN: MEDIDA DE SEGURIDAD POR ROL ===
        // =========================================================================

        DB::beginTransaction();

        try {
            // Se utiliza el modelo Eloquent para poder aplicar el Soft Delete.
            $persona = Persona::find($ci);
            
            if (!$persona) {
                // La redirección no necesita rollback si no se hizo nada.
                return redirect()->route('gestionar-adultomayor.index')
                                 ->with('error', 'Persona no encontrada.');
            }

            // Eliminar lógicamente el registro de AdultoMayor asociado (si existe).
            if ($persona->adultoMayor) {
                $persona->adultoMayor->delete(); // Esto aplica Soft Delete
            }

            // Eliminar lógicamente la persona.
            $persona->delete();
            
            // Si la persona también era un usuario del sistema, eliminarlo lógicamente también.
            if ($persona->usuario) {
                $persona->usuario->delete();
            }

            DB::commit();

            return redirect()->route('gestionar-adultomayor.index')
                             ->with('success', 'Adulto Mayor enviado a la papelera exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error eliminando adulto mayor: ' . $e->getMessage());
            
            return redirect()->route('gestionar-adultomayor.index')
                             ->with('error', 'Error al enviar el adulto mayor a la papelera.');
        }
    }
// MÉTODO ACTUALIZADO PARA EL NUEVO FORMULARIO DE RESPONSABLE DE SALUD
public function storeResponsableSalud(Request $request)
{
    $validator = Validator::make($request->all(), [
        // Pestaña 1: Datos Personales (tabla 'persona')
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
        // CORRECCIÓN: Se actualiza la regla 'in' para que coincida con los valores de la base de datos.
        'area_especialidad' => 'required|string|in:Enfermeria,Fisioterapia-Kinesiologia,otro',

        // Pestaña 2: Datos de Usuario (tabla 'users')
        'id_rol' => 'required|integer|exists:rol,id_rol',
        'password' => 'required|string|min:8|confirmed',
        'terms_acceptance' => 'accepted' // Importante para validar el checkbox
    ], [
        // Mensajes personalizados
        'nombres.required' => 'El campo nombres es obligatorio.',
        'primer_apellido.required' => 'El campo primer apellido es obligatorio.',
        'ci.required' => 'El campo CI es obligatorio.',
        'ci.unique' => 'Este CI ya ha sido registrado.',
        'fecha_nacimiento.required' => 'La fecha de nacimiento es obligatoria.',
        'fecha_nacimiento.before_or_equal' => 'La fecha de nacimiento no puede ser futura.',
        'sexo.required' => 'El campo sexo es obligatorio.',
        'estado_civil.required' => 'El estado civil es obligatorio.',
        'domicilio.required' => 'El domicilio es obligatorio.',
        'telefono.required' => 'El teléfono es obligatorio.',
        'area_especialidad.required' => 'El área de especialidad es obligatoria para el responsable de salud.',
        'area_especialidad.in' => 'El área de especialidad seleccionada no es válida.',
        'id_rol.required' => 'El rol es obligatorio.',
        'id_rol.exists' => 'El rol seleccionado no es válido.',
        'password.required' => 'La contraseña es obligatoria.',
        'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
        'password.confirmed' => 'La confirmación de contraseña no coincide.',
        'terms_acceptance.accepted' => 'Debe aceptar los términos y condiciones.'
    ]);

    if ($validator->fails()) {
        return redirect()->back()
                         ->withErrors($validator)
                         ->withInput();
    }

    DB::beginTransaction();

    try {
        // Calcular edad
        $edad = Carbon::parse($request->fecha_nacimiento)->age;

        // 1. Crear Persona
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
            'area_especialidad' => $request->area_especialidad,
            'area_especialidad_legal' => null, // Aseguramos que el otro campo de especialidad sea nulo
        ]);

        // 2. Crear Usuario
        User::create([
            'ci' => $request->ci,
            'id_rol' => $request->id_rol,
            'name' => $request->nombres . ' ' . $request->primer_apellido,
            'password' => Hash::make($request->password),
            'active' => true,
            'login_attempts' => 0,
        ]);

        DB::commit();

        return redirect()->route('admin.dashboard')
                         ->with('success', 'Responsable de Salud registrado exitosamente.');

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error registrando responsable de salud: ' . $e->getMessage() . ' en ' . $e->getFile() . ':' . $e->getLine());
        return redirect()->back()
                         ->withErrors(['error_registro' => 'Ocurrió un error interno al registrar al responsable. Por favor, inténtelo más tarde.'])
                         ->withInput();
    }
}


}
