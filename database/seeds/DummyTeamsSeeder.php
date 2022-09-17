<?php

use Illuminate\Database\Seeder;

class DummyTeamsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker\Factory::create();

        $companies = \App\Company::all();
        foreach($companies as $company)
        {
            $limit = rand(1,3);
            for ($i = 0; $i < $limit; $i++) {
                $record = new \App\Team;
                $record->company_id = $company->id;
                $record->team_name = $faker->jobTitle;
                $record->active = true;
                $record->save();
            }
        }
    }
}
