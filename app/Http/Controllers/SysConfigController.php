<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Alert;
use Illuminate\Support\Facades\Storage;

class SysConfigController extends Controller
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
    public function setting()
    {
        $data = \App\SysConfig::first();
        $title = session('menuLabel')['configuration.system'];
        $breadcrumbs = [
            '' => trans('controllers.setting'),
        ];
        return view('sysconfig.setting', compact('title', 'breadcrumbs', 'data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateSetting(Request $request)
    {

        $record = \App\SysConfig::first();

        if(!$record)
            $record = new \App\SysConfig;

        $record->active_menu = str_replace('#', '', $request->active_menu ?: '20A8D8');
        $record->active_menu_hover = str_replace('#', '', $request->active_menu_hover ?: 'FFFFFF');
        $record->top_bar = str_replace('#', '', $request->top_bar ?: 'FFFFFF');
        $record->top_bar_text = str_replace('#', '', $request->top_bar_text ?: '20A8D8');
        $record->text_primary = str_replace('#', '', $request->text_primary ?: '20A8D8');
        $record->top_heading = $request->top_heading;
        $record->updated_by = \Auth::id();

        if ($request->hasFile('logo'))
        {
            if($record->logo)
            {
                $exists = \Storage::exists($record->logo);
                if($exists)
                    \Storage::delete($record->logo);
            }
            $image = $request->file('logo');
            $filename = md5($record->company_name. time()).'.'.$image->getClientOriginalExtension();
            $img = \Image::make($image->getRealPath());
            $img->resize(300, null, function ($constraint) {
                $constraint->aspectRatio();
            });
            if($img->save(public_path('storage/logo/'.$filename)))
                $record->logo = $filename;
        }

        if($record->save())
        {
            session(['colourTheme' => [
                'active_menu' => $request->active_menu,
                'active_menu_hover' => $request->active_menu_hover,
                'top_bar' => $request->top_bar,
                'top_bar_text' => $request->top_bar_text,
                'text_primary' => $request->text_primary,
            ]]);
            Alert::success(__('messages.save_success'));
            return redirect()->route('setting.index');
        }

        Alert::error(__('messages.invalid_request'));
        return redirect()->back()->withInput();
    }



    private function setEnvironmentValue($environmentName, $configKey, $newValue) {

        $oldValue = \Config::get($configKey);
        if($configKey == 'mail.from.name')
            $oldValue = "'" .$oldValue. "'";

        file_put_contents(\App::environmentFilePath(), str_replace(
            $environmentName . '=' . $oldValue,
            $environmentName . '=' . $newValue,
            file_get_contents(\App::environmentFilePath())
        ));
        \Config::set($configKey, $newValue);
    }

    /**
     * Show the SMTP Account Config page.
     *
     * @return \Illuminate\Http\Response
     */
    public function smtp()
    {
        $title = session('menuLabel')['configuration.smtp-account'];
        $breadcrumbs = [
            '' => $title
        ];

        $env = [
            'mail_driver' => \Config::get('mail.driver'),
            'mail_from_address' => \Config::get('mail.from.address'),
            'mail_from_name' => \Config::get('mail.from.name'),
            'mail_type' => 0,
        ];
        if($env['mail_driver'] == 'smtp'){
            $env += [
                'mail_host' => \Config::get('mail.host'),
                'mail_port' => \Config::get('mail.port'),
                'mail_username' => \Config::get('mail.username'),
                'mail_password' => \Config::get('mail.password'),
            ];
        }
        elseif($env['mail_driver'] == 'mailgun'){
            $env += [
                'mailgun_domain' => \Config::get('services.mailgun.domain'),
                'mailgun_secret' => \Config::get('services.mailgun.secret'),
            ];
        }
        elseif($env['mail_driver'] == 'sparkpost'){
            $env += [
                'sparkpost_secret' => \Config::get('services.sparkpost.secret'),
            ];
        }
        return view('sysconfig.smtp', compact('title', 'breadcrumbs', 'env'));
    }

    /**
     * Update SMTP Configuration
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function smtpUpdate(Request $request)
    {
        chmod(\App::environmentFilePath(), 0777);

        $this->setEnvironmentValue('MAIL_DRIVER', 'mail.driver', $request->mail_driver);
        if($request->mail_driver == 'smtp')
        {
            $rules = [
                'mail_host' => 'required',
                'mail_port' => 'required|numeric',
                'mail_username' => 'required',
                'mail_password' => 'required',
            ];
            $request->validate($rules);

            $this->setEnvironmentValue('MAIL_HOST', 'mail.host', $request->mail_host);
            $this->setEnvironmentValue('MAIL_PORT', 'mail.port', $request->mail_port);
            $this->setEnvironmentValue('MAIL_USERNAME', 'mail.username', $request->mail_username);
            $this->setEnvironmentValue('MAIL_PASSWORD', 'mail.password', $request->mail_password);
        }
        elseif($request->mail_driver == 'mailgun')
        {
            $rules = [
                'mailgun_domain' => 'required',
                'mailgun_secret' => 'required'
            ];
            $request->validate($rules);

            $this->setEnvironmentValue('MAILGUN_DOMAIN', 'services.mailgun.domain', $request->mailgun_domain);
            $this->setEnvironmentValue('MAILGUN_SECRET', 'services.mailgun.secret', $request->mailgun_secret);
        }
        elseif($request->mail_driver == 'sparkpost')
        {
            $rules = [
                'sparkpost_secret' => 'required'
            ];
            $request->validate($rules);

            $this->setEnvironmentValue('SPARKPOST_SECRET', 'services.sparkpost.secret', $request->sparkpost_secret);
        }

        $this->setEnvironmentValue('MAIL_FROM_ADDRESS', 'mail.from.address', $request->mail_from_address);
        $this->setEnvironmentValue('MAIL_FROM_NAME', 'mail.from.name', "'" .$request->mail_from_name. "'");

        chmod(\App::environmentFilePath(), 0600);
        \Artisan::call('config:cache');
        sleep(5);

        \Alert::success(__('messages.save_success'))->autoclose(3000);

        return redirect()->route('smtp-account.index');
    }

    /**
     * Show the SCORM API Config page.
     *
     * @return \Illuminate\Http\Response
     */
    public function scormAPI()
    {
        $title = session('menuLabel')['configuration.scorm-dispatch-api'];
        $breadcrumbs = [
            '' => $title
        ];

        $env = [
            'scorm_url' => \Config::get('scorm.url'),
            'scorm_id' => \Config::get('scorm.id'),
            'scorm_secret' => \Config::get('scorm.secret'),
            'bbb_url' => \Config::get('scorm.bbb_url'),
            'bbb_secret' => \Config::get('scorm.bbb_secret'),
        ];

        return view('sysconfig.scorm-api', compact('title', 'breadcrumbs', 'env'));
    }

    /**
     * Update SCORM API Configuration
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function scormAPIUpdate(Request $request)
    {
        chmod(\App::environmentFilePath(), 0777);

        $this->setEnvironmentValue('SCORM_URL', 'scorm.url', $request->scorm_url);
        $this->setEnvironmentValue('SCORM_ID', 'scorm.id', $request->scorm_id);
        $this->setEnvironmentValue('SCORM_SECRET', 'scorm.secret', $request->scorm_secret);
        $this->setEnvironmentValue('BBB_SERVER_BASE_URL', 'scorm.bbb_url', $request->bbb_url);
        $this->setEnvironmentValue('BBB_SECURITY_SALT', 'scorm.bbb_secret', $request->bbb_secret);

        chmod(\App::environmentFilePath(), 0600);

        \Alert::success(__('messages.save_success'))->autoclose(3000);
        return redirect()->route('scormdispatch-api.index');
    }

    /**
     * Show the PUSHER Channel Config page.
     *
     * @return \Illuminate\Http\Response
     */
    public function pusher()
    {
        $title = session('menuLabel')['configuration.pusher'];
        $breadcrumbs = [
            '' => $title
        ];
        $env = [
            'pusher_key' => \Config::get('broadcasting.connections.pusher.key'),
            'pusher_secret' => \Config::get('broadcasting.connections.pusher.secret'),
            'pusher_app_id' => \Config::get('broadcasting.connections.pusher.app_id'),
            'pusher_cluster' => \Config::get('broadcasting.connections.pusher.options.cluster'),
        ];

        return view('sysconfig.pusher', compact('title', 'breadcrumbs', 'env'));
    }

    /**
     * Update PUSHER Channel Configuration
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function pusherUpdate(Request $request)
    {
        chmod(\App::environmentFilePath(), 0777);

        $this->setEnvironmentValue('PUSHER_APP_KEY', 'broadcasting.connections.pusher.key', $request->pusher_key);
        $this->setEnvironmentValue('PUSHER_APP_SECRET', 'broadcasting.connections.pusher.secret', $request->pusher_secret);
        $this->setEnvironmentValue('PUSHER_APP_ID', 'broadcasting.connections.pusher.app_id', $request->pusher_app_id);
        $this->setEnvironmentValue('PUSHER_APP_CLUSTER', 'broadcasting.connections.pusher.options.cluster', $request->pusher_cluster);
        chmod(\App::environmentFilePath(), 0600);

        \Alert::success(__('messages.save_success'))->autoclose(3000);
        return redirect()->route('pusher.index');
    }
}
