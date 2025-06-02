<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Exception;

use Illuminate\Support\Facades\Log;

class PermissionController extends Controller
{
    public function index()
    {
        return view('admin.permisos');
    }

    public function get()
    {
        try {

            Log::info('PermissionController@get');

            $permisos = Permission::orderBy('id', 'asc')
            ->get()
            ->map(function ($permisos) {
                return [
                    'id' => $permisos->id,
                    'name' => $permisos->name,
                    'descripcion' => $permisos->descripcion,
                    'activo' => $permisos->activo ? 'Activo' : 'Inactivo',
                ];
            });

            $result['status'] = true;
            $result['data'] = $permisos;
            $result['message'] = 'INFORMACIÓN OBTENIDA';

        } catch (\Exception $e) {

            Log::error('PermissionController@get - error: ' . $e->getMessage());
            $result['status'] = false;
            $result['data'] = [];
            $result['message'] = 'Error al obtener los datos: ' . $e->getMessage();
        }

        return $result;
    }

    public function create(Request $request)
    {
        Log::info('PermissionController@create');

        $request->validate([
            'nombre' => 'required|string|max:255|unique:permissions,name,',
            'descripcion' => 'required|string',
        ]);

        DB::beginTransaction();

        try {
            $permiso = Permission::create([
                'name' => $request->input('nombre'),
                'descripcion' => $request->input('descripcion'),
                'activo' => 1,
            ]);

            DB::commit();

            Log::info('PermissionController@create: ' . json_encode($permiso));

            return [
                'status' => true,
                'data' => $permiso,
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
            Log::info('PermissionController@edit');

            $permiso = Permission::find($id);

            if (!$permiso) {
            return response()->json([
                'status' => false,
                'message' => 'Datos no encontrados',
                'data' => [],
            ], 404);
        }

            Log::info('PermissionController@edit - tipo de permiso obtenido');
            $result['status'] = true;
            $result['data'] = $permiso->toArray();
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
        Log::info('PermissionController@update');

        $permiso = Permission::find($id);

        if (!$permiso) {
            return response()->json([
                'status' => false,
                'message' => 'Datos no encontrados',
                'data' => [],
            ], 404);
        }

        $request->validate([
            'id' => 'required|integer|exists:permissions,id',
            'nombre' => 'required|string|max:255|unique:permissions,name,' . $permiso->id,
            'descripcion' => 'required|string',
        ]);


        DB::beginTransaction();

        try {

            $permiso->update([
                'name' => $request->input('nombre'),
                'descripcion' => $request->input('descripcion'),
            ]);

            DB::commit();

            Log::info('PermissionController@update - Registro actualizado correctamente');

            $result['status'] = true;
            $result['data'] = $permiso->toArray();
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
        Log::info('PermissionController@delete');

        $permiso = Permission::find($id);

        if (!$permiso) {
            return response()->json([
                'status' => false,
                'message' => 'Datos no encontrados',
                'data' => [],
            ], 404);
        }

        if ($permiso->roles()->count() > 0) {
            return response()->json([
                'status' => false,
                'message' => 'No se puede eliminar el permiso porque está siendo utilizado por uno o más roles.',
                'data' => [],
            ], 400);
        }

        DB::beginTransaction();

        try {
            $permiso->delete();

            DB::commit();
            Log::info('PermissionController@delete - Registro eliminado correctamente');

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
        Log::info('PermissionController@updateStatus');

        $permiso = Permission::find($id);

        if (!$permiso) {
            return response()->json([
                'status' => false,
                'message' => 'Datos no encontrados',
                'data' => [],
            ], 404);
        }

        DB::beginTransaction();

        try {
            $permiso->activo = $permiso->activo ? 0 : 1;
            $permiso->save();

            DB::commit();

            Log::info('PermissionController@updateStatus - Estatus actualizado correctamente');

            $result['status'] = true;
            $result['data'] = $permiso->toArray();
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
}
