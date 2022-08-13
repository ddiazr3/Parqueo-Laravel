<?php

namespace App\Http\Controllers;

use App\Events\EgresoTicketEvent;
use App\Events\IngresoTicketEvent;
use App\Models\Ticket;
use App\Models\TipoPlaca;
use Carbon\Carbon;
use Csgt\Cancerbero\Cancerbero;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

class RegistroController extends Controller
{
    public $path = '/registros';

    public function index(Request $request)
    {
        $breadcrumb = '<ol class="breadcrumb float-sm-right float-sm-end">
            <li class="breadcrumb-item"></li>
            <li class="breadcrumb-item active">
            <i class="fa fa-car"></i>
            <a href="' . $this->path . '">Registrar</a></li>
        </ol>';

        $fechaActual = Carbon::now()->subDays(1);
        $tickets = Ticket::with('empresa')->where('created_at', '>=', $fechaActual);
        if (!Cancerbero::isGod()) {
            $tickets = $tickets->whereIn('empresa_id', Auth::user()->empresasIds());
        }
        $tickets = $tickets->whereNotIn('estado_ticket_id',[2,3])->get();

        $params = [
            'tipoplacas' => TipoPlaca::get(),
            'ingresos' => $tickets,
            'path' => $this->path,
            'emid' => Auth::user()->empresasIds()
        ];
        return view('component')
            ->withTitle('Ingreso de Vehículo')
            ->withBreadcrumb($breadcrumb)
            ->with('props', $params)
            ->with('component', 'registrar');
    }

    public function store(Request $request)
    {

        //event(new IngresoTicketEvent([],Auth::user()->empresasIds()));
        $rules = [
            'tipoplaca' => 'required',
            'numeroplaca' => 'required',
        ];

        $messages = [
            'tipoplaca.required' => 'El tipo de placa es requerido',
            'numeroplaca.required' => 'El número de placa es requerido'
        ];

        $request->validate($rules, $messages);

        if (count(Auth::user()->empresasIds()) > 1) {
            abort(501, "Tiene más de una empresa ligada a su usario");
        }

        $empresaId = Auth::user()->empresasIds();

        $ticket = new Ticket();
        $ticket->empresa_id = $empresaId[0];
        $ticket->user_creacion_id = Auth::id();
        $ticket->tipo_placa_id = $request->tipoplaca;
        $ticket->estado_ticket_id = 1;
        $ticket->placa = $request->numeroplaca;
        $ticket->descripcion = $request->descripcion;
        $ticket->fecha_ingreso = Carbon::now();
        $ticket->save();

        $ticketRetur = Ticket::with('empresa')->find($ticket->id);

        broadcast(new IngresoTicketEvent($ticketRetur,$empresaId[0]))->toOthers();
       // event(new IngresoTicketEvent($ticketRetur,$empresaId[0]));
        return response()->json($ticketRetur);

    }

    public function cancelar($id){
        if (count(Auth::user()->empresasIds()) > 1) {
            abort(501, "Tiene más de una empresa ligada a su usario");
        }
        $ticket = Ticket::find($id);
        $ticket->estado_ticket_id = 3;
        $ticket->fecha_egreso = Carbon::now();
        $ticket->user_salida_id = Auth::id();
        $ticket->update();
        $empresaId = Auth::user()->empresasIds();
        broadcast(new EgresoTicketEvent($ticket->id,$empresaId[0]))->toOthers();
        $data = [ 'ok' => $id];
        return response()->json($data);
    }

    public function salir($id){
        if (count(Auth::user()->empresasIds()) > 1) {
            abort(501, "Tiene más de una empresa ligada a su usario");
        }
        $ticket = Ticket::find($id);
        $ticket->estado_ticket_id = 2;
        $ticket->fecha_egreso = Carbon::now();
        $ticket->user_salida_id = Auth::id();
        $ticket->update();
        $data = [ 'ok' => $id];
        $empresaId = Auth::user()->empresasIds();
        broadcast(new EgresoTicketEvent($ticket->id,$empresaId[0]))->toOthers();
        return response()->json($data);
    }
}
