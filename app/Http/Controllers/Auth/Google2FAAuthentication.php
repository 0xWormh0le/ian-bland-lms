<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use PragmaRX\Google2FALaravel\Support\Authenticator;


class Google2FAAuthentication extends Authenticator
{
  // If User does not have Google2FA Setup yet
  protected function canPassWithoutCheckingOTP()
  {

    $user =  \Auth::user();
    if($user->google2fa_enable == 0)
    {
      return true;
    }
  //  echo "asdf-".($user->google2fa_enable==1)."==".$this->isEnabled()."==".$this->noUserIsAuthenticated()."==".$this->twoFactorAuthStillValid(); die;
    return !$user->google2fa_enable || !$this->isEnabled() || $this->noUserIsAuthenticated() || $this->twoFactorAuthStillValid();
  //  return  $this->twoFactorAuthStillValid();


  }

  protected function getGoogle2FaSecretkey()
  {
      // Get User secret column
     $user =  \Auth::user();
     $secret = $user->google2fa_secret;

    // If user has Google2FA setup and is Authenticated
    return $secret;
  }
}
