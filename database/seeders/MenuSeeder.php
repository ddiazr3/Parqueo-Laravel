<?php
namespace Database\Seeders;

use App\Models\Menu\Menu;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;

class MenuSeeder extends Seeder
{
    public function run()
    {
        $sections = new Sections;
        $sections = $sections->get();

        Menu::truncate();

        foreach ($sections as $section) {
            if ($section->menuPermission === 0) {
                continue;
            }
            $menuItem = new Menu;

            $menuItem->icon         = $section->icon;
            $menuItem->name         = $section->name;
            $menuItem->route        = $section->module . (!empty($section->permissions) ? '.' . $section->menuPermission : '');
            $menuItem->order        = $section->menuOrder;
            $menuItem->has_children = empty($section->permissions);
            if ($section->parentModule) {
                $menuItem->parent_route = $section->parentModule;
            }
            $menuItem->save();
        }
        Cache::flush();
    }
}
