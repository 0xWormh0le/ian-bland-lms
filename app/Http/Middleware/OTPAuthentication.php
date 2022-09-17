<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use App\Exceptions\TwoFactorAuthException;
use App\Company;
use App\OtpVerify;
use Carbon\Carbon;

class OTPAuthentication
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
     $otpStatus = false;
     if($otpStatus)
     {
       if(Auth::user()->is_otp_verified == false)
        {
           throw new TwoFactorAuthException();
        }
       else {
           $user = Auth::user();

           $otpDetail = OtpVerify::where('user_id', $user->id)->first();
           $now = Carbon::now();
           $otpTime = Carbon::createFromFormat('Y-m-d H:s:i', $otpDetail->created_at);
           $diff_in_hours = $now->diffInHours($otpTime);

          if($diff_in_hours >= 1)
          {
            $user->is_otp_verified = 0;
            $user->save();
            throw new TwoFactorAuthException();
          }

       }
     }

        return $next($request);
    }
}
