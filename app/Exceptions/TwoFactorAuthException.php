<?php

namespace App\Exceptions;

use Exception;
use App\Http\Controllers\Auth\TwoFactorAuthController ;

class TwoFactorAuthException extends Exception
{
  public function render($request)
   {
     $tfObj = new TwoFactorAuthController();
     $tfObj->sendOTP();

     return redirect(route('auth2'));
   }
}
