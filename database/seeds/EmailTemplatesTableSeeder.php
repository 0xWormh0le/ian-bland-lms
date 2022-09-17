<?php

use Illuminate\Database\Seeder;

class EmailTemplatesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('email_templates')->whereNull('company_id')->whereNull('language')->delete();
        
        \DB::table('email_templates')->insert(array (
            array (
                'company_id' => NULL,
                'template_name' => 'Welcome Email',
                'slug' => 'welcome-email',
                'subject' => 'Welcome Email',
            'content' => '<p>Hi, @FIRSTNAME></p>
<p>Welcome to @PORTAL, you have been registered @COMPANY Security Training LMS Portal. Please click the link bellow to verify your account and continue the registration step. </p>

<a href="@URL" id="iwgkg" class="button" style="box-sizing: border-box; font-size: 12px; padding-top: 10px; padding-right: 20px; padding-bottom: 10px; padding-left: 20px; background-color: rgb(217, 131, 166); color: rgb(255, 255, 255); text-align: center; border-top-left-radius: 3px; border-top-right-radius: 3px; border-bottom-right-radius: 3px; border-bottom-left-radius: 3px; font-weight: 300;">Verify Account</a>',
                'language' => NULL,
                'updated_by' => NULL,
                'updated_at' => '2019-01-28 09:58:22',
            ),
            array (
                'company_id' => NULL,
                'template_name' => 'Support Tickets',
                'slug' => 'support-tickets',
                'subject' => 'Support Tickets',
                'content' => '
<p>@COMPANY</p>



<p>Hi @FIRSTNAME @LASTNAME</p>

<p>you have receive new support ticket from @SENDER_NAME.<br />
<br />
Ticket No - @TICKET_NO<br />
Subject - @TICKET_SUBJECT<br />
Message - @TICKET_CONTENT<br />
<br />
To reply or get more details, click below.</p>

<a href="@URL">Click Here </a>',
                'language' => NULL,
                'updated_by' => NULL,
                'updated_at' => '2019-06-19 19:28:18',
            ),
            array (
                'company_id' => NULL,
                'template_name' => 'Support Tickets Response',
                'slug' => 'response_ticket',
                'subject' => 'Support Tickets Response',
                'content' => '<p>@COMPANY</p>
<p>Hi @FIRSTNAME @LASTNAME</p>

<p>you have received response of a ticket from @SENDER_NAME.<br />
<br /
Ticket No - @TICKET_NO<br />
<br />
Message - @TICKET_CONTENT<br />
<br />
To reply or get more details, click below.</p>

<a href="@URL">Click Here </a>
',
                'language' => NULL,
                'updated_by' => NULL,
                'updated_at' => '2019-06-19 19:28:03',
            ),
            array (
                'company_id' => NULL,
                'template_name' => 'Course Enrolment',
                'slug' => 'course-enrolment',
                'subject' => 'Course Enrolment',
                'content' => '<p>Hi @FIRSTNAME @LASTNAME</p>

<p>You have been enrolled in course &quot;<strong>@COURSE_NAME</strong>&quot; by &quot;<strong>@ENROLLED_BY</strong>&quot;.<br />
<br />
Please click on below link to start or get more details.</p>

<p><a href="@URL">Click Here</a></p>',
                'language' => NULL,
                'updated_by' => NULL,
                'updated_at' => '2019-05-30 19:46:39',
            ),
            array (
                'company_id' => NULL,
                'template_name' => 'Forgot Password',
                'slug' => 'forgot-password',
                'subject' => 'Forgot Password',
                'content' => '<pre>
<code>Hi @FIRSTNAME @LASTNAME,

You are receiving this email because we received a password reset request for your account.</code></pre>

<p>&nbsp;</p>

<p>@TOKEN_URL</p>

<p>&nbsp;</p>

<pre>
<code>If you did not request a password reset, no further action is required.</code></pre>

<p>&nbsp;</p>

<p>@COMPANY</p>

<p>@PORTAL</p>',
                'language' => NULL,
                'updated_by' => NULL,
                'updated_at' => '2019-06-18 14:09:17',
            ),
            array (
                'company_id' => NULL,
                'template_name' => 'Verification Email',
                'slug' => 'verification-email',
                'subject' => 'Verification Email',
                'content' => '
<p>@PORTAL @COMPANY</p>

<p>Hi @FIRSTNAME @LASTNAME</p>

<p>Please verify your email address by clicking on the link below. We&#39;ll communicate with you from time to time via email so it&#39;s important that we have an up-to-date email address on file.</p>

<a href="@URL">Click Here </a>

<p>If you did not sign up for a @PORTAL account please disregard this email.</p>',
                'language' => NULL,
                'updated_by' => NULL,
                'updated_at' => '2019-06-19 19:28:35',
            ),
            array (
                'company_id' => NULL,
                'template_name' => 'Course Completed',
                'slug' => 'certificate-achieved',
                'subject' => 'Course Completed',
                'content' => '<p>Hi @FIRSTNAME @LASTNAME,</p>

<p><strong>Congratulation! </strong></p>

<p>You have passed @COURSE_NAME course.</p>

<p><strong>Certificate</strong> : @CERTIFICATE_URL</p>

<p>@COMPANY</p>

<p>@PORTAL</p>',
                'language' => NULL,
                'updated_by' => NULL,
                'updated_at' => '2019-06-19 20:13:12',
            ),
            array (
                'company_id' => NULL,
                'template_name' => 'Course Reminder',
                'slug' => 'course-reminder',
                'subject' => 'Course Reminder',
                'content' => '<p>Hi @FIRSTNAME @LASTNAME</p>

<p>You have been enrolled in course &quot;<strong>@COURSE_NAME</strong>&quot; by &quot;<strong>@ENROLLED_BY</strong>&quot;.<br />
<br />
Please click on below link to start or get more details.</p>

<p><a href="@URL">Click Here</a></p>',
                'language' => NULL,
                'updated_by' => NULL,
                'updated_at' => '2019-06-28 08:02:51',
            ),
            array(
                'company_id' => NULL,
                'template_name' => 'Course Overdue',
                'slug' => 'course-overdue',
                'subject' => 'Course Overdue',
                'content' => '<p>HI @FIRSTNAME @LASTNAME</p>

<p>This is a polite reminder. You have been enrolled in the course &quot;<strong>@COURSE_NAME</strong>&quot; in the &quot;<strong>@COMPANY</strong>&quot <strong>Security Training LMS</strong>.</p>
<p>You are now <strong>OVERDUE</strong> and need to complete this course.</p>
<p>Please click on the link below to start.</p>
<p><a href="@URL">Click Here </a></p>',
                'language' => NULL,
                'updated_by' => NULL,
                'updated_at' => '2019-06-28 08:02:51',
            )
        ));
        
        
    }
}
