<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{

    public function empresa(){
        return $this->hasOne(Empresa::class,'id','empresa_id');
    }

}
