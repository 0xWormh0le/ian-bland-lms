<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use App\Exceptions\TwoFactorAuthException;
use App\Company;
use Carbon\Carbon;
use PragmaRX\Google2FALaravel\Support\Authenticator;
use App\Http\Controllers\Auth\Google2FAAuthentication;

class GoogleAuthentication
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */

    public function handle($request, Closure $next)
    {

        $user = \Auth::user();

        $authentication = app(Google2FAAuthentication::class)->boot($request);



      if ($authentication->isAuthenticated()) {
               return $next($request);
          }

        if(session("google2faSession") == 1)
          return redirect()->route('google-user-auth');
         else {
          return $next($request);
         }



    }
}
