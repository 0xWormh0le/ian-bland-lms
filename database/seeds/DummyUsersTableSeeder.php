<?php

use Illuminate\Database\Seeder;

class DummyUsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker\Factory::create();

        $role = \App\Role::whereNull('company_id')->where('is_client', true)->where('role_name', 'Learner')->first();

        $minDate = strtotime('2018-06-01');
        $maxDate = strtotime('2018-09-03');

        $companies = \App\Company::all();
        foreach($companies as $company)
        {
            $teams = \App\Team::where('company_id', $company->id)->get();
            foreach($teams as $team)
            {
                $limit = rand(3,10);
                for ($i = 0; $i < $limit; $i++) {
                    $record = new \App\User;
                    $record->company_id = $company->id;
                    $record->team_id = $team->id;
                    $record->role_id = $role->id;
                    $record->first_name = $faker->firstName;
                    $record->last_name = $faker->lastName;
                    $record->email = $faker->unique()->freeEmail;
                    $record->password = bcrypt('123456');
                    $record->role = 1;
                    $record->active = true;
                    $record->is_verified = true;
                    $randomDateTime = rand($minDate, $maxDate);
                    $record->created_at = date('Y-m-d H:i:s', $randomDateTime);
                    $record->save();
                }
            }
        }
    }
}
