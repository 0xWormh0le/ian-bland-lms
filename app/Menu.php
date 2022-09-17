<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Menu extends Model
{
    use SoftDeletes;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    public function sysAdminMenu()
    {
        return [
            'companies' => [
                'icon'  => 'far fa-building',
                'route' => 'companies.index',
                'children' => [],
            ],
            'user-management' => [
                'icon' => 'fa fa-users-cog',
                'children' => [
                    'user-management.roles' => [
                        'route' => 'roles.index',
                        'icon' => 'fa fa-user-shield'
                    ],
                    'user-management.teams' => [
                        'route' => 'teams.index',
                        'icon' => 'fa fa-user-friends'
                    ],
                    'user-management.users' => [
                        'route' => 'users.index',
                        'icon' => 'fa fa-users'
                    ]
                ],
            ],
            'courses' => [
                'icon'  => 'fa fa-chalkboard-teacher',
                'children' => [
                  'courses.courses' => [
                    'icon'  => 'fa fa-chalkboard-teacher',
                    'route' => 'courses.index'
                  ],
                  'company-course-management' => [
                    'icon'  => 'fa fa-list',
                    'route' => 'company.courses.index'
                  ],
                  'courses.category' => [
                    'icon'  => 'fa fa-list',
                    'route' => 'category.index'
                  ],
                ],
            ],
           'configuration' => [
                'icon' => 'fa fa-cogs',
                'children' => [
                    'configuration.system' => [
                        'route' => 'setting.index',
                        'icon' => 'fa fa-sliders-h'
                    ],
                    'configuration.certificate-templates' => [
                        'route' => 'certificate-templates.index',
                        'icon' => 'fa fa-award'
                    ],
                    'configuration.email-setup' => [
                        'route' => 'email-setup.index',
                        'icon' => 'fa fa-envelope-open-o'
                    ],
                    'configuration.smtp-account' => [
                        'route' => 'smtp-account.index',
                        'icon' => 'fa fa-at'
                    ],
                    'configuration.pusher' => [
                        'route' => 'pusher.index',
                        'icon' => 'fa fa-comments'
                    ],
                    'configuration.menu-management' => [
                        'route' => 'menu.index',
                        'icon' => 'fa fa-list'
                    ],
                ],
            ],
            'tickets' => [
                'icon' => 'fa fa-ticket-alt',
                'children' => [
                    'tickets.dashboard' => [
                        'route' => 'tickets.index',
                        'icon' => 'fa fa-mail-bulk'
                    ],
                    'tickets.open' => [
                        'route' => 'tickets.open',
                        'icon' => 'fa fa-envelope-open-text'
                    ],
                    'tickets.closed' => [
                        'route' => 'tickets.closed',
                        'icon' => 'fa fa-envelope'
                    ],
                ],
            ],
            'reports' => [
                'icon'  => 'fa fa-file-text-o',
                'children' => [
                  'reports.user' => [
                      'route' => 'reports.superadmin.index',
                      'icon' => 'fa fa-users'
                  ],
                  'reports.course' => [
                      'route' => 'reports.course',
                      'icon' => 'fa fa-chalkboard-teacher'
                  ],
                  'reports.login-log' => [
                      'route' => 'reports.log',
                      'icon' => 'fa fa-history'
                  ],
                ],
            ],
        ];
    }


    public function clientMenu()
    {
        return [
            'my-courses' => [
                'icon'  => 'fa fa-tasks',
                'route' => 'my-courses.index',
                'children' => [],
            ],
            'my-schedules' => [
                'icon'  => 'far fa-calendar-alt',
                'route' => 'my-schedules.index',
                'children' => [],
            ],
            'my-certificates' => [
                'icon'  => 'fa fa-award',
                'route' => 'my-certificates.index',
                'children' => [],
            ],
            'course-management' => [
                'icon'  => 'fa fa-chalkboard-teacher',
                'route' => 'courses.index',
                'children' => [],
            ],
            'user-management' => [
                'icon' => 'fa fa-users-cog',
                'children' => [
                    'user-management.roles' => [
                        'route' => 'roles.index',
                        'icon' => 'fa fa-user-shield'
                    ],
                    'user-management.teams' => [
                        'route' => 'teams.index',
                        'icon' => 'fa fa-user-friends'
                    ],
                    'user-management.users' => [
                        'route' => 'users.index',
                        'icon' => 'fa fa-users'
                    ]
                ],
            ],
          /*  'reports' => [
                'icon'  => 'fa fa-file-invoice',
                'route' => 'reports.index',
                'children' => [],
            ],*/

            // 'reports' => [
            //     'icon' => 'fa fa-file-invoice',
            //     'children' => [
            //         'Enrollment' => [
            //             'route' => 'reports.enrollment',
            //             'icon' => 'fa fa-id-card'
            //         ],
            //         'Chart' => [
            //             'route' => 'reports.chart.index',
            //             'icon' => 'fa fa-chart-bar'
            //         ],
            //         // 'Course' => [
            //         //     'route' => 'roles.index',
            //         //     'icon' => 'fa fa-file-signature'
            //         // ],
            //     ]
            // ],

            'portal-management' => [
                'icon' => 'fa fa-laptop-code',
                'children' => [
                    'portal-management.configuration' => [
                        'route' => 'configuration.index',
                        'icon' => 'fa fa-cogs'
                    ],
                    'configuration.welcome' => [
                        'route' => 'welcome-config.show',
                        'icon' => 'fa fa-grin-alt'
                    ],
                    'configuration.email-setup' => [
                        'route' => 'email-setup.index',
                        'icon' => 'fa fa-envelope-open-o'
                    ],
                    'configuration.smtp-account' => [
                        'route' => 'client-smtp-account.index',
                        'icon' => 'fa fa-at'
                    ],
                    'configuration.certificate-config' => [
                        'route' => 'client-certificate-config.index',
                        'icon' => 'fa fa-award'
                    ],
                ],
            ],
            'tickets' => [
                'icon' => 'fa fa-ticket-alt',
                'children' => [
                    'tickets.dashboard' => [
                        'route' => 'tickets.index',
                        'icon' => 'fa fa-mail-bulk'
                    ],
                    'tickets.myticket' => [
                        'route' => 'my-tickets.index',
                        'icon' => 'fa fa-envelope-open-text'
                    ],
                    'tickets.open' => [
                        'route' => 'tickets.open',
                        'icon' => 'fa fa-envelope-open-text'
                    ],
                    'tickets.closed' => [
                        'route' => 'tickets.closed',
                        'icon' => 'fa fa-envelope'
                    ],
                ],
            ],
          'reports' => [
            'icon'  => 'fa fa-file-text-o',
            'children' => [
              'reports.user' => [
                  'route' => 'reports.superadmin.index',
                  'icon' => 'fa fa-users'
              ],
              'reports.course' => [
                  'route' => 'reports.course',
                  'icon' => 'fa fa-chalkboard-teacher'
              ],
              'reports.login-log' => [
                'route' => 'reports.log',
                'icon' => 'fa fa-history'
              ],
            ],
          ],
        ];
    }

    public static function findMenu($client_level, $menu_id)
    {
        if(in_array($menu_id, ['elearning']))
            $client_level = true;

        $data = self::where('client_level', $client_level)
                    ->where('company_id', \Auth::user()->company_id)
                    ->where('menu_id','=', $menu_id)
                    ->first();
        if(!$data)
            $data = self::where('client_level', $client_level)
                    ->whereNull('company_id')
                    ->where('menu_id','=', $menu_id)
                    ->first();
        return $data;
    }
}
