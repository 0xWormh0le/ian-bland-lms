<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Auth;
use App\EmailTemplate;

class OTPVerificationEmail extends Mailable
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
      //  $template = \App\EmailTemplate::findBySlug('otp_verification');

        if(Auth::user()->isSysAdmin())
        {
          $template = EmailTemplate::where('company_id', null)
                                 ->where('slug','otp_verification')->first();
        }
        else {
          $companyId = Auth::user()->company_id;
          $template = EmailTemplate::where('company_id', $companyId)
                                 ->where('slug','otp_verification')->first();
        }

        if(!$template)
        {
          $template = EmailTemplate::where('company_id', null)
                                 ->where('slug','otp_verification')->first();
        }

        $verify = \DB::table('otp_verify')
                    ->select("otp")
                    ->where("user_id", $this->user->id)
                    ->first();

        $html = $template->content;
        $subject =  $template->subject;
        $trans = [
            '@OTP' => $verify->otp,
            '@PORTAL' => config('app.name'),
            '@COMPANY' => @$this->user->company->company_name?:'',
            '@FIRSTNAME' => $this->user->first_name,
            '@LASTNAME' => $this->user->last_name,
        ];
        $html = strtr($html, $trans);

        return $this->view('emails.rawtemplate')
                    ->subject($subject)
                    ->with(['html' => $html]);
    }
}
