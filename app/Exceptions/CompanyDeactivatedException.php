<?php

namespace App\Exceptions;

use Exception;
use Auth;
use Redirect ;

class CompanyDeactivatedException extends Exception
{
    //
    public function report()
    {
    }

    public function render($request)
   {
     Auth::logout();
     return redirect(route('login'))->withErrors(["email" => trans('auth.company_deactivate')]);

   }
}
