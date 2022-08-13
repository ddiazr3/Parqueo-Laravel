<?php

namespace App\Http\Controllers\Catalogs;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Models\EmpresaUser;
use Csgt\Cancerbero\Cancerbero;
use Csgt\Crud\CrudController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EmpresaController extends CrudController
{
    public $path = '/catalogs/empresas';

    public function __construct()
    {
        $this->setModel(new Empresa());
        $this->setBreadCrumb([
            ['url' => '', 'title' => 'Catálogos', 'icon' => 'fa fa-book'],
            ['url' => '', 'title' => 'Empresa', 'icon' => 'fa fa-building'],
        ]);
        $this->setTitle('Empresas');
        $this->setField(['name' => 'Nombre', 'field' => 'empresa']);
        $this->setField(['name' => 'Dirección', 'field' => 'direccion']);
        $this->setField(['name' => 'Teléfono', 'field' => 'telefono']);
        $this->middleware(function ($request, $next) {
            if (!Cancerbero::isGod()) {


                $this->setWhereIn('id', Auth::user()->empresasIds());
            }
            return $next($request);
        });
        $this->setPermissions("\Cancerbero::crudPermissions", substr(str_replace('/', '.', $this->path), 1));
    }

    public function detail(Request $request, $id)
    {
        $empresa   = ['empresa' => null, 'direccion' => null, 'telefono' => null, 'logotipo' => null];
        if ($id !== '0') {
            $id   = Crypt::decrypt($id);
            $empresa = Empresa::findOrFail($id);
        }
        return response()->json([
            'empresa'              => $empresa
        ]);
    }

    public function create(Request $request)
    {
        return $this->edit($request, 0);
    }

    public function edit(Request $request, $id)
    {
        $breadcrumb = '<ol class="breadcrumb float-sm-right float-sm-end">
            <li class="breadcrumb-item">Catálogos</li>
            <li class="breadcrumb-item"><a href="' . $this->path . '">Empresas</a></li>
            <li class="breadcrumb-item active">Empresa</li>
        </ol>';

        $params = [
            'id'   => $id,
            'path' => $this->path,
        ];

        return view('component')
            ->withTitle($this->getTitle())
            ->withBreadcrumb($breadcrumb)
            ->with('params', $params)
            ->with('props', $params)
            ->with('component', 'catalogs-empresas-edit');
    }

    public function store(Request $request)
    {
        return $this->update($request, 0);
    }

    public function update(Request $request, $id)
    {
        $rules    = [
            'empresa.empresa'     => 'required',
            'empresa.direccion'    => 'required',
            'empresa.telefono' => 'required',
        ];

        $messages = [
            'empresa.empresa.required'     => 'El nombre es requerido',
            'empresa.direccion.required'    => 'La dirección es requerido',
            'empresa.telefono.required' => 'El télefono es requerido'
        ];

        $request->validate($rules, $messages);

        DB::transaction(function () use ($request, $id) {

            if ($id !== 0) {
                $empressaId = Crypt::decrypt($id);
                $empresa   = Empresa::findOrFail($empressaId);

            } else {
                $empresa = new Empresa;
            }

            $empresa->empresa   = $request->empresa['empresa'];
            $empresa->direccion  = $request->empresa['direccion'];
            $empresa->telefono = $request->empresa['telefono'];
            $empresa->user_creacion_id = Auth::id();
            $empresa->save();

            if(!Cancerbero::isGod()){
                if ($id === 0) {
                    $usuarioEmpresa = new EmpresaUser();
                    $usuarioEmpresa->user_id = Auth::id();
                    $usuarioEmpresa->empresa_id = $empresa->id;
                    $usuarioEmpresa->save();
                }
            }

        });
        Cache::flush();

        return response()->json('ok');
    }

}
