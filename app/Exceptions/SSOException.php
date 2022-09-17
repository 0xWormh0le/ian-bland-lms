<?php

namespace App\Exceptions;

use Exception;
use Auth;
use Redirect ;

class SSOException extends Exception
{
    protected $type;
    
    public function __construct($type)
    {
      $this->type = $type;
    }

    public function report()
    {
    }

    public function render($request)
    {
      Auth::logout();
      return redirect(route('login'))->withErrors(["email" => trans($this->type)]);
    }
}
