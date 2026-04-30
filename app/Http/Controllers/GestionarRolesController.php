<?php

namespace App\Http\Controllers;

use App\Models\Rol;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class GestionarRolesController extends Controller
{
    /**
     * Se elimina el constructor y las llamadas a $this->authorize().
     * La protección está manejada en routes/web.php (con 'role:admin')
     * y por las directivas @can en la vista, que ahora funcionarán.
     */

    public function index()
    {
        $roles = Rol::withCount(['users', 'permissions'])
                    ->orderBy('nombre_rol', 'asc')
                    ->get();
        
        return view('Admin.gestionarRoles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::orderBy('name', 'asc')->get();
        return view('Admin.gestionarRoles.create.index', compact('permissions'));
    }

    public function store(Request $request)
    {
        Validator::make($request->all(), [
            'nombre_rol' => 'required|string|max:255|unique:rol,nombre_rol',
            'descripcion' => 'nullable|string|max:500',
            'permissions' => 'nullable|array',
            'permissions.*' => 'integer|exists:permissions,id',
            'active' => 'nullable|boolean',
        ])->validate();

        DB::beginTransaction();
        try {
            $rol = Rol::create($request->only(['nombre_rol', 'descripcion']) + ['active' => $request->boolean('active', false)]);
            $rol->permissions()->sync($request->input('permissions', []));
            DB::commit();
            Log::info("Rol '{$rol->nombre_rol}' creado por usuario: " . Auth::id());

            // CORRECCIÓN: Usar el nombre de ruta completo y correcto de tu archivo web.php
            return redirect()->route('admin.gestionar-roles.index')->with('success', 'Rol creado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error al crear el rol: " . $e->getMessage());
            // CORRECCIÓN: Usar el nombre de ruta completo y correcto de tu archivo web.php
            return redirect()->route('admin.gestionar-roles.create')->with('error', 'Hubo un error al crear el rol.')->withInput();
        }
    }

    public function edit(Rol $rol)
    {
        $permissions = Permission::orderBy('name', 'asc')->get();
        $rolePermissions = $rol->permissions->pluck('id')->toArray();
        return view('Admin.gestionarRoles.editar.edit', compact('rol', 'permissions', 'rolePermissions'));
    }

    public function update(Request $request, Rol $rol)
    {
        Validator::make($request->all(), [
            'nombre_rol' => ['required', 'string', 'max:255', Rule::unique('rol', 'nombre_rol')->ignore($rol->id_rol, 'id_rol')],
            'descripcion' => 'nullable|string|max:500',
            'permissions' => 'nullable|array',
            'permissions.*' => 'integer|exists:permissions,id',
            'active' => 'nullable|boolean',
        ])->validate();

        DB::beginTransaction();
        try {
            $rol->update($request->only(['nombre_rol', 'descripcion']) + ['active' => $request->boolean('active', false)]);
            $rol->permissions()->sync($request->input('permissions', []));
            DB::commit();
            Log::info("Rol '{$rol->nombre_rol}' (ID: {$rol->id_rol}) actualizado por usuario: " . Auth::id());
            // CORRECCIÓN: Usar el nombre de ruta completo y correcto de tu archivo web.php
            return redirect()->route('admin.gestionar-roles.index')->with('success', 'Rol actualizado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error al actualizar el rol (ID: {$rol->id_rol}): " . $e->getMessage());
            // CORRECCIÓN: Usar el nombre de ruta completo y correcto de tu archivo web.php
            return redirect()->route('admin.gestionar-roles.edit', $rol->id_rol)->with('error', 'Hubo un error al actualizar el rol.')->withInput();
        }
    }

    public function destroy(Rol $rol)
    {
        if (in_array(strtolower($rol->nombre_rol), ['admin', 'administrador'])) {
             return redirect()->route('admin.gestionar-roles.index')->with('error', 'Este rol crítico del sistema no puede ser eliminado.');
        }
        if ($rol->users()->count() > 0) {
            return redirect()->route('admin.gestionar-roles.index')->with('error', 'No se puede eliminar el rol porque tiene usuarios asignados.');
        }

        DB::beginTransaction();
        try {
            $rolNombre = $rol->nombre_rol;
            $rol->permissions()->detach();
            $rol->delete();
            DB::commit();
            Log::info("Rol '{$rolNombre}' eliminado por usuario: " . Auth::id());
            // CORRECCIÓN: Usar el nombre de ruta completo y correcto de tu archivo web.php
            return redirect()->route('admin.gestionar-roles.index')->with('success', 'Rol eliminado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error al eliminar el rol (ID: {$rol->id_rol}): " . $e->getMessage());
            return redirect()->route('admin.gestionar-roles.index')->with('error', 'Hubo un error al eliminar el rol.');
        }
    }
}
