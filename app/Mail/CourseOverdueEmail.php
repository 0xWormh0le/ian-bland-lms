<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\User;
use App\Course;
use App\Company;

class CourseOverdueEmail extends Mailable
{
    use Queueable, SerializesModels;
    
    protected $user;
    protected $course_id;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $course_id)
    {
        $this->user = $user;
        $this->course_id = $course_id;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $slug = "course-overdue" ;
        $course = Course::find($this->course_id);
        $company = Company::find($this->user->company_id);

        $token = optional(\DB::table('token_verify')->where('email', $this->user->email)->first())->token;
        $url = route('my-courses.show', $course->slug)
            . '?code=' . encrypt($this->user->email)
            . ($token ? '&token=' . $token : '');
            
        $trans = [
            '@FIRSTNAME' => $this->user->first_name,
            '@LASTNAME' => $this->user->last_name,
            '@COURSE_NAME' => $course->title,
            '@COMPANY' => $company->company_name,
            '@URL' => $url
        ];
        
        $template = email_template_language($this->user, $slug);
        $html = $template->content;
        $html = strtr($html, $trans);

        return $this->view('emails.rawtemplate')
                  ->subject($template->subject)
                  ->with(['html' => $html]);
    }
}
