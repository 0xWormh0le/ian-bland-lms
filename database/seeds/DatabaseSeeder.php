<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UsersTableSeeder::class);
        $this->call(EmailTemplatesTableSeeder::class);
        $this->call(CertificateDesignsTableSeeder::class);
        $this->call(MenusTableSeeder::class);
        $this->call(RolesTableSeeder::class);
        $this->call(WelcomeTemplateTableSeeder::class);
        //Dummy Seeder :
      //  $this->call(DummyCompaniesSeeder::class);
      //  $this->call(DummyTeamsSeeder::class);
      //  $this->call(DummyUsersTableSeeder::class);
      //  $this->call(DummyEnrolledSeeder::class);
      //  $this->call(DummyCourseResultsSeeder::class);


    }
}
