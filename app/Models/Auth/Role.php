<?php
namespace App\Models\Auth;

use App\Models\RoleEmpres;
use Csgt\Cancerbero\Models\RoleModulePermission;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $guarded = ['id'];

    public function role_module_permissions()
    {
        return $this->hasMany(RoleModulePermission::class, 'role_id', 'id');
    }

    public function empresas()
    {
        return $this->hasMany(RoleEmpres::class, 'role_id', 'id');
    }
}
