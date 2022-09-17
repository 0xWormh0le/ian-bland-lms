<?php

use Illuminate\Database\Seeder;

class DummyCompaniesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker\Factory::create();
        $faker->addProvider(new Faker\Provider\en_US\Company($faker));

        $limit = 5;
        for ($i = 0; $i < $limit; $i++) {
            $record = new \App\Company;
            $record->company_name = $faker->company;
            $record->active = true;
            $record->save();
        }
    }
}
