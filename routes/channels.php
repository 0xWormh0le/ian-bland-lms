<?php

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

Broadcast::channel('App.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
Broadcast::channel('ticket', function ($user) {
    return $user->isSysAdmin()  == true;
});

Broadcast::channel('message_user.{ticket_id}', function ($user, $ticket_id) {
    return $user->isSysAdmin()  == true;
});


Broadcast::channel('message.{user_id}', function ($user, $user_id) {
    return $user->id == $user_id;
});