<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Menu;
use Alert, Auth;

class MenuController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $menu = new Menu;

        $listMenu = [];

        $lists = $menu->sysAdminMenu();
        foreach($lists as $k => $m)
        {
            $listMenu['system'][] = $k;
            foreach($m['children'] as $ck => $cm)
            {
                $listMenu['system'][] = $ck;
            }
        }

        $lists = $menu->clientMenu();
        foreach($lists as $k => $m)
        {
            $listMenu['client'][] = $k;
            foreach($m['children'] as $ck => $cm)
            {
                $listMenu['client'][] = $ck;
            }
        }

        $listMenu['module'][] = 'elearning';
        $listMenu['module'][] = 'document';

        $title = trans('controllers.menu_management');
        $breadcrumbs = [
            '' => trans('controllers.menu_management'),
        ];
        return view('sysconfig.menu', compact('title', 'breadcrumbs', 'listMenu'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        foreach($request->menu as $level => $menu)
        {
            $client_level = $level !== 'system' ? true : false;
            foreach($menu as $menu_id => $label)
            {
                $record = Menu::findMenu($client_level, $menu_id);
                if(!$record)
                    $record = new Menu;
                $record->company_id = \Auth::user()->company_id;
                $record->client_level = $client_level;
                $record->menu_id = $menu_id;
                $record->label = $label;
                $record->save();
            }
        }
        \Auth::user()->getMenu();

        Alert::success(__('messages.save_success'));
        return redirect()->route('menu.index');

    }

}
