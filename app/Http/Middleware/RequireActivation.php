<?php

namespace App\Http\Middleware;

use Closure;

use App\User;

class RequireActivation
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
        $token = $request->input('token');

        if ($token) {
            $token = \DB::table('token_verify')->where('token', $token)->first();

            if (!$token) {
                return abort(400, 'Invalid token');
            }

            $user = User::where('email', $token->email)->first();

            if (!$user) {
                return abort(400, 'Invalid token');
            }

            if (empty($user->password) || !$user->active) {
                session(['redirect_after_verify' => $request->fullUrl()]);
                return redirect()->route('user.verify', $token->token);
            }
        }

        return $next($request);
    }
}
