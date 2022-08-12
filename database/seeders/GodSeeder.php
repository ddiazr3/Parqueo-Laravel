<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Auth\RoleModulePermission;

class GodSeeder extends Seeder
{
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        RoleModulePermission::where('role_id', 1)->delete();
        DB::statement('INSERT INTO role_module_permissions (role_id, module_permission, created_at, updated_at)
                SELECT 1, name, NOW(), NOW() FROM module_permissions');

        Schema::enableForeignKeyConstraints();
    }
}
