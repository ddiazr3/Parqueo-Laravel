<?php
namespace App\Http\Controllers;

use App\Events\PresenceEvent;
use App\Events\PrivateEvent;
use App\Events\TestEvent;
use Auth;
use Hash;
use App\Models\Auth\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{
    public $path = '/profile';

    public function index(Request $request)
    {
       Log::info("Index del perfil ".auth()->user()->id);
        return view('component')
            ->withTitle('Perfil')
            ->with('component', 'profile');
    }

    public function detail(Request $request, $id)
    {
        $user = User::findOrFail(Auth::id());

        return response()->json([
            'user'           => $user,
            'changePassword' => false,
        ]);
    }

    public function store(Request $request)
    {
        $savePass = $request->changePassword;
        $rules    = [
            'user.name' => 'required',
        ];

        $messages = [
            'user.name.required' => 'El nombre es requerido',
        ];

        if ($savePass) {
            $rules['user.password']              = 'required|confirmed';
            $messages['user.password.required']  = 'La password es requerida';
            $messages['user.password.confirmed'] = 'Las passwords no concuerdan';
        }

//        event(new TestEvent("Hola Este es un evento publico"));
//        sleep(3);
//        event(new PrivateEvent("******* Provado ************ "));
//        sleep(3);
        $request->validate($rules, $messages);

        $user       = User::findOrFail(Auth::id());
        $user->name = $request->user['name'];
        if ($savePass) {
            $user->password = Hash::make($request->user['password']);
        }
        $user->save();

        return response()->json('ok');
    }
}

