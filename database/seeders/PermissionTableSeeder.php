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

            'Modulo_Utilidades_Cursos' => 'Separador de permisos para la gestión de cursos.',

            'Modulo_Cursos' => 'Este permiso habilita el acceso al módulo de gestión de cursos.',
            'Modulo_PlantillaConstancias' => 'Este permiso habilita el acceso al módulo de gestión de plantillas de constancias.',

            'Modulo_Participantes' => 'Acceso al módulo de gestión de participantes.',
            'Create_Participante' => 'Permite crear nuevos participantes.',
            'Edit_Participante' => 'Permite editar información de participantes.',
            'Update_Participante' => 'Permite actualizar datos de participantes.',
            'Delete_Participante' => 'Permite eliminar participantes.',
            'ChangeStatus_Participante' => 'Permite cambiar el estado de un participante.',
            'Api_Participante' => 'Permite acceder a la API de participantes.',

            'Modulo_Ponentes' => 'Acceso al módulo de gestión de ponentes.',
            'Create_Ponentes' => 'Permite crear nuevos ponentes.',
            'Edit_Ponentes' => 'Permite editar información de ponentes.',
            'Update_Ponentes' => 'Permite actualizar datos de ponentes.',
            'Delete_Ponentes' => 'Permite eliminar ponentes.',
            'ChangeStatus_Ponentes' => 'Permite cambiar el estado de un ponente.',

            'Modulo_Utilidades_Utileria' => 'Separador de permisos para la gestión de utilidades.',

            'Modulo_EstadosInscripcion' => 'Acceso al módulo de estados de inscripción.',
            'Create_EstadosInscripcion' => 'Permite crear nuevos estados de inscripción.',
            'Edit_EstadosInscripcion' => 'Permite editar estados de inscripción.',
            'Update_EstadosInscripcion' => 'Permite actualizar estados de inscripción.',
            'Delete_EstadosInscripcion' => 'Permite eliminar estados de inscripción.',
            'ChangeStatus_EstadosInscripcion' => 'Permite cambiar el estado de inscripción.',

            'Modulo_Institucion' => 'Acceso al módulo de gestión de instituciones.',
            'Create_Institucion' => 'Permite crear instituciones.',
            'Edit_Institucion' => 'Permite editar instituciones.',
            'Update_Institucion' => 'Permite actualizar instituciones.',
            'Delete_Institucion' => 'Permite eliminar instituciones.',
            'ChangeStatus_Institucion' => 'Permite cambiar el estado de una institución.',

            'Modulo_MateriasCurso' => 'Acceso al módulo de materias del curso.',
            'Create_MateriasCurso' => 'Permite crear nuevas materias.',
            'Edit_MateriasCurso' => 'Permite editar materias.',
            'Update_MateriasCurso' => 'Permite actualizar materias.',
            'Delete_MateriasCurso' => 'Permite eliminar materias.',
            'ChangeStatus_MateriasCurso' => 'Permite cambiar el estado de una materia.',

            'Modulo_ModalidadesCurso' => 'Acceso al módulo de modalidades del curso.',
            'Create_ModalidadesCurso' => 'Permite crear nuevas modalidades.',
            'Edit_ModalidadesCurso' => 'Permite editar modalidades.',
            'Update_ModalidadesCurso' => 'Permite actualizar modalidades.',
            'Delete_ModalidadesCurso' => 'Permite eliminar modalidades.',
            'ChangeStatus_ModalidadesCurso' => 'Permite cambiar el estado de una modalidad.',

            'Modulo_TiposCurso' => 'Acceso al módulo de tipos de curso.',
            'Create_TiposCurso' => 'Permite crear tipos de curso.',
            'Edit_TiposCurso' => 'Permite editar tipos de curso.',
            'Update_TiposCurso' => 'Permite actualizar tipos de curso.',
            'Delete_TiposCurso' => 'Permite eliminar tipos de curso.',
            'ChangeStatus_TiposCurso' => 'Permite cambiar el estado de un tipo de curso.',

            'Modulo_Estados' => 'Acceso al módulo de estados geográficos.',
            'Create_Estados' => 'Permite crear estados.',
            'Edit_Estados' => 'Permite editar estados.',
            'Update_Estados' => 'Permite actualizar estados.',
            'Delete_Estados' => 'Permite eliminar estados.',

            'Modulo_Municipios' => 'Acceso al módulo de municipios.',
            'Create_Municipios' => 'Permite crear municipios.',
            'Edit_Municipios' => 'Permite editar municipios.',
            'Update_Municipios' => 'Permite actualizar municipios.',
            'Delete_Municipios' => 'Permite eliminar municipios.',

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
