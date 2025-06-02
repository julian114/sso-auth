<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Exception;

use Illuminate\Support\Facades\Log;

class UserController extends Controller
{

    public function index()
    {
        $roles = session('user_role', []);
        $permisos = session('user_permissions', []);

        return view('admin.user', [
            'roleGranted' => $roles,
            'permissionsGranted' => $permisos,
        ]);
    }

    public function get()
    {
        try {

            Log::info('UserController@get');

            $user = User::orderBy('id', 'asc')
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'activo' => $user->activo ? 'Activo' : 'Inactivo',
                ];
            });

            $result['status'] = true;
            $result['data'] = $user;
            $result['message'] = 'INFORMACIÓN OBTENIDA';
        } catch (\Exception $e) {
            Log::error('UserController@get - error: ' . $e->getMessage());
            $result['status'] = false;
            $result['data'] = [];
            $result['message'] = 'Error al obtener los datos: ' . $e->getMessage();
        }

        return $result;
    }

    public function create(Request $request)
    {

        Log::info('UserController@create');

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/',
        ]);

        DB::beginTransaction();

        try {
            $user = User::create([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'password' => Hash::make($request->input('password')),
                'activo' => 1,
            ]);

            $user->assignRole('Usuario');

            DB::commit();

            Log::info('UserController@create: ' . json_encode($user));

            $result['status'] = true;
            $result['data'] = $user;
            $result['message'] = 'Registro creado exitosamente.';

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al agregar: ' . $e->getMessage());

            $result['status'] = false;
            $result['data'] = [];
            $result['message'] = 'Hubo un error. Por favor, intente nuevamente.';
        }

        return $result;
    }

    public function edit($id)
    {
        try {
            Log::info('UserController@edit');

            $user = User::find($id);

            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'Datos no encontrados',
                    'data' => [],
                ], 404);
            }

            Log::info('UserController@edit - estado obtenido');

            $result['status'] = true;
            $result['data'] = $user->toArray();
            $result['message'] = 'INFORMACIÓN OBTENIDA';

        } catch (Exception $e) {
            Log::error('Error al obtener el estado: ' . $e->getMessage());

            $result['status'] = false;
            $result['data'] = [];
            $result['message'] = 'ID inválido';
        }

        return $result;
    }

    public function update(Request $request, $id)
    {
        Log::info('UserController@update');

        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Datos no encontrados',
                'data' => [],
            ], 404);
        }

        $request->validate([
            'id' => 'required|integer|exists:users,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/',
        ]);

        DB::beginTransaction();

        try {

            if(!empty($request->input('password'))){
                $user->password = Hash::make($request->input('password'));
            }

            $user->update([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
            ]);

            DB::commit();

            Log::info('UserController@update - Registro actualizado correctamente');

            $result['status'] = true;
            $result['data'] = $user->toArray();
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
        Log::info('UserController@delete');

        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Datos no encontrados',
                'data' => [],
            ], 404);
        }

        DB::beginTransaction();

        try {

            $user->delete();

            DB::commit();
            Log::info('UserController@delete - Registro eliminado correctamente');

            $result['status'] = true;
            $result['data'] = [];
            $result['message'] = 'Registro eliminado exitosamente';
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al eliminar: ' . $e->getMessage());

            $result['status'] = false;
            $result['data'] = [];
            $result['message'] = 'Ocurrió un error al eliminar';
        }

        return $result;
    }

    public function updateStatus($id)
    {
        Log::info('UserController@updateStatus');

        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Datos no encontrados',
                'data' => [],
            ], 404);
        }

        DB::beginTransaction();

        try {
            // Cambiar el valor de 'activo' al opuesto
            $user->activo = $user->activo ? 0 : 1;
            $user->save();

            DB::commit();

            Log::info('UserController@updateStatus - Estatus actualizado correctamente');

            $result['status'] = true;
            $result['data'] = $user->toArray();
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

    public function selectRole($id)
    {
        Log::info('UserController@selectRole');

        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Datos no encontrados',
                'data' => [],
            ], 404);
        }

        $allRole = Role::all();
        $assignedRole = $user->roles()->pluck('id');

        $result['status'] = true;
        $result['data'] = [
            'roles' => $allRole,
            'rolAsignado' => $assignedRole,
        ];
        $result['message'] = 'INFORMACIÓN OBTENIDA';

        return $result;
    }

    public function asigRole(Request $request)
    {
        Log::info('UserController@asigRole');

        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'roles.*' => 'integer|exists:roles,id',
        ]);

        $user = User::find($request->input('user_id'));
        $rol = $request->input('roles');

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Datos no encontrados',
                'data' => [],
            ], 404);
        }

        DB::beginTransaction();

        try {
            $user->syncRoles($rol);

            DB::commit();

            Log::info('UserController@asigRole - Rol sincronizado correctamente');

            return response()->json([
                'status' => true,
                'message' => 'Rol asignado correctamente',
                'data' => [],
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('UserController@asigRole - Error al asignar rol: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Ocurrió un error al asignar los rol',
                'data' => [],
            ], 500);
        }
    }

}
