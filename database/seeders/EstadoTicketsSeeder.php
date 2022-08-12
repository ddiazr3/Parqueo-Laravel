<?php

namespace Database\Seeders;

use App\Models\EstadoTicket;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class EstadoTicketsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();

        DB::table('estado_tickets')->truncate();

        EstadoTicket::insert([
            ['id' => 1,'estado' => 'ingreso'],
            ['id' => 2,'estado' => 'saliente'],
            ['id' => 3,'estado' => 'cancelado']
        ]);

        Schema::enableForeignKeyConstraints();
    }
}
