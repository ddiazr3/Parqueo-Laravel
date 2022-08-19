<?php

namespace App\Http\Controllers;

use App\Models\Auth\Role;
use App\Models\Empresa;
use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public $path = '/';

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $breadcrumb = '<ol class="breadcrumb float-sm-right float-sm-end">
            <li class="breadcrumb-item active"><a href="' . $this->path . '">/<i class="fa fa-home"></i></a></li>
        </ol>';

        $roles = Auth::user()->getRoles(true);
        $verDashboard = false;
        if (count($roles) > 0) {
            $verDashboard = true;
        }

        $anios = [];
        $meses = [];
        $semanas = [];
        $dias = [];
        $data = [];
        if ($verDashboard) {
            //add Años
            for ($i = 2022; $i <= 2030; $i++) {
                array_push($anios, $i);
            }
            //Add Meses
            $meses = [
                ['id' => 1, 'name' => 'Enero'],
                ['id' => 2, 'name' => 'Febrero'],
                ['id' => 3, 'name' => 'Marzo'],
                ['id' => 4, 'name' => 'Abril'],
                ['id' => 5, 'name' => 'Mayo'],
                ['id' => 6, 'name' => 'Junio'],
                ['id' => 7, 'name' => 'Julio'],
                ['id' => 8, 'name' => 'Agosto'],
                ['id' => 9, 'name' => 'Septiembre'],
                ['id' => 10, 'name' => 'Obtubre'],
                ['id' => 11, 'name' => 'Noviembre'],
                ['id' => 12, 'name' => 'Diciembre'],
            ];
            //Add Semanas
            $semanas = [1, 2, 3, 4];
            //Add dias
            $dias = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31];

            $data = self::getData();
        }

        $prop = [
            'verdashboard' => $verDashboard,
            'anios' => $anios,
            'meses' => $meses,
            'semanas' => $semanas,
            'dias' => $dias,
            'empresas' => $data
        ];

        return view('component')
            ->withBreadcrumb($breadcrumb)
            ->withTitle("Bienvenido")
            ->with('props', $prop)
            ->with('component', 'dashboard');
    }

    public function search(Request $request,$limpiar)
    {

        Log::info("Empresa Id");
        $data = self::getData(false, $request->search,$limpiar);
        Log::info($data);
//
        return response()->json($data);
    }

    static function getData($index = true, $search = null, $limpiar = 0)
    {
        $now = Carbon::now()->format('Y-m-d');
        $anio = Carbon::now()->year;
        $mes = null;
        $semana = null;
        $dia = null;

        $anioFilter = null;
        $mesFilter = null;
        $semanaFilter = null;
        $diaFilter = null;
        if (!$index) {
            //codigo para buscar
            $id = $search["id"];
            $anio = !$limpiar ? $search["anio"] : $anio;
            $mes = !$limpiar ? $search["mes"] : null;
            $semana = !$limpiar ? $search["semana"] : null;
            $dia = !$limpiar ? $search["dia"] : null;

            $anioFilter = $anio;
            $mesFilter = $mes;
            $semanaFilter = $semana;
            $diaFilter = $dia;


            if ($semana) {
                //Obtenemos el numero de semana del primer dia del mes a filtrar
                $semanasAnio = Carbon::parse("$anio-$mes-01")->weekOfYear;
                switch ($semana) {
                    case 1:
                        $semana = $semanasAnio;
                        break;
                    case 2:
                        $semana = $semanasAnio + 1;
                        break;
                    case 3:
                        $semana = $semanasAnio + 2;
                        break;
                    case 4:
                        $semana = $semanasAnio + 3;
                        break;
                }
            }
            Log::info("id $id anio $anio mes $mes semana $semana dia $dia");
            $empresas = Empresa::where('id', $id)->get();
        } else {
            $empresas = Empresa::whereIn('id', Auth::user()->empresasIds())->get();
        }
        $data = [];
        foreach ($empresas as $empresa) {
            //$empresa->empresa = 'test';
            $tickets = Ticket::select(DB::raw('MONTHNAME(created_at) as namemes,COUNT(*) as ingresos'))
                ->where('empresa_id', $empresa->id)
                ->whereRaw("YEAR(created_at) = $anio");
            if ($mes) {
                $tickets = $tickets->whereRaw("MONTH(created_at) = $mes");
            }
            if ($semana) {
                $tickets = $tickets->whereRaw("WEEKOFYEAR(created_at) = $semana");
            }
            if ($dia) {
                $tickets = $tickets->whereRaw("DAY(created_at) = $dia");
            }
            $tickets = $tickets
                ->groupBy(DB::raw('MONTHNAME(created_at)'))
                ->get();

            $mesesArray = [];
            $dataArray = [];
            foreach ($tickets as $ticket) {
                array_push($mesesArray, trans("meses." . strtoupper($ticket->namemes)));
                array_push($dataArray, $ticket->ingresos);
            }

            $tickets2 = Ticket::where('empresa_id', $empresa->id)
                ->where('created_at', '>', Carbon::parse($now)->subDay())
                ->get()
                ->count();
            $dataTmp = [
                "chartDataBar" => [
                    "labels" => $mesesArray,
                    "datasets" => [[
                        "data" => $dataArray,
                        "backgroundColor" => '#f87979',
                        "label" => "Total Ingresos"
                    ]]
                ],
                "chartDataDoung" => [
                    "labels" => ["Ingresos"],
                    "datasets" => [[
                        "data" => [$tickets2],
                        "backgroundColor" => '#41B883'
                    ]]
                ],
                "search" => [
                    "anio" => $anioFilter,
                    "mes" => $mesFilter,
                    "semana" => $semanaFilter,
                    "dia" => $diaFilter,
                    "id" => $empresa->id
                ],
                "empresa" => $empresa
            ];

            array_push($data, $dataTmp);
        }

        return $data;
    }


}
