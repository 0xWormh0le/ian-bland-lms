<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\User;
use App\Course;
use App\Company;

class CourseCompletionEmail extends Mailable
{
    use Queueable, SerializesModels;
    protected $coursId;
    protected $user;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($coursId, $user)
    {
        $this->coursId = $coursId;
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        $slug = "certificate-achieved" ;
        $companyName = "" ;

        $template = email_template_language($this->user, $slug);

        $course = Course::where("id", $this->coursId)->first();

        $certificateUrl = trans("modules.no_certificate") ;

        if($course && $course->certificate)
        {
           $certificateUrl = "<a href='".route('certificate.preview', ['user_id' => $this->user->id, 'course_id'=> $course->id])."'>".trans("modules.certificate")."</a>";
        }

        $html = $template->content;
        $companyName = Company::select('company_name')->where('id', $this->user->company_id)->first() ;
        $trans = [
            '@FIRSTNAME' => $this->user->first_name,
            '@LASTNAME' => $this->user->last_name,
            '@COURSE_NAME' => $course->title,
            '@PORTAL' => config('app.name'),
            '@COMPANY' => $companyName->company_name,
            '@CERTIFICATE_URL' => $certificateUrl
        ];

        $html = strtr($html, $trans);


        return $this->view('emails.rawtemplate')
                  ->subject($template->subject)
                  ->with(['html' => $html]);
    }
}
