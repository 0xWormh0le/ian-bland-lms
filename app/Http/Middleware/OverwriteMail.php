<?php

namespace App\Http\Middleware;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Mail\TransportManager;

use Closure;
use Mail;
use Config;
use App;
use App\CompanyEmailConfig;

class OverwriteMail
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

        if(\Auth::user() && !\Auth::user()->isSysAdmin())
        {
          $record = CompanyEmailConfig::where("company_id", \Auth::user()->company_id)->first();


          if($record && $record->mail_driver =="smtp" && $record->smtp_host !="")
          {
               Config::set('mail.driver',$record->mail_driver);
               Config::set('mail.host',$record->smtp_host);
               Config::set('mail.port',$record->smtp_port);
               Config::set('mail.username',$record->smtp_username);
               Config::set('mail.password',$record->smtp_password);
               Config::set('mail.from.address',$record->from_address);
               Config::set('mail.from.name',$record->from_name);
          }
          elseif($record && $record->mail_driver =="mailgun" && $record->mailgun_domain !="" && $record->mailgun_secret !="")
          {
            Config::set('services.domain',$record->mailgun_domain);
            Config::set('services.secret',$record->mailgun_secret);
          }
          elseif($record && $record->mail_driver =="sparkpost" && $record->sparkpost_secret !="")
          {
            Config::set('services.secret',$record->sparkpost_secret);
          }
          elseif($record && $record->mail_from_name_custom !="")
          {
            Config::set('mail.from.name',$record->mail_from_name_custom);
          }
        }

       $app = App::getInstance();
       $app->register('Illuminate\Mail\MailServiceProvider');

        return $next($request);
    }
}
