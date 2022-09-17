<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Carbon\Carbon;
use \Illuminate\Http\Request;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers {
        login as protected defaultLogin; 
    }

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Get the needed authorization credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentials(\Illuminate\Http\Request $request)
    {
        //return $request->only($this->username(), 'password');
        return ['email' => $request->{$this->username()}, 'password' => $request->password, 'active' => 1];
    }

    public function login(Request $request)
    {
        $email = $request->input('email');
        $password = $request->input('password');

        $user = \App\User::where('email', $email)->first();
        $request->flash();

        if ($user && $user->azure_id) {
            return redirect('/login/microsoft');
        }
        
        if ($password) {
            return $this->defaultLogin($request);
        } else {
            return $this->showLoginForm('default-step-2');
        }
    }

    function authenticated(Request $request, $user)
    {
        $user->update([
            'last_login_at' => Carbon::now()->toDateTimeString(),
            'last_login_ip' => $request->getClientIp()
        ]);
    }

    public function showLoginForm($login_view_type = 'default-step-1')
    {
        $url = url()->previous();

        if ($url) {
            parse_str(parse_url($url, PHP_URL_QUERY), $params);

            if (isset($params['code'])) {
                try {
                    $email = decrypt($params['code']);
                    $user = \App\User::where('email', $email)->first();
                    
                    if ($user) {
                        $login_view_type = empty($user->azure_id) ? 'tradition' : 'sso';
                    }
                } catch (\Exception $e) { }
            }
        }

        return view('auth.login', compact('login_view_type'));
    }

}
