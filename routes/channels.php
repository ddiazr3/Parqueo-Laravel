<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('public', function () {
    return true;
});

Broadcast::channel('privado.{id}', function ($user, $id) {
    \Illuminate\Support\Facades\Log::info("id +log ".$user->id);
    \Illuminate\Support\Facades\Log::info("id recibido ".$id);
    return (int) $user->id === (int) $id;
});

Broadcast::channel('presence.{id}', function () {
    return true;
});
