<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Mail;
use Auth;
use DB;
use App\User;


class SendEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;
    protected $type;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data, $type)
    {
        $this->data = $data;
        $this->type = $type;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->type == 'WelcomeEmail')
        {
            Mail::to($this->data->email)->queue(new \App\Mail\WelcomeEmail($this->data));
        }
        else if ($this->type == 'VerificationEmail')
        {
            Mail::to($this->data->email)->queue(new \App\Mail\VerificationEmail($this->data));
        }
        else if ($this->type == "OTPVerificationEmail")
        {
            Mail::to($this->data->email)->queue(new \App\Mail\OTPVerificationEmail($this->data));
        }
        else if ($this->type == "NewTicketEmail")
        {
          $data = $this->data;
          $ticket = $data['ticket'];
          $user = User::find($data['user_id']);

          $email_to = '';

          if ($user->isClientAdmin())
          {
             $super_admin  = User::where('role_id', 0)->first();
             $email_to = $super_admin->email ;
             Mail::to($email_to)->queue(new \App\Mail\NewTicketEmail($ticket));
          }
          else {
            $company_admin  = User::where('role_id', 1)->where('company_id', $user->company_id)->get();

            for($c=0;$c<count($company_admin); $c++)
            {
                $email_to = $company_admin[$c]->email;
                Mail::to($email_to)->queue(new \App\Mail\NewTicketEmail($ticket));
            }
          }

        }
        else if ($this->type == "ResponseTicketEmail")
        {
           $ticket = \App\Ticket::where("id", $this->data->ticket_id)->first();
           $comAdminEnable = 0 ;
           $email = "";
           $user = null ;

           if ($ticket->created_by != $this->data->responder_id)
           {
             $user = User::where('id', $ticket->created_by)->first();
             $email = $user->email;
           }
           else {
             $user = User::where('id', $this->data->responder_id)->first();

             if ($user->role_id == 1)
             {
               $superAdmin = User::where('role_id', 0)->orWhere('role_id', null)->first();
               $email = $superAdmin->email;
             }
             else {
               $companyAdmin = User::where('company_id', $user->company_id)->get();
               $comAdminEnable = 1;
               for($com=0;$com<count($companyAdmin);$com++)
               {

                 $email = $companyAdmin[$com]->email;
                 Mail::to($email)->queue(new \App\Mail\ResponseTicketEmail($this->data, $companyAdmin[$com]));
               }

             }

           }

           if ($comAdminEnable == 0 && $email!="" && $user!=null)
            Mail::to($email)->queue(new \App\Mail\ResponseTicketEmail($this->data, $user));
        }
        else if ($this->type == "CourseEnrollment")
        {
            $app = \App::getInstance();
            $app->register('Illuminate\Mail\MailServiceProvider');

            $courseUsers = \App\CourseUser::where("start_date", \Carbon\Carbon::now()->format("Y-m-d"))->get();

            foreach ($courseUsers as $courseUser)
            {
              $user = User::where("id", $courseUser->user_id )->first();
              \Mail::to($user->email)->queue(new \App\Mail\CourseEnrollmentEmail($courseUser, $user));
              \Log::debug('Course enrollment mail send to '.$user->email.' on '.\Carbon\Carbon::now()->format("Y-m-d"));
            }
        }
        else if ($this->type == 'CourseOverdue') {
          $data = $this->data;
          $course_id = $data['course_id'];
          $company_id = $data['company_id'];

          $query =
            'SELECT user_id, course_id '.
            'FROM ( '.
            '  SELECT '.
            '    SUBSTRING( deadline, 1, LOCATE( " ", deadline ) - 1 ) AS deadline_value, '.
            '    SUBSTRING( deadline, LOCATE( " ", deadline ) + 1 ) AS deadline_unit, '.
            '    IFNULL( start_date, enrol_date ) AS start_base_date, '.
            '    course_users.course_id AS course_id, '.
            '    course_users.user_id AS user_id '.
            '  FROM '.
            '    `course_users` '.
            '  JOIN `users` ON `users`.`id` = `course_users`.`user_id` '.
            '  JOIN `courses` ON `courses`.`id` = `course_users`.`course_id` '.
            '  LEFT JOIN `course_companies` ON `course_companies`.`course_id` = `course_users`.`course_id` '.
            '    AND `course_companies`.`company_id` = `users`.`company_id` '.
            '  WHERE TRUE'.
            '    AND `course_users`.`completed` = 0 ';

          if ($course_id > 0) {
            $query .= ' AND `course_users`.`course_id` = '.$course_id;
          }

          $query .=
            '    AND `users`.`active` = 1 '.
            '    AND `users`.`is_suspended` = 0 '.
            '    AND `users`.`company_id` = '.$company_id.
            '    AND `users`.`deleted_at` IS NULL '.
            '    AND `course_users`.`deleted_at` IS NULL '.
            '    AND `courses`.`deleted_at` IS NULL '.
            '    AND `course_companies`.`deleted_at` IS NULL '.
            '  ) AS t '.
            'WHERE '.
            '  CASE '.
            '    WHEN deadline_unit = "day" THEN '.
            '    DATE_ADD( start_base_date, INTERVAL deadline_value DAY ) '.
            '    WHEN deadline_unit = "week" THEN '.
            '    DATE_ADD( start_base_date, INTERVAL deadline_value WEEK ) '.
            '    WHEN deadline_unit = "month" THEN '.
            '    DATE_ADD( start_base_date, INTERVAL deadline_value MONTH ) ELSE DATE_ADD( start_base_date, INTERVAL deadline_value YEAR ) '.
            '  END < NOW()';

          $courses = \DB::select($query);
          
          foreach ($courses as $course) {
            $user = User::find($course->user_id);
            if ($user) {
              \Mail::to($user->email)->queue(new \App\Mail\CourseOverdueEmail($user, $course->course_id));
            }
          }
        }
        else if ($this->type == "CourseReminder")
        {

            $app = \App::getInstance();
            $app->register('Illuminate\Mail\MailServiceProvider');
          //  \Artisan::call('config:cache');
          //  sleep(2);

            $courseUsers = \App\CourseUser::select(
              'course_users.id',
              'course_users.course_id',
              'course_users.user_id',
              'course_users.start_date',
              'course_users.enrolled_by',
              'course_companies.notification_reminder'
            )->join(
              'course_companies',
              'course_companies.course_id',
              'course_users.course_id'
            )->join('users', 'users.id', 'course_users.user_id')
            ->whereNotNull('course_companies.notification_reminder')
            ->where('course_companies.notification_reminder', '>', 0)
            ->whereNotNull('course_users.start_date')
            ->where('course_users.start_date', '<', DB::raw('CURDATE()'))
            ->where('users.company_id', DB::raw('course_companies.company_id'))
            ->whereRaw('(DATEDIFF(CURDATE(), course_users.start_date) % course_companies.notification_reminder) = 0')
            ->get();
            
            foreach ($courseUsers as $courseUser) {
              $result = course_completion_rules_result($courseUser->course_id , $courseUser->user_id);

              if ($result['complete'] == 0) {
                $user = User::where("id", $courseUser->user_id)->first();
                $course = \App\Course::where("id", $courseUser->course_id)->first();
                $comAdmin = User::where('id', $courseUser->enrolled_by)->first();

                if ($user && $course && $comAdmin) {
                  \Mail::to($user->email)->queue(new \App\Mail\CourseReminderEmail($courseUser, $user));
                  \Log::debug('Course reminder mail send to '.$user->email.' on '.\Carbon\Carbon::now()->format("Y-m-d"));
                }
              }
            }
        }
        else if ($this->type == "ForgotPassword")
        {
          Mail::to($this->data->email)->queue(new \App\Mail\ForgotPasswordEmail($this->data));
        }
        else if ($this->type == "CourseCompletion") {
          $data = $this->data;
          $user = User::find($data['user_id']);
          $course_id = $data['course_id'];
          Mail::to($user->email)->queue(new \App\Mail\CourseCompletionEmail($course_id, $user));
        }
        else if ($this->type == "CourseMemberEnrollment")
        {
          $courseUsers = $this->data;

           $user = User::where("id", $courseUsers->user_id )->first();
           Mail::to($user->email)->queue(new \App\Mail\CourseEnrollmentEmail($courseUsers, $user));
        }
        if ($this->type == 'TestEmail')
        {
          $app = \App::getInstance();
          $app->register('Illuminate\Mail\MailServiceProvider');
          $this->data = User::where("email", "dheerendrachouhan@gmail.com")->first();
          Mail::to($this->data->email)->queue(new \App\Mail\ForgotPasswordEmail($this->data));
          /*     Mail::raw('Our task, according to its usage, is supposed to be run once a day. So we can use the daily() method. So we need to write following code in the app  >>  Console  >>  Kerne.php file. We will write the code inside schedule function. If you want to more info about task scheduling, then please refer the ', function($message)
                        {
                            $message->from('admin@desk-track.com', 'Laravel');
                            $message->subject('dheerendrachouhan@gmail.com');
                            $message->to('dheerendrachouhan@gmail.com');
                        } );*/
          //  $this->data = User::where("email", "dheerendrachouhan@gmail.com")->first();
          //  Mail::to($this->data->email)->queue(new \App\Mail\WelcomeEmail($this->data));
        }

    }
  }
