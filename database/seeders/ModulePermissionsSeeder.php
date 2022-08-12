<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Auth\ModulePermission;

class ModulePermissionsSeeder extends Seeder
{
    private $inserts = [];
    public function run()
    {
        $arr = [
            '1' => 'index',
            '2' => 'create',
            '3' => 'store',
            '4' => 'edit',
            '5' => 'update',
            '6' => 'destroy',
            '7' => 'data',
            '8' => 'detail',
            '9' => 'show',
        ];
        $sections = new Sections;
        $sections = $sections->get()->filter(function ($section) {
            return $section->permissions !== [];
        });

        ModulePermission::truncate();

        $insertPermissions = $sections->map(function ($section, $arr) {
            return collect($section->permissions)->map(function ($permission) use ($section, $arr) {

                if (is_numeric($permission)) {
                    $permission = $arr[$permission];
                }

                return $section->module . '.' . $permission;
            });
        })->flatten()->map(function ($item) {
            return ['name' => $item];
        })->toArray();

        ModulePermission::insert($insertPermissions);
    }
}
