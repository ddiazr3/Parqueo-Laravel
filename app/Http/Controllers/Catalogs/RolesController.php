<?php

namespace App\Http\Controllers\Catalogs;

use App\Models\Auth\UserRole;
use App\Models\Empresa;
use App\Models\EmpresaUser;
use App\Models\RoleEmpres;
use DB;
use Cache;
use Crypt;
use App\Models\Auth\Role;
use App\Models\Auth\Module;
use Illuminate\Http\Request;
use Csgt\Crud\CrudController;
use App\Models\Auth\Permission;
use App\Models\Auth\ModulePermission;
use App\Models\Auth\RoleModulePermission;
use Illuminate\Support\Facades\Auth;
use Csgt\Cancerbero\Cancerbero;
use Illuminate\Support\Facades\Log;

class RolesController extends CrudController
{
    public $path = '/catalogs/roles';

    public function __construct()
    {
        $this->setModel(new Role);
        $this->setTitle('Roles');
        $this->setBreadCrumb([
            ['url' => '', 'title' => 'Catálogos', 'icon' => 'fa fa-book'],
            ['url' => '', 'title' => 'Roles', 'icon' => 'fa fa-key'],
        ]);
        $this->setField(['name' => 'Nombre', 'field' => 'name']);
        $this->setField(['name' => 'Descripción', 'field' => 'description']);
        $this->middleware(function ($request, $next) {
            if (!Cancerbero::isGod()) {
                $empresasIds = Auth::user()->empresasIds();
                $rolesIds = RoleEmpres::whereIn('empresa_id', $empresasIds)
                    ->where('role_id','<>', Cancerbero::godRole())
                    ->pluck('role_id');
                $this->setWhereIn('id', $rolesIds);
            }
            return $next($request);
        });
        $this->setPermissions("\Cancerbero::crudPermissions", substr(str_replace('/', '.', $this->path), 1));
    }

    public function detail(Request $request, $id)
    {
        $rmpids = [];
        $role = ['name' => null, 'description' => null, 'role_module_permissions' => [], 'empresas_ids' => []];

        if ($id !== '0') {
            $id = Crypt::decrypt($id);
            $role = Role::with('role_module_permissions:id,role_id,module_permission', 'empresas')
                ->findOrFail($id);

            $rmpids = $role->role_module_permissions->map(function ($rmp) {
                return $rmp->module_permission;
            })->toArray();

            $empresaIds = $role->empresas->map(function ($empresa) {
                return $empresa->id;
            })->toArray();

            $role->empresas_ids = $empresaIds;
        }

        $permissions = Permission::orderBy('name')->get();
        $modules = Module::orderBy('name')->get();

        $modulepermissions = ModulePermission::query()
            ->orderBy('name')
            ->get()
            ->map(function ($mp) use ($modules, $permissions, $rmpids) {
                $mp->m = $modules->first(function ($m) use ($mp) {
                    return $m->name == $mp->module;
                });

                $mp->p = $permissions->first(function ($p) use ($mp) {
                    return $p->name == $mp->permission;
                });

                $mp->enabled = in_array($mp->name, $rmpids);

                return $mp;
            })
            ->sortBy('m.description')
            ->groupBy('m.description');

        $empresas = Empresa::orderBy('empresa');
        if (!Cancerbero::isGod()) {
            $empresas = $empresas->whereIn('id', Auth::user()->empresasIds());
        }
        $empresas = $empresas->get();

        return response()->json([
            'role' => $role,
            'empresas' => $empresas,
            'modulepermissions' => $modulepermissions,
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
            <li class="breadcrumb-item"><a href="' . $this->path . '">Roles</a></li>
            <li class="breadcrumb-item active">Rol</li>
        </ol>';

        $params = [
            'id' => $id,
            'path' => $this->path,
        ];

        return view('component')
            ->withTitle($this->getTitle())
            ->withBreadcrumb($breadcrumb)
            ->with('params', $params)
            ->with('props', $params)
            ->with('component', 'catalogs-roles-edit');
    }

    public function store(Request $request)
    {
        return $this->update($request, 0);
    }

    public function update(Request $request, $id)
    {
        $rules = [
            'role.name' => 'required',
            'role.description' => 'required',
        ];

        $messages = [
            'role.name.required' => 'El nombre es requerido',
            'role.description.required' => 'La descripción es requerida',
        ];

        $request->validate($rules, $messages);

        DB::transaction(function () use ($request, $id) {

            if ($id !== 0) {
                $roleid = Crypt::decrypt($id);
                $role = Role::findOrFail($roleid);

            } else {
                $role = new Role;
            }

            $role->name = $request->role['name'];
            $role->description = $request->role['description'];
            $role->save();

            RoleModulePermission::where('role_id', $role->id)->delete();
            RoleEmpres::where('role_id', $role->id)->delete();

            foreach ($request->modulepermissions as $modules) {
                foreach ($modules as $mp) {
                    if ($mp['enabled']) {
                        $rmp = new RoleModulePermission;
                        $rmp->role_id = $role->id;
                        $rmp->module_permission = $mp['name'];
                        $rmp->save();
                    }
                }
            }

            Log::info($request->role['empresas_ids']);
            if(count($request->role['empresas_ids']) > 0){
                foreach ($request->role['empresas_ids'] as $empresa) {
                    $rmp = new RoleEmpres;
                    $rmp->role_id = $role->id;
                    $rmp->empresa_id = $empresa;
                    $rmp->save();
                }
            }

        });

        Cache::flush();

        return response()->json('ok');
    }
}
