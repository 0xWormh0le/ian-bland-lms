<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use App\CompanyEmailConfig;
use App\Company;

class ConfigCompany
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

        if(Auth::user() && !Auth::user()->isSysAdmin())
        {
          $companyId = Auth::user()->company_id ;

          if($companyId > 0)
          {
              $config = CompanyEmailConfig::find(['company_id' => $companyId])->first();

              if(@$config)
              {
                if(trim($config->mail_driver) != "")
                  \Config::set('mail.driver', $config->mail_driver);
                if(trim($config->from_address) != "")
                  \Config::set('mail.from.address', $config->from_address);
                if(trim($config->from_name) != "")
                  \Config::set('mail.name', $config->from_name);
                if(trim($config->mail_driver) != "" && trim($config->mail_driver) == "smtp" && trim($config->smtp_host) != "")
                {
                  \Config::set('mail.host', $config->smtp_host);
                  \Config::set('mail.port', $config->smtp_port);
                  \Config::set('mail.username', $config->smtp_username);
                  \Config::set('mail.password', $config->smtp_password);
                }
                else if(trim($config->mail_driver) != "" && trim($config->mail_driver) == "mailgun" && trim($config->mailgun_secret) != "")
                  {
                    \Config::set('mail.host', 'smtp.mailgun.org');
                    \Config::set('mail.port', '587');
                    \Config::set('services.mailgun.domain', $config->mailgun_domain);
                    \Config::set('services.mailgun.secret', $config->mailgun_secret);
                    \Config::set('mail.encryption', 'tls');

                  }
               else if(trim($config->mail_driver) != "" && trim($config->mail_driver) == "sparkpost" && trim($config->sparkpost_secret) != "")
                    {
                      \Config::set('mail.host', 'smtp.sparkpostmail.com');
                      \Config::set('mail.port', '587');
                      \Config::set('services.sparkpost.secret', $config->sparkpost_secret);
                      \Config::set('mail.encryption', 'tls');
                    }

              }
              $company = Company::find($companyId);

              if($company && trim($company->timezone) !="")
              {
                \Config::set('app.timezone', $company->timezone);
                 date_default_timezone_set($company->timezone);
              }
          }
        }
        return $next($request);
    }
}
