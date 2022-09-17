<?php

use Illuminate\Database\Seeder;

class MenusTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('menus')->delete();
        
        \DB::table('menus')->insert(array (
            0 => 
            array (
                'id' => 1,
                'company_id' => NULL,
                'client_level' => 0,
                'menu_id' => 'companies',
                'label' => 'Companies',
                'created_at' => '2019-01-12 12:04:43',
                'updated_at' => '2019-01-12 12:04:43',
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'company_id' => NULL,
                'client_level' => 0,
                'menu_id' => 'user-management',
                'label' => 'User Management',
                'created_at' => '2019-01-12 12:04:43',
                'updated_at' => '2019-01-12 12:04:43',
                'deleted_at' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'company_id' => NULL,
                'client_level' => 0,
                'menu_id' => 'user-management.roles',
                'label' => 'Roles',
                'created_at' => '2019-01-12 12:04:43',
                'updated_at' => '2019-01-12 12:04:43',
                'deleted_at' => NULL,
            ),
            3 => 
            array (
                'id' => 4,
                'company_id' => NULL,
                'client_level' => 0,
                'menu_id' => 'user-management.teams',
                'label' => 'Teams',
                'created_at' => '2019-01-12 12:04:43',
                'updated_at' => '2019-01-12 12:04:43',
                'deleted_at' => NULL,
            ),
            4 => 
            array (
                'id' => 5,
                'company_id' => NULL,
                'client_level' => 0,
                'menu_id' => 'user-management.users',
                'label' => 'Users',
                'created_at' => '2019-01-12 12:04:43',
                'updated_at' => '2019-01-12 12:04:43',
                'deleted_at' => NULL,
            ),
            5 => 
            array (
                'id' => 6,
                'company_id' => NULL,
                'client_level' => 0,
                'menu_id' => 'course-management',
                'label' => 'Courses Management',
                'created_at' => '2019-01-12 12:04:43',
                'updated_at' => '2019-01-12 12:04:43',
                'deleted_at' => NULL,
            ),
            6 => 
            array (
                'id' => 7,
                'company_id' => NULL,
                'client_level' => 0,
                'menu_id' => 'configuration',
                'label' => 'Configuration',
                'created_at' => '2019-01-12 12:04:43',
                'updated_at' => '2019-01-12 12:04:43',
                'deleted_at' => NULL,
            ),
            7 => 
            array (
                'id' => 8,
                'company_id' => NULL,
                'client_level' => 0,
                'menu_id' => 'configuration.system',
                'label' => 'System',
                'created_at' => '2019-01-12 12:04:43',
                'updated_at' => '2019-01-12 12:04:43',
                'deleted_at' => NULL,
            ),
            8 => 
            array (
                'id' => 9,
                'company_id' => NULL,
                'client_level' => 0,
                'menu_id' => 'configuration.certificate-templates',
                'label' => 'Certificate Templates',
                'created_at' => '2019-01-12 12:04:43',
                'updated_at' => '2019-01-12 12:04:43',
                'deleted_at' => NULL,
            ),
            9 => 
            array (
                'id' => 10,
                'company_id' => NULL,
                'client_level' => 0,
                'menu_id' => 'configuration.email-setup',
                'label' => 'Email Setup',
                'created_at' => '2019-01-12 12:04:43',
                'updated_at' => '2019-01-12 12:04:43',
                'deleted_at' => NULL,
            ),
            10 => 
            array (
                'id' => 11,
                'company_id' => NULL,
                'client_level' => 0,
                'menu_id' => 'configuration.smtp-account',
                'label' => 'SMTP Account',
                'created_at' => '2019-01-12 12:04:43',
                'updated_at' => '2019-01-12 12:04:43',
                'deleted_at' => NULL,
            ),
            11 => 
            array (
                'id' => 12,
                'company_id' => NULL,
                'client_level' => 0,
                'menu_id' => 'configuration.scorm-dispatch-api',
                'label' => 'SCORM Dispatch API',
                'created_at' => '2019-01-12 12:04:43',
                'updated_at' => '2019-01-12 12:04:43',
                'deleted_at' => NULL,
            ),
            12 => 
            array (
                'id' => 13,
                'company_id' => NULL,
                'client_level' => 0,
                'menu_id' => 'configuration.pusher',
                'label' => 'Pusher Channel',
                'created_at' => '2019-01-12 12:04:43',
                'updated_at' => '2019-01-12 12:04:43',
                'deleted_at' => NULL,
            ),
            13 => 
            array (
                'id' => 14,
                'company_id' => NULL,
                'client_level' => 0,
                'menu_id' => 'configuration.menu-management',
                'label' => 'Menu Management',
                'created_at' => '2019-01-12 12:04:43',
                'updated_at' => '2019-01-12 12:04:43',
                'deleted_at' => NULL,
            ),
            14 => 
            array (
                'id' => 15,
                'company_id' => NULL,
                'client_level' => 0,
                'menu_id' => 'tickets',
                'label' => 'Tickets',
                'created_at' => '2019-01-12 12:04:43',
                'updated_at' => '2019-01-12 12:04:43',
                'deleted_at' => NULL,
            ),
            15 => 
            array (
                'id' => 16,
                'company_id' => NULL,
                'client_level' => 0,
                'menu_id' => 'tickets.dashboard',
                'label' => 'Dashboard',
                'created_at' => '2019-01-12 12:04:43',
                'updated_at' => '2019-01-12 12:04:43',
                'deleted_at' => NULL,
            ),
            16 => 
            array (
                'id' => 17,
                'company_id' => NULL,
                'client_level' => 0,
                'menu_id' => 'tickets.open',
                'label' => 'Open Tickets',
                'created_at' => '2019-01-12 12:04:43',
                'updated_at' => '2019-01-12 12:04:43',
                'deleted_at' => NULL,
            ),
            17 => 
            array (
                'id' => 18,
                'company_id' => NULL,
                'client_level' => 0,
                'menu_id' => 'tickets.closed',
                'label' => 'Closed Tickets',
                'created_at' => '2019-01-12 12:04:43',
                'updated_at' => '2019-01-12 12:04:43',
                'deleted_at' => NULL,
            ),
            18 => 
            array (
                'id' => 19,
                'company_id' => NULL,
                'client_level' => 1,
                'menu_id' => 'my-schedules',
                'label' => 'My Schedules',
                'created_at' => '2019-01-12 12:04:43',
                'updated_at' => '2019-01-12 12:04:43',
                'deleted_at' => NULL,
            ),
            19 => 
            array (
                'id' => 20,
                'company_id' => NULL,
                'client_level' => 1,
                'menu_id' => 'my-courses',
                'label' => 'My Courses',
                'created_at' => '2019-01-12 12:04:43',
                'updated_at' => '2019-01-12 12:04:43',
                'deleted_at' => NULL,
            ),
            20 => 
            array (
                'id' => 21,
                'company_id' => NULL,
                'client_level' => 1,
                'menu_id' => 'my-certificates',
                'label' => 'My Certificates',
                'created_at' => '2019-01-12 12:04:44',
                'updated_at' => '2019-01-12 12:04:44',
                'deleted_at' => NULL,
            ),
            21 => 
            array (
                'id' => 22,
                'company_id' => NULL,
                'client_level' => 1,
                'menu_id' => 'course-management',
                'label' => 'Courses Management',
                'created_at' => '2019-01-12 12:04:44',
                'updated_at' => '2019-01-12 12:04:44',
                'deleted_at' => NULL,
            ),
            22 => 
            array (
                'id' => 23,
                'company_id' => NULL,
                'client_level' => 1,
                'menu_id' => 'reports',
                'label' => 'Reports',
                'created_at' => '2019-01-12 12:04:44',
                'updated_at' => '2019-01-12 12:04:44',
                'deleted_at' => NULL,
            ),
            23 => 
            array (
                'id' => 24,
                'company_id' => NULL,
                'client_level' => 1,
                'menu_id' => 'portal-management',
                'label' => 'Portal Management',
                'created_at' => '2019-01-12 12:04:44',
                'updated_at' => '2019-01-12 12:04:44',
                'deleted_at' => NULL,
            ),
            24 => 
            array (
                'id' => 25,
                'company_id' => NULL,
                'client_level' => 1,
                'menu_id' => 'portal-management.configuration',
                'label' => 'Configuration',
                'created_at' => '2019-01-12 12:04:44',
                'updated_at' => '2019-01-12 12:04:44',
                'deleted_at' => NULL,
            ),
            25 => 
            array (
                'id' => 26,
                'company_id' => NULL,
                'client_level' => 1,
                'menu_id' => 'portal-management.role-access',
                'label' => 'Role Access',
                'created_at' => '2019-01-12 12:04:44',
                'updated_at' => '2019-01-12 12:04:44',
                'deleted_at' => NULL,
            ),
            26 => 
            array (
                'id' => 27,
                'company_id' => NULL,
                'client_level' => 1,
                'menu_id' => 'portal-management.user-management',
                'label' => 'User Management',
                'created_at' => '2019-01-12 12:04:44',
                'updated_at' => '2019-01-12 12:04:44',
                'deleted_at' => NULL,
            ),
            27 => 
            array (
                'id' => 28,
                'company_id' => NULL,
                'client_level' => 1,
                'menu_id' => 'portal-management.teams',
                'label' => 'Teams',
                'created_at' => '2019-01-12 12:04:44',
                'updated_at' => '2019-01-12 12:04:44',
                'deleted_at' => NULL,
            ),
            28 => 
            array (
                'id' => 29,
                'company_id' => NULL,
                'client_level' => 1,
                'menu_id' => 'tickets',
                'label' => 'Tickets',
                'created_at' => '2019-01-12 12:04:44',
                'updated_at' => '2019-01-12 12:04:44',
                'deleted_at' => NULL,
            ),
            29 => 
            array (
                'id' => 30,
                'company_id' => NULL,
                'client_level' => 1,
                'menu_id' => 'tickets.dashboard',
                'label' => 'Dashboard',
                'created_at' => '2019-01-12 12:04:44',
                'updated_at' => '2019-01-12 12:04:44',
                'deleted_at' => NULL,
            ),
            30 => 
            array (
                'id' => 31,
                'company_id' => NULL,
                'client_level' => 1,
                'menu_id' => 'tickets.open',
                'label' => 'Open Tickets',
                'created_at' => '2019-01-12 12:04:44',
                'updated_at' => '2019-01-12 12:04:44',
                'deleted_at' => NULL,
            ),
            31 => 
            array (
                'id' => 32,
                'company_id' => NULL,
                'client_level' => 1,
                'menu_id' => 'tickets.closed',
                'label' => 'Closed Tickets',
                'created_at' => '2019-01-12 12:04:44',
                'updated_at' => '2019-01-12 12:04:44',
                'deleted_at' => NULL,
            ),
            32 => 
            array (
                'id' => 33,
                'company_id' => NULL,
                'client_level' => 1,
                'menu_id' => 'elearning',
                'label' => 'Elearning',
                'created_at' => '2019-01-12 12:04:44',
                'updated_at' => '2019-01-12 12:04:44',
                'deleted_at' => NULL,
            ),
            33 => 
            array (
                'id' => 36,
                'company_id' => NULL,
                'client_level' => 0,
                'menu_id' => 'courses.courses',
                'label' => 'Courses',
                'created_at' => NULL,
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            34 => 
            array (
                'id' => 37,
                'company_id' => NULL,
                'client_level' => 0,
                'menu_id' => 'courses.category',
                'label' => 'Course Category',
                'created_at' => NULL,
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            35 => 
            array (
                'id' => 38,
                'company_id' => NULL,
                'client_level' => 0,
                'menu_id' => 'document',
                'label' => 'Document',
                'created_at' => '2019-02-26 00:00:00',
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            36 => 
            array (
                'id' => 39,
                'company_id' => NULL,
                'client_level' => 1,
                'menu_id' => 'reports.superadmin.index',
                'label' => 'Report',
                'created_at' => NULL,
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            37 => 
            array (
                'id' => 40,
                'company_id' => NULL,
                'client_level' => 1,
                'menu_id' => 'reports.superadmin.index',
                'label' => 'Reports',
                'created_at' => NULL,
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
        ));
        
        
    }
}