<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Auth, Alert;
use Illuminate\Support\Facades\Hash;

class UserProfileController extends Controller
{

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = trans('controllers.update_profile');
        $breadcrumbs = [
            '' => $title,
        ];
        $user = Auth::user();

        return view('profile.index', compact('title', 'breadcrumbs', 'user'));
    }

    /**
     * Update Profile.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $rules = [
            'first_name' => 'required',
        ];
        $request->validate($rules);
        $user = \Auth::user();
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;

        if($request->image_base64)
        {
            if($user->avatar)
            {
                if(\Storage::disk('public')->exists('avatars/'.$user->avatar));
                    \Storage::disk('public')->delete('avatars/'.$user->avatar);
            }
            $profile_image = str_replace('data:image/png;base64,', '', $request->image_base64);
            $profile_image = str_replace(' ', '+', $profile_image);
            $avatar =  uuid().'.png';
            \Storage::disk('public')->put('/avatars/'.$avatar, base64_decode($profile_image));
            $user->avatar = $avatar;
        }

        if($user->save())
        {
            Alert::success(__('messages.save_success'))->autoclose(3000);
            return redirect()->route('user.profile');
        }
        Alert::error(__('messages.save_failed'))->autoclose(3000);
        return redirect()->back()->withInput();
    }


    /**
     * Get the User Initial as Avatar.
     *
     * @return \Illuminate\Http\Response
     */
    public function initialAvatar(Request $request, $user_id)
    {
        $user = \App\User::find($user_id);
        if($user)
            $name = $user->first_name;
        else
            $name = $user_id;

        $backgrounds = ['#8BC34A', '#20A8D8', '#FFC107', '#00BCD4', '#4A276B', '#009688'];
        $background = $backgrounds[array_rand($backgrounds)];
        $avatar = new \LasseRafn\InitialAvatarGenerator\InitialAvatar();
        if($request->has('size'))
            $size = $request->size;
        else
            $size = 70;
        return $avatar->name($name)
                ->length(1)
                ->fontSize(0.5)
                ->size($size)
                ->background($background)
                ->color('#fff')
                ->generate()
                ->stream('png', 100);
    }

    /**
     * Show the Change Password View.
     *
     * @return \Illuminate\Http\Response
     */
    public function changePassword()
    {
        $title = trans('controllers.change_password');
        $breadcrumbs = [
            '' => $title,
        ];

        $user = Auth::user();

        $auth_result = null;
        
        if($user->google2fa_enable == 1 && $user->google2fa_secret!="")
        {
           $auth_result = google2fa_authenticator($user->email, $user->google2fa_secret);
        }

        return view('profile.change-password', compact('title', 'breadcrumbs', 'user', 'auth_result'));
    }

    public function updatePassword(Request $request)
    {
      $user = \Auth::user();
      $user->google2fa_enable = $request->google2fa_enable;

      if($request->google2fa_enable == 1)
      {
          $user->google2fa_secret = $request->google2fa_secret;

      }
      else {
          $user->google2fa_secret = "";
      }



    if(trim($request->new_password) !="")
    {
      $rules = [
          'old_password' => 'required',
          'new_password' => 'required',
          'confirm_password' => 'required|same:new_password',
      ];
      $validate = $request->validate($rules);

      if($validate)
      {

        if (Hash::check($request->old_password, $user->password)) {
              $user->password =  Hash::make($request->new_password);
         }
        else {
          $msg = trans('messages.old_password_mismatch') ;
          return back()->withInput()->withErrors(['error'=> $msg]);
        }
       }
      }


      if($user->save())
      {
          Alert::success(__('messages.save_success'))->autoclose(3000);
          return redirect()->route('user.password');
      }
      Alert::error(__('messages.save_failed'))->autoclose(3000);
      return redirect()->back()->withInput();

  }


    public function ajaxGoogleAuth(Request $request)
    {
      $user = \Auth::user();
      $result = google2fa_authenticator($user->email);

      return response()->json($result);
    }

    public function googleAuth()
    {

      $user = \Auth::user();
      $google2fa =  app('pragmarx.google2fa');
      $secret = $user->google2fa_secret;

      if(trim($secret) =="")
      {
          $secret =  $google2fa->generateSecretKey();
          $user->google2fa_secret = $secret;
          $user->save();
      }

        $result = google2fa_authenticator($user->email, $secret);
        $key = $result["google2fa_secret"] ;
        $inlineUrl = $result["qr_image"] ;
        $valid = false ;



        return view('google2fa.index', compact('key', 'inlineUrl', 'valid'));
    }

    public function googleAuthCheck(Request $request)
    {
      $user = \Auth::user();
      $google2fa =  app('pragmarx.google2fa');
      $key = $user->google2fa_secret ;
      $window = \Config::get('google2fa.window');
      $valid = $google2fa->verifyKey($key, $request->code);

      \Cache::forever('auth_request_code', $request->code);

      if($valid)
      {
        $google2fa->login();
        return redirect('/home');
      }
      else {
        return redirect()->back()->with(['valid' => 1]);
      }
    }

}
