<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Auth;
use App\EmailTemplate;
use App\Company;
use App\User;

class NewTicketEmail extends Mailable
{
    use Queueable, SerializesModels;
    protected $ticket;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(\App\Ticket $ticket)
    {
        $this->ticket = $ticket;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $slug = "support-tickets" ;
        $companyName = "" ;

        $sender = User::where('email', $this->ticket->sender_email)->first();
        $companyName = $sender->company->compnay_name;
        $receiver = null ;
        if($sender->role_id == 1)
        {
           $receiver = User::where("role_id", 0)->orWhere('role_id', null)->first();

           $receiverFirstName = $receiver->first_name ;
           $receiverLastName = $receiver->last_name ;
        }
        else {
          $receiver = User::where("company_id", $sender->company_id)->where("role_id", 1)->first();
          $receiverFirstName = $receiver->first_name ;
          $receiverLastName = $receiver->last_name ;
        }

        $template = email_template_language($receiver, $slug);

        $html = $template->content;
        $trans = [
            '@URL' => route('tickets.show', encrypt($this->ticket->id)),
            '@FIRSTNAME' => $receiverFirstName,
            '@LASTNAME' => $receiverLastName,
            '@SENDER_NAME' => $this->ticket->sender_name,
            '@SENDER_EMAIL' => $this->ticket->sender_email,
            '@TICKET_NO' => $this->ticket->ticket_number,
            '@TICKET_SUBJECT' => $this->ticket->title,
            '@TICKET_CONTENT' => $this->ticket->content,
            '@PORTAL' => config('app.name'),
            '@COMPANY' => $companyName,
        ];

        $html = strtr($html, $trans);

        return $this->view('emails.rawtemplate')
                    ->subject($template->subject)
                    ->with(['html' => $html]);
    }
}
