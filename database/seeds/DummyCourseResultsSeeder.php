<?php

use Illuminate\Database\Seeder;

class DummyCourseResultsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $minTime = strtotime('00:10:00');
        $maxTime = strtotime('01:00:00');
        $minDate = strtotime('2018-08-01');
        $maxDate = strtotime('2018-09-03');
        
        
        $completion = ['completed', 'incomplete'];
        $satisfied_status = ['passed', 'failed'];
        
        $courses = \App\Course::all();
        foreach($courses  as $course)
        {
            $courseUsers = \App\CourseUser::where('course_id', $course->id)->get();
            $modules = $course->modules;
            foreach($courseUsers as $courseUser)
            {
                foreach($modules as $module)
                {
                    $result = \App\CourseResult::where('courseuser_id', $courseUser->id)->where('module_id', $module->id)->first();
                    if(!$result)
                        $result = new \App\CourseResult;
                    
                    $result->courseuser_id = $courseUser->id;
                    $result->module_id = $module->id;
                    
                    $randComplete = rand(0,1);
                    $result->complete_status = $completion[$randComplete];
                    $result->satisfied_status = $satisfied_status[$randComplete];
                    if($module->type == 'Elearning'){
                        $randomTime = rand($minTime, $maxTime);
                        $result->total_time =date('H:i:s', $randomTime);
                        $result->score = rand(60,100);
                    }
                    if($result->complete_status == 'completed')
                    {
                        $randomDateTime = rand($minDate, $maxDate);
                        $result->completion_date = date('Y-m-d H:i:s', $randomDateTime);
                    }
                    if($result->save())
                        \App\CourseUser::updateResult($course->id, $courseUser->user_id);

                }
            }
        }
    }
}
