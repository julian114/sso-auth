<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class Model_Has_RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener el usuario con ID 1
        $user = User::find(1);

        if (!$user) {
            $this->command->error('El usuario con ID 1 no existe.');
            return;
        }

        // Obtener el rol Root
        $rootRole = Role::where('name', 'Root')->first();

        if (!$rootRole) {
            $this->command->error('El rol "Root" no existe. Ejecuta primero el seeder de roles.');
            return;
        }

        // Asignar el rol Root al usuario
        $user->assignRole($rootRole);

        $this->command->info('El rol "Root" fue asignado correctamente al usuario con ID 1.');
    }
}
