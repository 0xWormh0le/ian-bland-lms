<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'roles';

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    public function company()
    {
        return $this->belongsTo('App\Company', 'company_id', 'id');
    }

    public function creator()
    {
        return $this->hasOne('App\User', 'id', 'created_by');
    }

    public function updater()
    {
        return $this->hasOne('App\User', 'id', 'updated_by');
    }

    public function clientRoles()
    {
        $roles = [
            'my-schedules.index' => trans("menu.my-schedules"),
            'my-courses.index' => trans("menu.my-courses"),
            'my-certificates.index' => trans("menu.my-certificates"),
            'courses.index' => trans("menu.course-management"),
            'user-management' => [
                'label' => trans("menu.user-management"),
                'menus' => [
                    'roles' => [
                        'label' => trans("menu.user-management.roles"),
                        'menus' => [
                            'index'     => trans("menu.view"),
                            'show'      => trans("menu.detail"),
                            'create'    => trans("menu.create"),
                            'edit'      => trans("menu.edit"),
                            'destroy'   => trans("menu.delete"),
                        ],
                    ],
                    'teams' => [
                        'label' => trans("menu.user-management.teams"),
                        'menus' => [
                            'index'     => trans("menu.view"),
                            'show'      => trans("menu.detail"),
                            'create'    => trans("menu.create"),
                            'edit'      => trans("menu.edit"),
                            'destroy'   => trans("menu.delete"),
                        ],
                    ],
                    'users' => [
                        'label' => trans("menu.user-management.users"),
                        'menus' => [
                            'index'     => trans("menu.view"),
                            'show'      => trans("menu.detail"),
                            'create'    => trans("menu.create"),
                            'edit'      => trans("menu.edit"),
                            'destroy'   => trans("menu.delete"),
                        ],
                    ],
                ],
            ],
            'portal-management' => [
                'label' => trans("menu.portal-management"),
                'menus' => [
                    'configuration.index' => [
                        'label' => trans("menu.portal-management.configuration"),
                        'menus' => [],
                    ],
                    'configuration.welcome' => [
                        'label' => trans("menu.configuration.welcome"),
                        'menus' => [],
                        'icon'  => 'fa fa-grin-alt',
                        'route' => 'welcome-config.show'
                    ],
                    'configuration.email-setup' => [
                        'label' => trans("menu.configuration.email-setup"),
                        'menus' => [],
                        'route' => 'email-setup.index',
                        'icon' => 'fa fa-envelope-open-o'
                    ],
                    'configuration.smtp-account' => [
                        'label' => trans("menu.configuration.smtp-account"),
                        'menus' => [],
                        'route' => 'smtp-account.index',
                        'icon' => 'fa fa-at'
                    ],
                    // 'roles' => [
                    //     'label' => trans("menu.portal-management.role-access"),
                    //     'menus' => [
                    //         'index'     => trans("menu.view"),
                    //         'show'      => trans("menu.detail"),
                    //         'create'    => trans("menu.create"),
                    //         'edit'      => trans("menu.edit"),
                    //         'destroy'   => trans("menu.delete"),
                    //     ],
                    // ],
                    // 'users' => [
                    //     'label' => trans("menu.user-management"),
                    //     'menus' => [
                    //         'index'     => trans("menu.view"),
                    //         'show'      => trans("menu.detail"),
                    //         'create'    => trans("menu.create"),
                    //         'edit'      => trans("menu.edit"),
                    //         'destroy'   => trans("menu.delete"),
                    //     ],
                    // ],
                    // 'teams' => [
                    //     'label' => trans("menu.user-management.teams"),
                    //     'menus' => [
                    //         'index'     => trans("menu.view"),
                    //         'show'      => trans("menu.detail"),
                    //         'create'    => trans("menu.create"),
                    //         'edit'      => trans("menu.edit"),
                    //         'destroy'   => trans("menu.delete"),
                    //     ],
                    // ],
                ],
            ],
            'tickets' => [
                'label' => trans("menu.tickets"),
                'menus' => [
                    'index'     => trans("menu.tickets.dashboard"),
                    'open'      => trans("menu.tickets.read-tickets"),
                    'closed'    => trans("menu.tickets.closed"),
                    'assign'    => trans("menu.tickets.assign"),
                    'respond'   => trans("menu.tickets.respond"),
                    'myticket'  => trans("menu.tickets.myticket")
                ],
            ],
          'reports.superadmin.index' => trans("menu.reports"),
        ];

        return $roles;
    }

    public function systemRoles()
    {

        $roles = [
            'companies' => [
                'label' => trans("menu.companies"),
                'menus' => [
                    'index'     => trans("menu.view"),
                    'show'      => trans("menu.detail"),
                    'create'    => trans("menu.create"),
                    'edit'      => trans("menu.edit"),
                    'destroy'   => trans("menu.delete"),
                ],
            ],
            'user-management' => [
                'label' => trans("menu.user-management"),
                'menus' => [
                    'roles' => [
                        'label' => trans("menu.user-management.roles"),
                        'menus' => [
                            'index'     => trans("menu.view"),
                            'show'      => trans("menu.detail"),
                            'create'    => trans("menu.create"),
                            'edit'      => trans("menu.edit"),
                            'destroy'   => trans("menu.delete"),
                          ],
                    ],
                    'teams' => [
                        'label' => trans("menu.user-management.teams"),
                        'menus' => [
                            'index'     => trans("menu.view"),
                            'show'      => trans("menu.detail"),
                            'create'    => trans("menu.create"),
                            'edit'      => trans("menu.edit"),
                            'destroy'   => trans("menu.delete"),
                        ],
                    ],
                    'users' => [
                        'label' => trans("menu.user-management.users"),
                        'menus' => [
                            'index'     => trans("menu.view"),
                            'show'      => trans("menu.detail"),
                            'create'    => trans("menu.create"),
                            'edit'      => trans("menu.edit"),
                            'destroy'   => trans("menu.delete"),
                            ],
                    ],
                ],
            ],
            'courses' => [
                'label' => trans("menu.courses.courses"),
                'menus' => [
                    'index'     => trans("menu.view"),
                    'show'      => trans("menu.detail"),
                    'create'    => trans("menu.create"),
                    'edit'      => trans("menu.edit"),
                    'destroy'   => trans("menu.delete"),
                    'config'    => trans("menu.rules_update"),
                  ],
            ],
            'tickets' => [
                'label' => trans("menu.tickets"),
                'menus' => [
                    'index'     => trans("menu.tickets.dashboard"),
                    'open'      => trans("menu.tickets.read-tickets"),
                    'closed'    => trans("menu.tickets.closed"),
                    'assign'    => trans("menu.tickets.assign"),
                    'respond'   => trans("menu.tickets.respond"),
                    'myticket'  => trans("menu.tickets.myticket")
                ],
            ],

        ];

        return $roles;
    }

    public static function companyRole()
    {
        $roles = [];
        $default = self::whereNull('company_id')->pluck('role_name', 'id');
        foreach($default as $k => $v)
            $roles[$k] = $v;

        $company = self::whereCompanyId(\Auth::user()->company_id)->pluck('role_name', 'id');
        foreach($default as $k => $v)
            $roles[$k] = $v;

        return $roles;
    }

    public static function getLists($company_id = null)
    {
        $data = self::select('id', 'role_name', 'company_id');

        if($company_id)
        {
            $data->where('company_id', $company_id)
                    ->orWhereNull('company_id')
                    ->where('is_client', 1);
        }else{
            $data->where('company_id', \Auth::user()->company_id);
        }

        $data = $data->orderBy('company_id', 'desc')->orderBy('role_name')->get();

        $lists = [];
        foreach($data as $r)
            $lists[$r->id] = $r->role_name .(!$r->company_id ? ' (system default)':'');
        return $lists;
    }

    public static function getByRole($role_access)
    {
        $data = self::where('role_access', 'LIKE', '%'.$role_access.'%')
                    ->whereNull('company_id');
        if(\Auth::user()->company_id)
            $data->where('company_id', \Auth::user()->company_id);
        $data = $data->pluck('id')->toArray();
        return $data;
    }

}
