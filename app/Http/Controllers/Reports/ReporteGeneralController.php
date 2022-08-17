<?php

namespace App\Http\Controllers\Reports;

use App\Exports\TicketsExport;
use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Models\Ticket;
use PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Excel;

class ReporteGeneralController extends Controller
{
    public $path = '/reports/generales';

    public function index(){

        $breadcrumb = '<ol class="breadcrumb float-sm-right float-sm-end">
            <li class="breadcrumb-item">
            <i class="fa fa-chart-bar"></i>
            Reportes
            </li>
            <li class="breadcrumb-item active"><a href="' . $this->path . '">
            <i class="fa fa-chart-line"></i>
            General</a></li>
        </ol>';

        $empresas = Auth::user()->empresasIds();
        $params = [
            'empresas' => Empresa::whereIn('id',$empresas)->get(),
            'path' => $this->path
        ];

        return view('component')
            ->withBreadcrumb($breadcrumb)
            ->withTitle('Generador de Reportes')
            ->with('props', $params)
            ->with('component', 'reports-general');
    }

    public function detail(Request $request,$id){
        Log::info("details");
    }

    public function search(Request $request){
        $tickets = self::getData($request, true);
        return response()->json($tickets);
    }

    public function exportExcel(Request $request){
        $tickets = self::getData($request);
        $data = [];
        $header = ["Empresa","Descripción","Placa","Fecha Ingreso","Fecha Egreso","Tiempo Transcurrido"];
        foreach ($tickets as $ticket) {
            $registro = [
                "empresa" => $ticket->empresa->empresa ?? '',
                "descripcion" => $ticket->descripcion ?? '',
                "placa" => $ticket->placa ?? '',
                "fecha_ingreso" => $ticket->fecha_ingreso ?? '',
                "fecha_egreso" => $ticket->fecha_egreso ?? '',
            ];
            array_push($data, $registro);
        }

        $excel = (new TicketsExport(collect($data),$header))->download('Reportes.xlsx',Excel::XLSX);
        return $excel;
    }

    public function exportPdf(Request $request){
        Log::info("export pdf");
        $tickets = self::getData($request);
        $pdf = PDF::loadView('pdf.tickets', ['tickets'=>$tickets]);
        return $pdf->download('reporte.pdf');
    }

    static function getData($request,$paginate = false){
        $placa = $request->datasearch["placa"];
        $fechai = $request->datasearch["fechai"];
        $fechaf = $request->datasearch["fechaf"];
        $min = $request->datasearch["min"];
        $empresaIds = $request->datasearch["empresa"];

        $tickets = Ticket::with('empresa');

        if($placa){
            $tickets = $tickets->where('placa',$placa);
        }
        if($fechai){
            $tickets = $tickets->where('fecha_ingreso','>=',$fechai);
        }
        if($fechaf){
            $tickets = $tickets->where('fecha_egreso','>=',$fechaf);
        }
//        if($min){
//            $tickets = $tickets->where('fecha_ingreso','>=',$fechai);
//        }

        $tickets = $tickets->whereIn('empresa_id',$empresaIds);
        if($paginate){
            $tickets = $tickets->paginate(20);
        }else{
            $tickets = $tickets->get();
        }

        return $tickets;
    }
}
