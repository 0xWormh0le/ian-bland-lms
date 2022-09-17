<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Http\Request;
use App\Company;
use Auth ;
use Session;
use Carbon\Carbon;
use App\Exceptions\CompanyDeactivatedException;
use App\Exceptions\TwoFactorAuthException;

class AuthLogin
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Handle the event.
     *
     * @param  Event  $event
     * @return void
     */
    public function handle(Login $event)
    {


        $user = $event->user;

       if(!$user->isSysAdmin())
       {

          $company =Company::where("id",$user->company_id)->where("deleted_at", null)->first();

          if(!$company)
          {
            throw new CompanyDeactivatedException();
          }
          else if($company && $company->active == 0)
          {
            throw new CompanyDeactivatedException();
          }
          else if($company && $company->active_from !="" && $company->active_to !="")
          {
            $from_date = Carbon::parse($company->active_from)->format('Y-m-d');
            $to_date = Carbon::parse($company->active_to)->format('Y-m-d');

            $from = Carbon::now()->diffInDays($from_date, false);
            $to = Carbon::now()->diffInDays($to_date, false);

            if($from <=0 && $to >=0 ) ; else throw new CompanyDeactivatedException();
          }

       }


        $sysconfig = \App\SysConfig::first();
        $colourTheme = [
            'top_bar' => @$user->company->top_bar ?: @$sysconfig->top_bar,
            'top_bar_text' => @$user->company->top_bar_text ?: @$sysconfig->top_bar_text,
            'active_menu' => @$user->company->active_menu ?: @$sysconfig->active_menu,
            'active_menu_hover' => @$user->company->active_menu_hover ?: @$sysconfig->active_menu_hover,
            'text_primary' => @$user->company->text_primary ?: @$sysconfig->text_primary,
        ];

        session([
            'colourTheme' => $colourTheme,
            'google2faSession' => $user->google2fa_enable
        ]);
    }
}
