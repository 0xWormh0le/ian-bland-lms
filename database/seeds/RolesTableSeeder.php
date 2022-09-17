<?php

use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('roles')->delete();
        
        \DB::table('roles')->insert(array (
            0 => 
            array (
                'id' => 1,
                'company_id' => NULL,
                'role_name' => 'Admin',
                'role_access' => 'my-schedules.index,courses.index,user-management,user-management.roles.index,user-management.roles.show,user-management.roles.create,user-management.roles.edit,user-management.roles.destroy,user-management.teams.index,user-management.teams.show,user-management.teams.create,user-management.teams.edit,user-management.teams.destroy,user-management.users.index,user-management.users.show,user-management.users.create,user-management.users.edit,user-management.users.destroy,user-management.roles,user-management.teams,user-management.users,portal-management,portal-management.roles.index,portal-management.roles.show,portal-management.roles.create,portal-management.roles.edit,portal-management.roles.destroy,portal-management.users.index,portal-management.users.show,portal-management.users.create,portal-management.users.edit,portal-management.users.destroy,portal-management.teams.index,portal-management.teams.show,portal-management.teams.create,portal-management.teams.edit,portal-management.teams.destroy,portal-management.configuration.index,portal-management.configuration.email-setup,portal-management.configuration.smtp-account,portal-management.roles,portal-management.users,portal-management.teams,tickets,tickets.index,tickets.open,tickets.closed,tickets.assign,tickets.respond,tickets.myticket,reports.superadmin.index',
                'is_client' => 1,
                'is_learner' => 0,
                'created_by' => 1,
                'updated_by' => 1,
                'deleted_by' => NULL,
                'created_at' => '2019-01-07 13:30:58',
                'updated_at' => '2019-06-27 11:58:09',
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'company_id' => NULL,
                'role_name' => 'Learner',
                'role_access' => 'my-schedules.index,my-courses.index,my-certificates.index,tickets.myticket',
                'is_client' => 1,
                'is_learner' => 1,
                'created_by' => 1,
                'updated_by' => 1,
                'deleted_by' => NULL,
                'created_at' => '2019-01-07 13:31:38',
                'updated_at' => '2019-06-27 12:15:39',
                'deleted_at' => NULL,
            ),
        ));
        
        
    }
}