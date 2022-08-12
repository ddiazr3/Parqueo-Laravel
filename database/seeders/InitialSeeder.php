<?php
namespace Database\Seeders;

use App\Models\Auth\Role;
use App\Models\Auth\User;
use App\Models\Auth\UserRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class InitialSeeder extends Seeder
{
    public function run()
    {
        Schema::disableForeignKeyConstraints();

        Role::insert([
            'id'          => 1,
            'name'        => 'Backdoor',
            'description' => 'Rol Backdoor',
            'created_at'  => date_create(),
            'updated_at'  => date_create(),
        ]);

        User::insert([
            'id'         => 1,
            'email'      => 'danylen1@hotmail.com',
            'password'   => '$2y$10$t3NFT.NsnAZWlLbaVOJhl.Ofpcymyj3Q5c1GXG4WLpACZRYKbgziK',
            'name'       => 'Dany Díaz',
            'active'     => 1,
            'created_at' => date_create(),
            'updated_at' => date_create(),
        ]);

        UserRole::insert([
            'user_id'    => 1,
            'role_id'    => 1,
            'created_at' => date_create(),
            'updated_at' => date_create(),
        ]);

        DB::statement('INSERT INTO role_module_permissions (role_id, module_permission, created_at, updated_at)
				SELECT 1, name, NOW(), NOW() FROM module_permissions');

        Schema::enableForeignKeyConstraints();
    }
}
