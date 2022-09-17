<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\EmailTemplate;
use App\Company;
use App\User;
use App\Ticket;
use App\TicketResponse;
use Auth;

class ResponseTicketEmail extends Mailable
{
    use Queueable, SerializesModels;
    protected $response;
    protected $user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(TicketResponse $response, User $user)
    {
       $this->response = $response;
       $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $slug = "response_ticket";

        $ticket =  Ticket::where('id', $this->response->ticket_id)->first();

        $template = email_template_language($this->user, $slug);

        $responder = User::where("id", $this->response->responder_id)->first();

        $companyName = "" ;
        if(!Auth::user()->isSysAdmin())
        {
           $companyName = $responder->company->company_name;
        }
        $html = $template->content;

        $trans = [
            '@URL' => route('tickets.show', encrypt($ticket->id)),
            '@FIRSTNAME' => $this->user->first_name,
            '@LASTNAME' => $this->user->last_name,
            '@SENDER_NAME' => $responder->first_name.' '.$responder->last_name,
            '@SENDER_EMAIL' => $responder->email,
            '@TICKET_NO' => $ticket->ticket_number,
            '@TICKET_CONTENT' => $this->response->content,
            '@PORTAL' => config('app.name'),
            '@COMPANY' => $companyName,
        ];

        $html = strtr($html, $trans);

        return $this->view('emails.rawtemplate')
                    ->subject($template->subject)
                    ->with(['html' => $html]);
    }
}
