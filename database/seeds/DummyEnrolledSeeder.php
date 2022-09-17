<?php

use Illuminate\Database\Seeder;

class DummyEnrolledSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Enrolled Company to Course
        $companies = \App\Company::all();
        $courses = \App\Course::all();

        $role = \App\Role::whereNull('company_id')->where('is_client', true)->where('role_name', 'Learner')->first();

        $minDate = strtotime('2018-06-01');
        $maxDate = strtotime('2018-09-03');

        foreach($courses  as $course)
        {
            foreach($companies as $company)
            {
                $record = \App\CourseCompany::withTrashed()->where('course_id', $course->id) ->where('company_id', $company->id)->first();
                if(!$record)
                    $record = new \App\CourseCompany;
                $record->course_id = $course->id;
                $record->company_id = $company->id;
                $record->active = true;
                $record->deleted_at = null;
                $record->save();

                // Enroll All users of company to Course
                $users = \App\User::where('company_id', $company->id)->where('role_id', $role->id)->get();
                foreach($users as $user)
                {
                    $enrolledUser = \App\CourseUser::withTrashed()->where('user_id', $user->id)->where('course_id', $course->id)->first();
                    if(!$enrolledUser)
                        $enrolledUser = new \App\CourseUser;
                    $enrolledUser->course_id = $course->id;
                    $enrolledUser->user_id = $user->id;
                    $enrolledUser->course_member_id = $record->id;
                    $enrolledUser->role = 1;
                    $enrolledUser->active = true;

                    $randomDateTime = rand($minDate, $maxDate);
                    $enrolledUser->enrol_date = date('Y-m-d H:i:s', $randomDateTime);
                    $enrolledUser->enrolled_by = 1;
                    $enrolledUser->deleted_at =null;
                    $enrolledUser->save();
                }

            }
        }




    }
}
