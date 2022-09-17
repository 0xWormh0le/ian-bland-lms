<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Mail;

class TwoFactorAuthController extends Controller
{


    public function index(Request $request)
    {
        return view("auth.two_factor_auth") ;
    }

    public function verify(Request $request)
    {
       $user = Auth::user();
       $otp = implode("",$request->otp);
       $userVerify = \DB::table('otp_verify')->where('otp', $otp)->where("user_id", $user->id)->first();


       if(!$userVerify)
       {

         return redirect()->back()->withInput()->withErrors(["otp" => trans('auth.otp_error')]);
       }
       else {
         $user->is_otp_verified = 1;
         $user->save();
         return redirect(route('home'));
       }
    }


    public function sendOTP()
    {
        $user = Auth::user();
        if($user)
        {
            $user->is_verified = false;
          //  $user->active = false;

            if($user->save())
            {
                $otp = mt_rand(111111, 999999);
                \DB::table('otp_verify')->where('user_id', $user->id)->delete();
                \DB::table('otp_verify')->insert([
                    'user_id' => $user->id,
                    'otp' => $otp,
                    'created_at' => date('Y-m-d H:i:s'),
                ]);

                dispatch(new \App\Jobs\SendEmail($user, 'OTPVerificationEmail'));


            }
          return redirect(route('auth2'));
        }
        else
        {
          return redirect(route('login'));
        }

    }
}
