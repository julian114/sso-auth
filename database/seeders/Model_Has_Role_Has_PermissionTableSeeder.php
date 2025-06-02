<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class Model_Has_Role_Has_PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener el rol Root
        $rootRole = Role::where('name', 'Root')->first();

        if (!$rootRole) {
            $this->command->error('El rol "Root" no existe. Ejecuta primero el seeder de roles.');
            return;
        }

        // Obtener todos los permisos
        $permissions = Permission::all();

        // Asignar todos los permisos al rol Root
        $rootRole->syncPermissions($permissions);

        $this->command->info('Todos los permisos fueron asignados al rol Root.');
    }
}
