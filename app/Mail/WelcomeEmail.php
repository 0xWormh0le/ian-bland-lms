<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Auth;
use App\EmailTemplate;
use App\Company;

class WelcomeEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The user instance.
     *
     * @var User
     */
    protected $user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(\App\User $user)
    {
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        //$template = \App\EmailTemplate::findBySlug('welcome-email');

        if(Auth::user()->isSysAdmin())
        {
          $template = EmailTemplate::where('company_id', null)
                                 ->where('slug','welcome-email')
                                 ->where('language', null)
                                 ->first();
        }
        else {
          $companyId = Auth::user()->company_id;
          $language = Auth::user()->language ;
          $count = 0 ;
          $template = EmailTemplate::where('company_id', $companyId)
                                 ->where('slug','welcome-email');

                // if(trim($language) !="")
                // {
                //   $template->where('language', $language);
                //   $count = $template->count();
                // }

                if($count == 0)
                 {
                    $comResult = Company::select('language')->where('id', $companyId)->first();
                    if(trim($comResult->language) !="")
                    {
                      $template->where('language', $comResult->language);
                      $count = $template->count();
                    }

                 }

               if($count == 0)
                  {
                      $template->where('language', null);
                  }


           $template = $template->first();
        }


        if(!$template)
        {
          $template = EmailTemplate::where('company_id', null)
                                 ->where('language', null)
                                 ->where('slug','welcome-email')->first();
        }

        $token = \DB::table('token_verify')
                    ->whereEmail($this->user->email)
                    ->orderBy('created_at', 'DESC')
                    ->first();

        $html = $template->content;
        $trans = [
            '@URL' => route('user.verify', $token->token),
            '@FIRSTNAME' => $this->user->first_name,
            '@PORTAL' => config('app.name'),
            '@COMPANY' => @$this->user->company->company_name?:'',
        ];
        $html = strtr($html, $trans);

        return $this->view('emails.rawtemplate')
                    ->subject($template->subject)
                    ->with(['html' => $html]);
    }
}
