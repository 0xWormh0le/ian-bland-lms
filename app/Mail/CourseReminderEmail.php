<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\User;
use App\Course;
use DB;

class CourseReminderEmail extends Mailable
{
    use Queueable, SerializesModels;
    protected $courseUser;
    protected $user;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($courseUser, $user)
    {
        $this->courseUser = $courseUser;
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $slug = "course-reminder" ;
        $companyName = "" ;

        $template = email_template_language($this->user, $slug);

        $course = Course::where("id", $this->courseUser->course_id)->first();
        $comAdmin = User::where('id', $this->courseUser->enrolled_by)->first();

        $html = $template->content;
        $companyName = $comAdmin->company->company_name ;

        $token = optional(DB::table('token_verify')->where('email', $this->user->email)->first())->token;
        
        $trans = [
            '@URL' => route('my-courses.show', $course->slug)
                        . '?code=' . encrypt($this->user->email)
                        . ($token ? '&token=' . $token : ''),
            '@FIRSTNAME' => $this->user->first_name,
            '@LASTNAME' => $this->user->last_name,
            '@COURSE_NAME' => $course->title,
            '@ENROLLED_BY' => $comAdmin->first_name." ".$comAdmin->last_name,
            '@PORTAL' => config('app.name'),
            '@COMPANY' => $companyName,
        ];

        $html = strtr($html, $trans);


        return $this->view('emails.rawtemplate')
                  ->subject($template->subject)
                  ->with(['html' => $html]);
    }
}
