<?php

namespace Naweown\Http\Controllers;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Http\Request;
use Naweown\Events\UserAccountWasDeleted;
use Naweown\Events\UserProfileWasViewed;
use Naweown\User;

class UserController extends Controller
{

    public function index()
    {
        return view(
            'users.all',
            [
                'users' => User::paginate(50)
            ]
        );
    }

    public function show(
        Request $request,
        User $user,
        Dispatcher $dispatcher
    ) {

        $isOwner = false;

        if ($request->user() !== null) {
            $isOwner = $request->user()->id === $user->id;
        }

        $dispatcher->fire(new UserProfileWasViewed($user));

        return view(
            'users.show',
            [
                'user' => $user,
                'isOwner' => $isOwner
            ]
        );
    }

    public function destroy(
        User $user,
        Dispatcher $dispatcher
    ) {
        $user->delete();

        $dispatcher->fire(new UserAccountWasDeleted($user));

        return redirect('logout');
    }
}
