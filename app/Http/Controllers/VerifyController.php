<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;

class VerifyController extends Controller
{

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('verify.index');
    }

    /**
     * Handle Token Verify.
     *
     * @param string $token
     * @return \Illuminate\Http\Response
     */
    public function verify($token)
    {
        $valid = \DB::table('token_verify')
                        ->where('token', $token)
                        ->first();

        if(!$valid)
        {
            return view('verify.token_missmatch', compact('token'));
        }
        else{
            $user = User::findByEmail($valid->email);
            if($user)
                return view('verify.setup_password', compact('user', 'token'));
            return view('verify.invalid_email')->with([
                'email' => $valid->email,
            ]);
        }        
    }

    /**
     * Confirm Token Verify.
     *
     * @param string $token
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function confirmVerify(Request $request, $token)
    {
        $validatedData = $request->validate([
            'password' => 'required|confirmed'
        ]);

        $valid = \DB::table('token_verify')
                        ->where('token', $token)
                        ->first();

        if(!$valid)
        {
            return view('verify.token_missmatch');
        }
        else{
            $user = User::findByEmail($valid->email);
            $user->password = bcrypt($request->password);
            $user->active = true;
            $user->is_verified = true;
            if($user->save())
            {
                // \DB::table('token_verify')
                //         ->where('token', $token)
                //         ->delete();

                \Auth::login($user);
                $redirect = session()->pull('redirect_after_verify', null);

                if (empty($redirect)) {
                    return redirect()->route('home');
                } else {
                    return redirect($redirect);
                }
            }
        }
    }

    /**
     * Resend Token Verify.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function resend(Request $request)
    {
        $user = User::findByEmail($request->email);
        if($user)
        {
            $user->is_verified = false;
            $user->active = false;
            $user->password = '';
            if($user->save())
            {
                \DB::table('token_verify')->where('email', $request->email)->delete();
                \DB::table('token_verify')->insert([
                    'email' => $user->email,
                    'token' => str_random(32),
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
                dispatch(new \App\Jobs\SendEmail($user, 'VerificationEmail'));

                return redirect()->route('user.verify.index');
            }
        }

    }

}
