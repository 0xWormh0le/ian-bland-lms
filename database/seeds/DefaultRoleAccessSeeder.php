<?php

use Illuminate\Database\Seeder;

class DefaultRoleAccessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $adminRoles = "";

        DB::table('roles')->where('role_name', 'Admin')
                        ->update([
                            'role_access' => $adminRoles
                        ]);
    }
}
