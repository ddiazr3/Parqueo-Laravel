<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{

    public function usuarios()
    {
        return $this->belongsToMany(\App\Models\Auth\User::class, 'empresa_users');
    }

    public function usuariosIds(){
        return $this->empresas->pluck('id')->toArray();
    }
}
