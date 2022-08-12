<?php

namespace App\Models;

use App\Models\Auth\Role;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

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

    public function roleIds()
    {
        return $this->roles->pluck('id')->toArray();
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
}
