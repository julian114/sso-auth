<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        //Ejecutar el seeder de instituciones
        $this->call([
            UserTableSeeder::class,
            RoleTableSeeder::class,
            PermissionTableSeeder::class,
            Model_Has_Role_Has_PermissionTableSeeder::class,
            Model_Has_RoleTableSeeder::class,

        ]);
        //Comando para ejecutar todos los seeders
        //php artisan migrate --seed
        //Comando para hacer el rollback de todos los seeders
        //php artisan migrate:rollback
    }
}
