<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Persona;
use App\Models\Rol;
use App\Models\AdultoMayor; // <-- ESTA ES LA LÍNEA QUE FALTABA
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;


class GestionarUsuariosController extends Controller
{
    /**
     * Muestra una lista de todos los usuarios registrados.
     */
    public function index(Request $request)
    {
        // La carga anticipada (eager loading) con 'persona' y 'rol' es correcta y eficiente.
        $query = User::with(['persona', 'rol'])->orderBy('created_at', 'desc');

        // Lógica de búsqueda
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                // CORRECCIÓN: Se elimina la búsqueda en 'name' ya que esa columna no existe en la tabla 'usuario'.
                // La búsqueda por nombre se hace correctamente a través de la relación 'persona'.
                $q->where('ci', 'like', "%{$searchTerm}%")
                  ->orWhereHas('rol', function ($q) use ($searchTerm) {
                      $q->where('nombre_rol', 'like', "%{$searchTerm}%");
                  })
                  ->orWhereHas('persona', function ($q) use ($searchTerm) {
                      $q->where('nombres', 'like', "%{$searchTerm}%")
                        ->orWhere('primer_apellido', 'like', "%{$searchTerm}%")
                        ->orWhere('segundo_apellido', 'like', "%{$searchTerm}%");
                  });
            });
        }

        $users = $query->paginate(10);
        
        return view('Admin.gestionarUsers.index', compact('users'));
    }

    /**
     * Muestra el formulario para editar un usuario específico.
     */
    public function edit($id_usuario)
    {
        // La consulta para obtener el usuario con sus relaciones es correcta.
        $user = User::with(['persona', 'rol'])->findOrFail($id_usuario);
        $roles = Rol::where('nombre_rol', '!=', 'superadmin')->get(); // Excluir superadmin de la lista

        // Determina si el rol del usuario es 'adulto_mayor' para deshabilitar la edición del rol.
        // NOTA: Si este rol no existe, esta variable simplemente será 'false'.
        $isAdultoMayorRole = optional($user->rol)->nombre_rol === 'adulto_mayor';

        return view('Admin.gestionarUsers.editar.edit', compact('user', 'roles', 'isAdultoMayorRole'));
    }

    /**
     * Actualiza los datos del usuario especificado en la base de datos.
     */
    public function update(Request $request, $id_usuario)
    {
        // ... (Este método se mantiene sin cambios)
        $user = User::with('persona')->findOrFail($id_usuario);
        $persona = $user->persona;

        // Reglas de validación base
        $rules = [
            'nombres' => 'required|string|max:255',
            'primer_apellido' => 'required|string|max:255',
            'segundo_apellido' => 'nullable|string|max:255',
            'fecha_nacimiento' => 'required|date|before_or_equal:today',
            'sexo' => 'required|string|in:M,F,O',
            'estado_civil' => 'required|string|in:casado,divorciado,soltero,otro',
            'domicilio' => 'required|string|max:255',
            'telefono' => 'required|string|max:20',
            'zona_comunidad' => 'nullable|string|max:100',
            'password' => 'nullable|string|min:8|confirmed',
        ];

        if (optional($user->rol)->nombre_rol !== 'adulto_mayor') {
            $rules['id_rol'] = ['required', 'integer', Rule::exists('rol', 'id_rol')];
        }

        $rolId = $request->input('id_rol', $user->id_rol);

        if ($rolId == 2) { // Rol Responsable
            $rules['area_especialidad'] = 'required|string|in:Enfermeria,Fisioterapia-Kinesiologia,otro';
            $rules['area_especialidad_legal'] = 'nullable|string';
        } elseif ($rolId == 3) { // Rol Legal
            $rules['area_especialidad_legal'] = 'required|string|in:Asistente Social,Psicologia,Derecho';
            $rules['area_especialidad'] = 'nullable|string';
        } else {
            $rules['area_especialidad'] = 'nullable|string';
            $rules['area_especialidad_legal'] = 'nullable|string';
        }

        $messages = [
            'nombres.required' => 'El nombre es obligatorio.',
            'primer_apellido.required' => 'El primer apellido es obligatorio.',
            'fecha_nacimiento.required' => 'La fecha de nacimiento es obligatoria.',
            'sexo.required' => 'El sexo es obligatorio.',
            'estado_civil.required' => 'El estado civil es obligatorio.',
            'domicilio.required' => 'El domicilio es obligatorio.',
            'telefono.required' => 'El teléfono es obligatorio.',
            'id_rol.required' => 'El rol es obligatorio.',
            'password.min' => 'La nueva contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
            'area_especialidad.required' => 'El área de especialidad de salud es obligatoria para el rol seleccionado.',
            'area_especialidad_legal.required' => 'El área de especialidad legal es obligatoria para el rol seleccionado.',
            'area_especialidad.in' => 'El valor de la especialidad de salud no es válido.',
            'area_especialidad_legal.in' => 'El valor de la especialidad legal no es válido.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            // Actualizar datos de Persona
            if ($persona) {
                $personaData = $request->only([
                    'nombres', 'primer_apellido', 'segundo_apellido', 'fecha_nacimiento',
                    'sexo', 'estado_civil', 'domicilio', 'telefono', 'zona_comunidad'
                ]);
                $personaData['edad'] = Carbon::parse($request->fecha_nacimiento)->age;

                if ($rolId == 2) {
                    $personaData['area_especialidad'] = $request->input('area_especialidad');
                    $personaData['area_especialidad_legal'] = null;
                } elseif ($rolId == 3) {
                    $personaData['area_especialidad_legal'] = $request->input('area_especialidad_legal');
                    $personaData['area_especialidad'] = null;
                } else {
                    $personaData['area_especialidad'] = null;
                    $personaData['area_especialidad_legal'] = null;
                }
                $persona->update($personaData);
            }

            // Actualizar datos de User
            $userData = []; // Iniciar array vacío
            
            if (optional($user->rol)->nombre_rol !== 'adulto_mayor') {
                $userData['id_rol'] = $request->id_rol;
            }
            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }

            if (!empty($userData)) {
                $user->update($userData);
            }

            DB::commit();
            return redirect()->route('admin.gestionar-usuarios.index')->with('success', 'Usuario actualizado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error al actualizar usuario {$id_usuario}: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->back()->with('error', 'Ocurrió un error al actualizar el usuario.')->withInput();
        }
    }

    /**
     * Elimina un usuario específico de la base de datos (eliminación lógica).
     */
    public function destroy($id_usuario)
    {
        $user = User::findOrFail($id_usuario);

        if (Auth::id() == $id_usuario) {
            // --- CAMBIO: De JSON a redirección con mensaje de error ---
            return redirect()->back()->with('error', 'No puedes eliminar tu propia cuenta.');
        }
        
        if (optional($user->rol)->nombre_rol === 'superadmin') {
            // --- CAMBIO: De JSON a redirección con mensaje de error ---
            return redirect()->back()->with('error', 'No se puede eliminar al superadministrador.');
        }

        DB::beginTransaction();
        try {
            // Eliminar lógicamente al usuario. Laravel se encargará de esto si el modelo usa SoftDeletes.
            $user->delete();
            // Eliminar lógicamente la persona asociada.
            if ($user->persona) {
                // Esto asume que el modelo Persona también usa el trait SoftDeletes.
                $user->persona()->delete();
            }
            DB::commit();
             
            // ===== CAMBIO EN EL MENSAJE =====
            return redirect()->route('admin.gestionar-usuarios.index')->with('success', 'Usuario enviado a la papelera exitosamente.');
            // --- CAMBIO PRINCIPAL: De JSON a redirección con mensaje de éxito ---
           //eturn redirect()->route('admin.gestionar-usuarios.index')->with('success', 'Usuario eliminado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error al eliminar usuario {$id_usuario}: " . $e->getMessage());
            // --- CAMBIO: De JSON a redirección con mensaje de error ---
            return redirect()->route('admin.gestionar-usuarios.index')->with('error', 'Ocurrió un error al eliminar el usuario.');
        }
    }


    /**
     * Activa o desactiva la cuenta de un usuario.
     */
    public function toggleActivity($id_usuario)
    {
        // ... (Este método se mantiene sin cambios)
        $user = User::findOrFail($id_usuario);
        
        if (Auth::id() == $id_usuario) {
            return back()->with('error', 'No puedes desactivar tu propia cuenta.');
        }

        $user->active = !$user->active;
        $user->login_attempts = 0;
        $user->temporary_lockout_until = null;
        $user->save();

        $status = $user->active ? 'activado' : 'desactivado';
        return back()->with('success', "Usuario {$status} exitosamente.");
    }

    /**
     * Muestra la lista de usuarios y adultos mayores eliminados lógicamente.
     */
    public function trash(Request $request)
    {
        // Se obtiene el término de búsqueda desde la URL.
        $searchTerm = $request->input('search');

        // --- Consulta para Usuarios del Sistema eliminados ---
        $deletedUsersQuery = User::onlyTrashed()->with('persona', 'rol');
        
        // --- Consulta para Adultos Mayores eliminados ---
        $deletedAdultosMayoresQuery = AdultoMayor::onlyTrashed()->with('persona');

        // Si hay un término de búsqueda, se aplica el filtro a AMBAS consultas.
        if (!empty($searchTerm)) {
            // Se usa 'ILIKE' para que la búsqueda no distinga mayúsculas/minúsculas en PostgreSQL.
            // Si usas MySQL, puedes cambiarlo por 'LIKE'.
            $deletedUsersQuery->where('ci', 'ILIKE', '%' . $searchTerm . '%');
            $deletedAdultosMayoresQuery->where('ci', 'ILIKE', '%' . $searchTerm . '%');
        }

        // Se ejecutan las consultas para obtener las colecciones finales.
        $deletedUsers = $deletedUsersQuery->get();
        $deletedAdultosMayores = $deletedAdultosMayoresQuery->get();

        // Se pasa la data a la vista.
        return view('Admin.gestionarUsers.trash', compact('deletedUsers', 'deletedAdultosMayores'));
    }

    /**
     * Restaura un registro eliminado lógicamente (Persona y sus roles asociados).
     */
    public function restore($ci)
    {
        DB::beginTransaction();
        try {
            $persona = Persona::onlyTrashed()->where('ci', $ci)->firstOrFail();
            $persona->restore();

            // Intenta restaurar el Usuario del Sistema si existe
            User::onlyTrashed()->where('ci', $ci)->restore();

            // Intenta restaurar el Adulto Mayor si existe
            AdultoMayor::onlyTrashed()->where('ci', $ci)->restore();

            DB::commit();
            return redirect()->route('admin.gestionar-usuarios.trash')->with('success', 'El registro con CI ' . $ci . ' ha sido restaurado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error al restaurar el registro con CI {$ci}: " . $e->getMessage());
            return redirect()->route('admin.gestionar-usuarios.trash')->with('error', 'No se pudo restaurar el registro.');
        }
    }
    // --- FIN DE NUEVOS MÉTODOS ---
}
