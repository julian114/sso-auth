<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Exception;

use Illuminate\Support\Facades\Log;

class RoleController extends Controller
{
    public function index()
    {
        return view('admin.roles');
    }

    public function get()
    {
        try {

            Log::info('RoleController@get');

            $roles = Role::orderBy('id', 'asc')
            ->get()
            ->map(function ($roles) {
                return [
                    'id' => $roles->id,
                    'name' => $roles->name,
                    'descripcion' => $roles->descripcion,
                    'activo' => $roles->activo ? 'Activo' : 'Inactivo',
                ];
            });

            $result['status'] = true;
            $result['data'] = $roles;
            $result['message'] = 'INFORMACIÓN OBTENIDA';

        } catch (\Exception $e) {

            Log::error('RoleController@get - error: ' . $e->getMessage());
            $result['status'] = false;
            $result['data'] = [];
            $result['message'] = 'Error al obtener los datos: ' . $e->getMessage();
        }

        return $result;
    }

    public function create(Request $request)
    {
        Log::info('RoleController@create');

        $request->validate([
            'nombre' => 'required|string|max:255|unique:roles,name,',
            'descripcion' => 'required|string',
        ]);

        DB::beginTransaction();

        try {
            $rol = Role::create([
                'name' => $request->input('nombre'),
                'descripcion' => $request->input('descripcion'),
                'activo' => 1,
            ]);

            DB::commit();

            Log::info('RoleController@create: ' . json_encode($rol));

            return [
                'status' => true,
                'data' => $rol,
                'message' => 'Registro creado exitosamente.',
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al agregar: ' . $e->getMessage());

            return [
                'status' => false,
                'data' => [],
                'message' => 'Hubo un error. Por favor, intente nuevamente.',
            ];
        }
    }

    public function edit($id)
    {
        try {
            Log::info('RoleController@edit');

            $rol = Role::find($id);

            if (!$rol) {
            return response()->json([
                'status' => false,
                'message' => 'Datos no encontrados',
                'data' => [],
            ], 404);
        }

            Log::info('RoleController@edit - tipo de rol obtenido');
            $result['status'] = true;
            $result['data'] = $rol->toArray();
            $result['message'] = 'INFORMACIÓN OBTENIDA';

        } catch (Exception $e) {
            Log::error('Error al obtener el tipo de curso: ' . $e->getMessage());
            $result['status'] = false;
            $result['data'] = [];
            $result['message'] = 'ID inválido';
        }

        return $result;
    }

    public function update(Request $request, $id)
    {
        Log::info('RoleController@update');

        $rol = Role::find($id);

        if (!$rol) {
            return response()->json([
                'status' => false,
                'message' => 'Datos no encontrados',
                'data' => [],
            ], 404);
        }

        $request->validate([
            'id' => 'required|integer|exists:roles,id',
            'nombre' => 'required|string|max:255|unique:roles,name,'. $rol->id,
            'descripcion' => 'required|string',
        ]);


        DB::beginTransaction();

        try {

            $rol->update([
                'name' => $request->input('nombre'),
                'descripcion' => $request->input('descripcion'),
            ]);

            DB::commit();

            Log::info('RoleController@update - Registro actualizado correctamente');

            $result['status'] = true;
            $result['data'] = $rol->toArray();
            $result['message'] = 'Registro actualizado exitosamente';

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar: ' . $e->getMessage());

            $result['status'] = false;
            $result['data'] = [];
            $result['message'] = 'Ocurrió un error al actualizar';
        }

        return $result;
    }

    public function delete($id)
    {
        Log::info('RoleController@delete');

        $rol = Role::find($id);

        if (!$rol) {
            return response()->json([
                'status' => false,
                'message' => 'Datos no encontrados',
                'data' => [],
            ], 404);
        }

        if ($rol->users()->count() > 0) {
            return response()->json([
                'status' => false,
                'message' => 'No se puede eliminar el rol porque está siendo utilizado por uno o más usuarios.',
                'data' => [],
            ], 400);
        }

        DB::beginTransaction();

        try {
            $rol->delete();

            DB::commit();
            Log::info('RoleController@delete - Registro eliminado correctamente');

            return response()->json([
                'status' => true,
                'message' => 'Registro eliminado exitosamente',
                'data' => [],
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al eliminar permiso: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Ocurrió un error al eliminar el permiso',
                'data' => [],
            ], 500);
        }
    }

    public function updateStatus($id)
    {
        Log::info('RoleController@updateStatus');

        $rol = Role::find($id);

        if (!$rol) {
            return response()->json([
                'status' => false,
                'message' => 'Datos no encontrados',
                'data' => [],
            ], 404);
        }

        DB::beginTransaction();

        try {
            $rol->activo = $rol->activo ? 0 : 1;
            $rol->save();

            DB::commit();

            Log::info('RoleController@updateStatus - Estatus actualizado correctamente');

            $result['status'] = true;
            $result['data'] = $rol->toArray();
            $result['message'] = 'Registro actualizado exitosamente';

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar: ' . $e->getMessage());

            $result['status'] = false;
            $result['data'] = [];
            $result['message'] = 'Ocurrió un error al actualizar';
        }

        return $result;
    }

    public function selectPermissions($id)
    {
        Log::info('RoleController@selectPermissions');

        $rol = Role::find($id);

        if (!$rol) {
            return response()->json([
                'status' => false,
                'message' => 'Datos no encontrados',
                'data' => [],
            ], 404);
        }

        $allPermissions = Permission::all();
        $assignedPermissions = $rol->permissions()->pluck('id');

        $result['status'] = true;
        $result['data'] = [
            'permisos' => $allPermissions,
            'permisosAsignados' => $assignedPermissions,
        ];
        $result['message'] = 'INFORMACIÓN OBTENIDA';

        return $result;
    }

    public function asigPermissions(Request $request)
    {
        Log::info('RoleController@asigPermissions');

        $request->validate([
            'rol_id' => 'required|integer|exists:roles,id',
            'permisos' => 'array',
            'permisos.*' => 'integer|exists:permissions,id',
        ]);

        $rol = Role::find($request->input('rol_id'));
        $permisos = $request->input('permisos');

        if (!$rol) {
            return response()->json([
                'status' => false,
                'message' => 'Datos no encontrados',
                'data' => [],
            ], 404);
        }

        DB::beginTransaction();

        try {
            $rol->syncPermissions($permisos);

            DB::commit();

            Log::info('RoleController@asigPermissions - Permisos sincronizados correctamente');

            return response()->json([
                'status' => true,
                'message' => 'Permisos asignados correctamente',
                'data' => [],
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('RoleController@asigPermissions - Error al asignar permisos: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Ocurrió un error al asignar los permisos',
                'data' => [],
            ], 500);
        }
    }
    

}
