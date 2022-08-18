<?php

namespace App\Http\Controllers\Reports;

use App\Exports\TicketsExport;
use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Excel;

class ReporteGeneralController extends Controller
{
    public $path = '/reports/generales';

    public function index()
    {

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
            'empresas' => Empresa::whereIn('id', $empresas)->get(),
            'path' => $this->path
        ];

        return view('component')
            ->withBreadcrumb($breadcrumb)
            ->withTitle('Generador de Reportes')
            ->with('props', $params)
            ->with('component', 'reports-general');
    }

    public function detail(Request $request, $id)
    {
        Log::info("details");
    }

    public function search(Request $request)
    {
        $tickets = self::getData($request, true);
        return response()->json($tickets);
    }

    public function exportExcel(Request $request)
    {
        $tickets = self::getData($request);
        $data = [];
        $header = ["Empresa", "Descripción", "Placa", "Fecha Ingreso", "Fecha Egreso", "Min. Transcurridos", "Hrs. Transcurridos"];
        foreach ($tickets as $ticket) {
            $registro = [
                "empresa" => $ticket->empresa ?? '',
                "descripcion" => $ticket->descripcion ?? '',
                "placa" => $ticket->placa ?? '',
                "fecha_ingreso" => $ticket->fecha_ingreso ?? '',
                "fecha_egreso" => $ticket->fecha_egreso ?? '',
                "minutos" => "$ticket->minutos min." ?? '',
                "horas" => "$ticket->horas hrs." ?? '',
            ];
            array_push($data, $registro);
        }

        $excel = (new TicketsExport(collect($data), $header))->download('Reportes.xlsx', Excel::XLSX);
        return $excel;
    }

    public function exportPdf(Request $request)
    {
        Log::info("export pdf");
        $tickets = self::getData($request);
        $pdf = PDF::loadView('pdf.tickets', ['tickets' => $tickets]);
        return $pdf->download('reporte.pdf');
    }

    static function getData($request, $paginate = false)
    {
        $placa = $request->datasearch["placa"];
        $fechai = $request->datasearch["fechai"];
        $fechaf = $request->datasearch["fechaf"];
        $min = $request->datasearch["min"];
        $empresaIds = $request->datasearch["empresa"];
        $now = Carbon::now();


        $tickets = Ticket::select(
            'tickets.id', 'tickets.descripcion', 'tickets.placa', 'tickets.fecha_ingreso', 'tickets.fecha_egreso',
            'e.empresa',
            DB::raw("(
 	                        CASE WHEN tickets.fecha_egreso is not null
 	                                THEN TIMESTAMPDIFF(MINUTE ,tickets.fecha_ingreso,tickets.fecha_egreso)
 	                                ELSE TIMESTAMPDIFF(MINUTE ,tickets.fecha_ingreso,NOW())
 	                        END
            ) as minutos"),
            DB::raw("(
 	                        CASE WHEN tickets.fecha_egreso is not null
 	                                THEN floor((TIMESTAMPDIFF(MINUTE ,tickets.fecha_ingreso,tickets.fecha_egreso)/60))
 	                                ELSE floor((TIMESTAMPDIFF(MINUTE ,tickets.fecha_ingreso,NOW())/60))
 	                        END
            ) as horas")
        )
            ->join('empresas as e', 'e.id', 'tickets.empresa_id');

        if ($placa) {
            $tickets = $tickets->where('tickets.placa', $placa);
        }
        if ($fechai) {
            $tickets = $tickets->where('tickets.fecha_ingreso', '>=', $fechai);
        }
        if ($fechaf) {
            $tickets = $tickets->where('tickets.fecha_egreso', '>=', $fechaf);
        }
        if ($min) {
            $tickets = $tickets->whereRaw("(
                        CASE WHEN tickets.fecha_egreso is not null
 	                        THEN TIMESTAMPDIFF(MINUTE ,tickets.fecha_ingreso,tickets.fecha_egreso) > $min
 	                        ELSE TIMESTAMPDIFF(MINUTE ,tickets.fecha_ingreso,NOW()) > $min
 	                    END
            )");
        }
        $tickets = $tickets->whereIn("empresa_id", $empresaIds);
        if ($paginate) {
            $tickets = $tickets->paginate(20);
        } else {
            $tickets = $tickets->get();
        }

        return $tickets;
    }
}
