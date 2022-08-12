<?php

namespace App\Http\Controllers;

use App\Models\TipoPlaca;
use Illuminate\Http\Request;

class RegistroController extends Controller
{
    public function index(Request $request){

        $params = [
            'tipoplacas' => TipoPlaca::get(),
            'ingresos' => []
        ];

        return view('component')
            ->withTitle('Ingreso de Vehículo')
            ->with('props', $params)
            ->with('component', 'registrar');
    }
}
