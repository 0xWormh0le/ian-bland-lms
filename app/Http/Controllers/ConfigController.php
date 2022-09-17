<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Alert, Auth;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use App\Company;
use App\WelcomeTemplate;
use App\CompanyEmailConfig;

class ConfigController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $company = Company::find(Auth::user()->company_id);

        if($company->timezone == "")
          $company->timezone = \Config::get("app.timezone");
        if($company->language == "")
          $company->language = \Config::get("app.fallback_locale");

        if($company)
        {
            $title = trans('controllers.configuration');
            $breadcrumbs = [
                '' => trans('controllers.configuration'),
            ];
            $timezones = tz_list();
            return view('administration.configuration', compact('title', 'breadcrumbs', 'company', 'timezones'));
        }
        Alert::error(__('messages.invalid_request'));
        return redirect('/');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {

        $record = Company::find(Auth::user()->company_id);
        if($record)
        {
            $record->company_name = $request->company_name;
            $record->timezone = $request->timezone?:'';
            $record->language = $request->language?:'';
            $record->top_heading = $request->top_heading;
            $record->active_menu = str_replace('#', '', $request->active_menu ?: '20A8D8');
            $record->active_menu_hover = str_replace('#', '', $request->active_menu_hover ?: 'FFFFFF');
            $record->top_bar = str_replace('#', '', $request->top_bar ?: 'FFFFFF');
            $record->top_bar_text = str_replace('#', '', $request->top_bar_text ?: '20A8D8');
            $record->text_primary = str_replace('#', '', $request->text_primary ?: '20A8D8');
            $record->slug = null;
            $record->updated_by = Auth::id();

            if ($request->hasFile('logo'))
            {
                if($record->logo)
                {
                    $exists = Storage::exists($record->logo);
                    if($exists)
                        Storage::delete($record->logo);
                }
                $image = $request->file('logo');
                $filename = md5($record->company_name. time()).'.'.$image->getClientOriginalExtension();
                $img = Image::make($image->getRealPath());
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
                return redirect()->route('configuration.index');
            }
        }
        Alert::error(__('messages.invalid_request'));
        return redirect('/');
    }

    public function smtp()
    {

       $record = CompanyEmailConfig::where("company_id", Auth::user()->company_id)->first();

       $title = trans('controllers.smtp_account');
       $breadcrumbs = [
           '' => $title
       ];


       $env = [
           'mail_driver' => $record? $record->mail_driver :'',
           'mail_from_address' => $record? $record->from_address:'',
           'mail_from_name' => $record?$record->from_name:'',
           'mail_type' => 1,
       ];
       if($env['mail_driver'] == 'smtp'){
           $env += [
               'mail_host' => $record?$record->smtp_host:'',
               'mail_port' => $record?$record->smtp_port:'',
               'mail_username' => $record?$record->smtp_username:'',
               'mail_password' => $record?$record->smtp_password:'',
           ];
       }
       elseif($env['mail_driver'] == 'mailgun'){
           $env += [
               'mailgun_domain' => $record?$record->mailgun_domain:'',
               'mailgun_secret' => $record?$record->mailgun_secret:'',
           ];
       }
       elseif($env['mail_driver'] == 'sparkpost'){
           $env += [
               'sparkpost_secret' => $record?$record->sparkpost_secret:'',
           ];
       }

       $env['mail_from_name_custom']  = $record?$record->mail_from_name_custom:'';

       return view('sysconfig.smtp', compact('title', 'breadcrumbs', 'env'));
    }

    public function smtpUpdate(Request $request)
    {
        $record = CompanyEmailConfig::firstOrCreate(["company_id" => Auth::user()->company_id]);

        $record->mail_driver = $request->mail_driver;

        if($request->mail_driver == 'smtp')
        {

          $rules = [
              'mail_host' => 'required',
              'mail_port' => 'required|numeric',
              'mail_username' => 'required',
              'mail_password' => 'required',
          ];
          $request->validate($rules);

          $record->from_address = $request->mail_from_address;
          $record->from_name = $request->mail_from_name ;
          $record->smtp_host = $request->mail_host ;
          $record->smtp_port = $request->mail_port ;
          $record->smtp_username = $request->mail_username;
          $record->smtp_password = $request->mail_password ;
        }
        elseif($request->mail_driver == 'mailgun')
        {
          $rules = [
              'mailgun_domain' => 'required',
              'mailgun_secret' => 'required'
          ];
          $request->validate($rules);

          $record->mailgun_domain = $request->mailgun_domain ;
          $record->mailgun_secret = $request->mailgun_secret ;
        }
        elseif($request->mail_driver == 'sparkpost')
        {
          $rules = [
              'sparkpost_secret' => 'required'
          ];
          $request->validate($rules);

          $record->sparkpost_secret = $request->sparkpost_secret ;
        }

        $record->save();

        return redirect()->back();
    }

    public function mailUpdate(Request $request)
    {
      $record = CompanyEmailConfig::firstOrCreate(["company_id" => Auth::user()->company_id]);

      $record->mail_from_name_custom = $request->mail_from_name_custom;

      $record->save();

      return redirect()->back();

    }

    public function smtpConfigReset(Request $request)
    {
      $record = CompanyEmailConfig::where(["company_id" => Auth::user()->company_id])->delete();
      return redirect()->back();
    }

    public function showWelcome(Request $request)
    {
        $breadcrumbs = [
            route('email-setup.index') => trans('controllers.welcome_screen')
        ];
        $title = trans('controllers.welcome_screen');

        $user = $request->user();
        $content = welcome_template($request->user());

        return view('welcome-config.index', compact(
            'title',
            'breadcrumbs',
            'content'
        ));
    }

    public function updateWelcome(Request $request)
    {
        $content = $request->input('content', '');
        $user = $request->user();
        $company = $user->company;

        if ($company) {
            WelcomeTemplate::updateOrCreate([
                'company_id' => $company->id,
                'language' => $company->language ?? 'en',
            ], ['content' => $content]);
        }

        return redirect()->route('welcome-config.show');
    }
}
