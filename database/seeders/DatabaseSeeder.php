<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */

    public function run()
    {
        Model::unguard();
        Schema::disableForeignKeyConstraints();
        $this->call([
            ModulesSeeder::class,
            PermissionsSeeder::class,
            ModulePermissionsSeeder::class,
            MenuSeeder::class,
            EstadoTicketsSeeder::class,
            TipoPlacaSeeder::class
        ]);
        Schema::enableForeignKeyConstraints();
        Model::reguard();

        Cache::flush();
    }
}
