<?php

namespace App\Http\Controllers\Catalogs;

use App\Models\Empresa;
use App\Models\EmpresaUser;
use App\Models\RoleEmpres;
use DB;
use Hash;
use Cache;
use Crypt;
use App\Models\Auth\Role;
use App\Models\Auth\User;
use Illuminate\Http\Request;
use App\Models\Auth\UserRole;
use Csgt\Crud\CrudController;
use Csgt\Cancerbero\Cancerbero;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UsersController extends CrudController
{
    public $path = '/catalogs/users';

    public function __construct()
    {
        $this->setModel(new User);
        $this->setTitle('Usuarios');
        $this->setBreadCrumb([
            ['url' => '', 'title' => 'Catálogos', 'icon' => 'fa fa-book'],
            ['url' => '', 'title' => 'Usuarios', 'icon' => 'fa fa-user'],
        ]);

        $this->setField(['name' => 'Nombre', 'field' => 'name']);
        $this->setField(['name' => 'Email', 'field' => 'email']);
        $this->setField(['name' => 'Creado', 'field' => 'created_at', 'type' => 'datetime']);
        $this->setField(['name' => 'Super User', 'field' => 'super', 'type' => 'bool']);
        $this->setField(['name' => 'Activo', 'field' => 'active', 'type' => 'bool']);

        $this->middleware(function ($request, $next) {
            if (!Cancerbero::isGod()) {
                $ids = UserRole::where('role_id', Cancerbero::godRole())->pluck('user_id')->toArray();

                $empresasIds = Auth::user()->empresasIds();

                $usuariosIds = EmpresaUser::whereIn('empresa_id', $empresasIds);
                if (!empty($ids)) {
                    $usuariosIds = $usuariosIds->whereNotIn('user_id', $ids);
                }
                $usuariosIds = $usuariosIds->pluck('user_id');

                #Si el usuario logueado es superAdmin ve todos los usuarios ligados a sus empresas
                if(!Auth::user()->super){
                    $this->setWhere('super', 0);
                }
                $this->setWhereIn('id', $usuariosIds);
            }
            return $next($request);
        });

        $this->setPermissions("\Cancerbero::crudPermissions", substr(str_replace('/', '.', $this->path), 1));
    }

    public function detail(Request $request, $id)
    {
        $roleIds = [];
        $user = [
            'name' => null,
            'email' => null,
            'active' => true,
            'super' => false,
            'role_ids' => [],
            'impresora' => false,
            'nombre_impresora' => null,
            'empresas_ids' => []
        ];

        if ($id !== '0') {
            $id = Crypt::decrypt($id);
            $user = User::with('roles', 'empresas')->findOrFail($id);

            $roleIds = $user->roles->map(function ($role) {
                return $role->id;
            })->toArray();

            $empresaIds = $user->empresas->map(function ($empresa) {
                return $empresa->id;
            })->toArray();

            $user->role_ids = $roleIds;
            $user->empresas_ids = $empresaIds;
        }

        $roles = Role::orderBy('name');
        $isSuper = true;
        if (!Cancerbero::isGod()) {
            $isSuper = false;
            $empresasIds = Auth::user()->empresasIds();
            $rolesIds = RoleEmpres::whereIn('empresa_id', $empresasIds)
                ->where('role_id','<>', Cancerbero::godRole())
                ->pluck('role_id');
            $roles->whereIn('id', $rolesIds);
        }
        $roles = $roles->get();

        $empresas = Empresa::orderBy('empresa');
        if (!Cancerbero::isGod()) {
            $empresas = $empresas->whereIn('id', Auth::user()->empresasIds());
        }
        $empresas = $empresas->get();

        return response()->json([
            'user' => $user,
            'empresas' => $empresas,
            'roles' => $roles,
            'isSuper' => $isSuper,
            'changePassword' => false,
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
            <li class="breadcrumb-item"><a href="' . $this->path . '">Usuarios</a></li>
            <li class="breadcrumb-item active">Usuario</li>
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
            ->with('component', 'catalogs-users-edit');
    }

    public function store(Request $request)
    {
        return $this->update($request, 0);
    }

    public function update(Request $request, $id)
    {
        $savePass = ($id === 0 || $request->changePassword);
        $print = $request->user['impresora'];
        $rules = [
            'user.name' => 'required',
            'user.email' => 'required|unique:users,email' . ($id !== 0 ? ',' . Crypt::decrypt($id) : ''),
            'user.role_ids' => 'required|min:1',
            'user.empresas_ids' => 'required|min:1',
        ];

        $messages = [
            'user.name.required' => 'El nombre es requerido',
            'user.email.required' => 'El email es requerido',
            'user.role_ids.required' => 'Al menos debe seleccionar un rol',
            'user.role_ids.min' => 'Al menos debe seleccionar un rol',
            'user.empresas_ids.required' => 'Al menos debe seleccionar una empresa',
            'user.empresas_ids.min' => 'Al menos debe seleccionar una empresa',
        ];

        if ($savePass) {
            $rules['user.password'] = 'required|confirmed';
            $messages['user.password.required'] = 'La password es requerida';
            $messages['user.password.confirmed'] = 'Las passwords no concuerdan';
        }

        if ($print) {
            $rules['user.nombre_impresora'] = 'required';
            $messages['user.nombre_impresora.required'] = 'Nombre de la impresora es requerida';
        }

        $request->validate($rules, $messages);

        DB::transaction(function () use ($request, $id, $savePass) {

            if ($id !== 0) {
                $userId = Crypt::decrypt($id);
                $user = User::findOrFail($userId);

            } else {
                $user = new User;
            }

            $user->name = $request->user['name'];
            $user->email = $request->user['email'];
            $user->active = $request->user['active'];
            $user->super = $request->user['super'];
            $user->impresora = $request->user['impresora'];
            $user->nombre_impresora = $request->user['nombre_impresora'];
            if ($savePass) {
                $user->password = Hash::make($request->user['password']);
            }
            $user->save();

            UserRole::where('user_id', $user->id)->delete();
            EmpresaUser::where('user_id', $user->id)->delete();

            foreach ($request->user['role_ids'] as $roleId) {
                $ur = new UserRole;
                $ur->role_id = $roleId;
                $ur->user_id = $user->id;
                $ur->save();
            }

            foreach ($request->user['empresas_ids'] as $empresaId) {
                $eu = new EmpresaUser;
                $eu->empresa_id = $empresaId;
                $eu->user_id = $user->id;
                $eu->save();
            }
        });
        Cache::flush();

        return response()->json('ok');
    }
}
