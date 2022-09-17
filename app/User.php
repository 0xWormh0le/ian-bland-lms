<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Auth, App\Menu;
use Illuminate\Support\Facades\Input ;

class User extends Authenticatable
{
    use Notifiable;
    use SoftDeletes;
    use \Lab404\Impersonate\Models\Impersonate;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
      'first_name', 'role', 'role_id', 'name', 'email', 'password', 'google2fa_secret', 'last_login_at', 'last_login_ip',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'google2fa_secret',
    ];

    /**
     * Check if User is System Admin
     */
    public function isSysAdmin()
    {
        if(Auth::user()->company_id == null && Auth::user()->role == 0)
        {
            return true;
        }
        return false;
    }

    /**
     * Check if User is Client
     */
    public function isClient()
    {
        if(Auth::user()->company_id > 0 && Auth::user()->role <> 0)
        {
            return true;
        }
        return false;
    }
   /* Check if user is client admin */
    public function isClientAdmin()
    {
        if(Auth::user()->company_id > 0 && Auth::user()->role_id == 1)
        {
            return true;
        }
        return false;
    }

    public function companyAccess($company_id)
    {
        if($this->company_id)
        {
            if($this->company_id !== $company_id)
                return false;
        }
        return true;
    }

    public function canImpersonate()
    {
        // For example
        return $this->isSysAdmin();
    }

    public function getMenu()
    {
        $menu = new Menu;
        $menuList = Auth::user()->isSysAdmin() ? $menu->sysAdminMenu() : $menu->clientMenu();
        $client_level = Auth::user()->company_id ? true : false;

        if($this->roleUser)
            $roles = explode(',',$this->roleUser->role_access);
        elseif($this->role==0)
            $roles = ['superadmin'];
        else
            $roles = [];

//print_r($roles) ; die;
        $html = '';
        $menuLabel = [];

        $userLanguage = \Session::get('locale');
        foreach($menuList as $menu_id => $menu)
        {
            if(count($menu['children']) == 0)
            {
                if(in_array($menu['route'], $roles) || in_array('superadmin', $roles))
                {
                    $label = @Menu::findMenu($client_level, $menu_id)->label ?: __('menu.'.$menu_id);

                    if($userLanguage != "en")
                    {
                      $label = trans("menu.".$menu_id);
                    }

                    $menuLabel[$menu_id] = $label;


                    $url = \Route::has($menu['route']) ? route($menu['route']) : '#';
                    $html .= '
                    <li class="nav-item">
                        <a class="nav-link" href="'.$url.'">
                            <i class="nav-icon '.$menu['icon'].'"></i> '.$label.'
                        </a>
                    </li>';
                }
            }

            else{
                $show = false;
                $routeInDropdown = [];
                foreach($menu['children'] as $prop)
                    $routeInDropdown[] = $prop['route'];
                foreach($routeInDropdown as $r)
                {
                    if(!$show){
                        if(in_array($r, $roles))
                            $show = true;
                        elseif(in_array($menu_id.'.'.$r, $roles))
                            $show = true;
                    }
                }
                if($show || in_array('superadmin', $roles))
                {

                    $label = @Menu::findMenu(Auth::user()->isSysAdmin() ? false : true, $menu_id)->label ?: __('menu.'.$menu_id);

                    if($userLanguage != "en")
                    {
                      $label = trans("menu.".$menu_id);
                    }

                    $menuLabel[$menu_id] = $label;

                    $html .= '
                        <li class="nav-item nav-dropdown">
                            <a class="nav-link nav-dropdown-toggle" href="#">
                                <i class="nav-icon '.$menu['icon'].'"></i> '.$label.'
                            </a>
                            <ul class="nav-dropdown-items">';
                        foreach($menu['children'] as $submenu_id => $prop)
                        {
                            // if (in_array($submenu_id, $roles) ||
                            //     in_array($menu_id.'.'.$prop['route'], $roles) ||
                            //     in_array($menu_id.'.'.$submenu_id, $roles) ||
                            //     in_array('superadmin', $roles) ||
                            //     (in_array('portal-management.configuration.index', $roles) && $submenu_id == 'configuration.certificate-config') ||
                            //     $menu_id == 'reports') {

                                $url = \Route::has($prop['route']) ? route($prop['route']) : '#';
                                $sublabel = @Menu::findMenu($client_level, $submenu_id)->label ?: __('menu.'.$submenu_id);

                                if($userLanguage != "en")
                                {
                                    $sublabel = trans("menu.".$submenu_id);
                                }

                                $menuLabel[$submenu_id] = $sublabel;

                                $html .='
                                    <li class="nav-item">
                                        <a class="nav-link" href="'.$url.'">
                                            <i class="nav-icon '.$prop['icon'].'"></i> '.$sublabel.'
                                        </a>
                                    </li>';
                            // }
                        }
                    $html .='
                            </ul>
                        </li>';
                }

            }
        }

        $moduleLabel = [
            'elearning' => @Menu::findMenu(true, 'elearning')->label ?: __('menu.elearning'),
            'webex' => @Menu::findMenu(true, 'webex')->label ?: __('menu.webex'),
            'classroom' => @Menu::findMenu(true, 'classroom')->label ?: __('menu.classroom'),
            'document' => @Menu::findMenu(true, 'document')->label ?: __('menu.document'),
        ];


        session(['menu' => $html,
                'menuLabel' => $menuLabel,
                'moduleLabel' => $moduleLabel,
            ]);
        return $html;
    }



    public function company()
    {
        return $this->belongsTo('\App\Company', 'company_id', 'id');
    }

    public function roleUser()
    {
        return $this->belongsTo('\App\Role', 'role_id', 'id');
    }

    public function team()
    {
        return $this->belongsTo('\App\Team', 'team_id', 'id');
    }

    public static function getList($company_id = null, $sysUser = true)
    {
        $list = [];
        $data = self::select('id', 'first_name', 'last_name');

        if($company_id > 0)
            $data->where('company_id', $company_id);
        elseif(Auth::user()->company_id > 0)
        {
            $data->where('company_id', Auth::user()->company_id);
        }

        if(!$sysUser)
            $data->whereNotNull('company_id');

        $data = $data->orderBy('first_name')
                    ->get();
        foreach($data as $r)
            $list[$r->id] = $r->first_name.' '.$r->last_name;

        return $list;
    }

    public function getOpenTicket()
    {
        return \App\Ticket::where('created_by', \Auth::id())
                    ->where('status', 'open')
                    ->orderBy('created_at', 'DESC')
                    ->first();

    }

    public static function checkEmailExists($email)
    {
        return self::select('id')
                    ->withTrashed()
                    ->whereEmail($email)
                    ->first();
    }

    public static function findByEmail($email)
    {
        return self::where('email', $email)->first();
    }

    public function sendPasswordResetNotification($token)
    {
      $email = Input::get('email');
      $record = \DB::table('password_resets')->where('email', $email)->first();
      $record->token = $token;
      dispatch(new \App\Jobs\SendEmail($record, 'ForgotPassword'));
    //    $this->notify(new ForgotPasswordEmail($token));
    }

    public function azure()
    {
        return $this->hasOne(Azure::class);
    }
}
