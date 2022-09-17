<?php

use Illuminate\Database\Seeder;

class WelcomeTemplateTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $en_content = '<h4 class="card-title text-center pb-4">Your Security Training Dashboard.</h4>
        <p>Welcome to your Security Training Dashboard. Here you can access all of you training.
        To see your assigned training visit the "<a href="/my-courses">My Courses</a>" link on the left side menu.</p>
        <p>Once you are enrolled in a course you will receive an email notification that contains a link to access the course,
        or else you can login here directly. You\'ll receive periodic reminder email until you have completed your assigned courses.
        You have a limited time to complete a course but you can leave a course at any point and you\'re progress will be saved for you to resume at anytime.</p>
        <p>Once you have completed a course you will receive a certificate that you can download or print for your reference.
        You can find these in <a href="/my-certificates">"My Certificates"</a>.
        Each course requires you to pass a short quiz to ensure that you have understood the material.
        If you fail the quiz you are able to retake until you achieve a passing grade. </p>
        <p><a href="/my-schedules">"My Schedules"</a> provides a calendar view of courses that you have been enrolled in,
        the number of days you have to complete the course and the course deadline. </p>
        <p>If you have any problems you can create a support ticket at <a href="/my-tickets">"My Tickets"</a>
        or send an email to: <a href="mailto:support@securitytrain.me">support@securitytrain.me</a>.</p>
        <div class="row justify-content-center pt-4">
        <div class="d-flex flex-wrap justify-content-center bg-info p-3 align-items-center shadow rounded">
            <div class="text-center">
            <i class="fa fa-life-ring pr-3" style="font-size: 70px"></i>
        </div>
        <div class="p-2">
            <h4 >Have any problems with your courses?</h4>
            <p class="mb-0">Submit a ticket and we\'ll get back to you as soon as we can.</p>
        </div>
        <div  class="pt-2 pb-2">
            <a href="javascript:void()"  class="btn btn-lg btn-outline-light ml-3 mr-3">SUBMIT A TICKET</a>
        </div>';

        $tpl = new \App\WelcomeTemplate;
        $tpl->language = 'en';
        $tpl->content = $en_content;
        $tpl->company_id = 0;

        $tpl->save();
    }
}
