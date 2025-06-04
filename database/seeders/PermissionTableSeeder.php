<?php

namespace Database\Seeders;
use Spatie\Permission\Models\Permission;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permisos = [
            'Modulo_Dashboard' => 'Este permiso habilita el acceso al módulo de dashboard principal.',

            'Modulo_Utilidades_Administracion' => 'Separador de permisos para la gestión de administración.',

            'Modulo_Usuario' => 'Acceso al módulo de usuarios.',
            'Create_Usuario' => 'Permite crear usuarios.',
            'Edit_Usuario' => 'Permite editar usuarios.',
            'Update_Usuario' => 'Permite actualizar usuarios.',
            'Delete_Usuario' => 'Permite eliminar usuarios.',
            'ChangeStatus_Usuario' => 'Permite cambiar el estado de un usuario.',
            'AssigRole_Usuario' => 'Permite asignar roles a los usuarios.',

            'Modulo_Rol' => 'Acceso al módulo de roles.',
            'Create_Rol' => 'Permite crear roles.',
            'Edit_Rol' => 'Permite editar roles.',
            'Update_Rol' => 'Permite actualizar roles.',
            'Delete_Rol' => 'Permite eliminar roles.',
            'ChangeStatus_Rol' => 'Permite cambiar el estado de un rol.',
            'AssigPermission_Rol' => 'Permite asignar permisos a los roles.',

            'Modulo_Permiso' => 'Acceso al módulo de permisos.',
            'Create_Permiso' => 'Permite crear permisos.',
            'Edit_Permiso' => 'Permite editar permisos.',
            'Update_Permiso' => 'Permite actualizar permisos.',
            'Delete_Permiso' => 'Permite eliminar permisos.',
            'ChangeStatus_Permiso' => 'Permite cambiar el estado de un permiso.',

            'Modulo_Log' => 'Acceso al módulo de registros y logs del sistema.',
        ];

        foreach ($permisos as $nombre => $descripcion) {
            Permission::updateOrCreate(
                ['name' => $nombre, 'guard_name' => 'web'],
                ['descripcion' => $descripcion, 'activo' => true]
            );
        }
    }
}
