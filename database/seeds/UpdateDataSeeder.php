<?php

use Illuminate\Database\Seeder;

class UpdateDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Update `Roles` table set value is_learner = true WHERE  has `my-course` role_access
        foreach(DB::table('roles')->where('role_access', 'LIKE', '%my-courses%')->get() as $r)
        {
            DB::table('roles')->where('id', $r->id)->update(['is_learner' => true]);
        }
    }
}
