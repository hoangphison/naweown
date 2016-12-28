<?php

namespace Naweown\Http\Controllers\Auth;

use Naweown\Http\Requests;
use Naweown\Http\Controllers\Controller;
use Naweown\Link;
use function Naweown\carbon;

class AccountActivationController extends Controller
{

    public function activate(Link $link)
    {
        $redirectRoute = route("dashboard");

        if (carbon()->diffInMinutes($link->created_at) >= config('auth.token.expires_after')) {
            return redirect($redirectRoute)
                ->with(
                    'token.expired',
                    'This token has expired and has thus been invalidated, please request for another token to activate your account'
                );
        }

        $user = $link->user;
        $user->activateAccount();

        return redirect($redirectRoute)
            ->with(
                'account.activated',
                "Hi, {$user->moniker}, your email address has been verified"
            );
    }
}