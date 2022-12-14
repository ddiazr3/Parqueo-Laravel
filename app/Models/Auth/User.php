<?php
namespace App\Models\Auth;

use App\Models\Empresa;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
	use Notifiable;

	protected $hidden  = ['password', 'remember_token'];
	protected $guarded = ['id'];
    protected $casts   = [
        'active' => 'boolean',
    ];

	public function sendPasswordResetNotification($token)
    {
		$this->notify(new ResetPasswordNotification($token));
	}

	public function roles()
    {
		return $this->belongsToMany(Role::class, 'user_roles');
	}

    public function empresas()
    {
        return $this->belongsToMany(Empresa::class, 'empresa_users');
    }

	public function roleIds()
    {
		return $this->roles->pluck('id')->toArray();
	}

    public function empresasIds(){
        return $this->empresas->pluck('id')->toArray();
    }

	public function getAvatarAttribute($value)
    {
		if ($value)
        {
			return $value;
		}
		else
        {
			return base64_encode(file_get_contents(public_path() . '/images/user-generic.jpg'));
		}
	}

    public function getUrlPathAttributeFoto(){
        return Storage::url($this->foto);
    }

    public function getRoles($dasboard = false){
        $roles = Role::whereIn('id', Auth::user()
            ->roleIds());
        if($dasboard){
            $roles = $roles->where('ver_dashboard',1);
        }
        $roles = $roles->get();
        return $roles;
    }
}
