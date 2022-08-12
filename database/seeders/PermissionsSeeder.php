<?php
namespace Database\Seeders;

use App\Models\Auth\Permission;
use Illuminate\Database\Seeder;

class PermissionsSeeder extends Seeder
{
    public function run()
    {
        Permission::truncate();

        Permission::insert([
            [
                'name'        => 'index',
                'parent'      => null,
                'description' => 'Ver',
            ],
            [
                'name'        => 'create',
                'parent'      => null,
                'description' => 'Crear',
            ],
            [
                'name'        => 'store',
                'parent'      => 'create',
                'description' => 'Guardar',
            ],
            [
                'name'        => 'edit',
                'parent'      => null,
                'description' => 'Editar',
            ],
            [
                'name'        => 'update',
                'parent'      => 'edit',
                'description' => 'Actualizar',
            ],
            [
                'name'        => 'destroy',
                'parent'      => null,
                'description' => 'Borrar',
            ],
            [
                'name'        => 'data',
                'parent'      => 'index',
                'description' => 'Obtener datos',
            ],
            [
                'name'        => 'detail',
                'parent'      => 'edit',
                'description' => 'Obtener datos edición',
            ],
            [
                'name'        => 'show',
                'parent'      => null,
                'description' => 'Información',
            ],
        ]);
    }
}
