<?php

namespace Database\Seeders;

use Spatie\Permission\Models\Role;
use Illuminate\Database\Seeder;

class RoleTableSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Root',
                'guard_name' => 'web',
                'descripcion' => 'Este rol tiene acceso a todas las opciones del sistema.',
                'activo' => true,
            ],
            [
                'name' => 'Administrador',
                'guard_name' => 'web',
                'descripcion' => 'Este rol tiene permisos para la gestión de catálogos',
                'activo' => true,
            ],
            [
                'name' => 'Usuario',
                'guard_name' => 'web',
                'descripcion' => 'Este rol tiene acceso a la gestión de cursos y plantillas',
                'activo' => true,
            ]
        ];

        foreach ($roles as $roleData) {
            Role::firstOrCreate(
                ['name' => $roleData['name']],
                $roleData
            );
        }
    }
}
