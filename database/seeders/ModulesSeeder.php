<?php
namespace Database\Seeders;

use App\Models\Auth\Module;
use Illuminate\Database\Seeder;

class ModulesSeeder extends Seeder
{
    public function run()
    {
        $sections = new Sections;

        $modules = $sections->get()->filter(function ($section) {
            return $section->permissions !== [];
        });

        $insertModules = collect($modules)->map(function ($module) {
            return ['name' => $module->module, 'description' => $module->description];
        })->toArray();

        Module::truncate();
        Module::insert($insertModules);
    }
}
