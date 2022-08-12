<?php

namespace Database\Seeders;

use App\Models\TipoPlaca;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TipoPlacaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();

        DB::table('tipo_placas')->truncate();

        TipoPlaca::insert([
            ['id' => 1, 'tipo' => 'P-0','descripcion' => 'Particulares'],
            ['id' => 2, 'tipo' => 'M-0','descripcion' => 'Mercantiles'],
            ['id' => 3, 'tipo' => 'C-0','descripcion' => 'Comerciales'],
            ['id' => 4, 'tipo' => 'O-0','descripcion' => 'Oficiales'],
            ['id' => 5, 'tipo' => 'CD-0','descripcion' => 'Cuerpo diplomático'],
            ['id' => 6, 'tipo' => 'DE-0','descripcion' => 'De emergencia'],
            ['id' => 7, 'tipo' => 'DA-0','descripcion' => 'De aprendizaje'],
        ]);

        Schema::enableForeignKeyConstraints();
    }
}
