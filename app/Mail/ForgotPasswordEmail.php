<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\User;
use App\Course;

class ForgotPasswordEmail extends Mailable
{
    use Queueable, SerializesModels;
    protected $user;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user)
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
        $slug = "forgot-password" ;
        $companyName = "" ;

        $user = User::where('email', $this->user->email)->first();

        $template = email_template_language($user, $slug);

        $tokenUrl = '<a href="'.url('password/reset', $this->user->token).'">Reset Password</a>';

        $html = $template->content;
        $companyName = $user->company->company_name ;
        $trans = [
            '@FIRSTNAME' => $user->first_name,
            '@LASTNAME' => $user->last_name,
            '@TOKEN_URL' => $tokenUrl,
            '@PORTAL' => config('app.name'),
            '@COMPANY' => $companyName,
        ];

        $html = strtr($html, $trans);


        return $this->view('emails.rawtemplate')
                  ->subject($template->subject)
                  ->with(['html' => $html]);
    }
}
