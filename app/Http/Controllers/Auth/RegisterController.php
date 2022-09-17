<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use PragmaRX\Google2FA\Google2FA;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    //use RegistersUsers;
    use RegistersUsers {
            register as registration;
        }

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        return User::create([
            'first_name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'google2fa_secret' => $data['google2fa_secret'],
            'role' => '1',
            'role_id' => '2'
        ]);
    }

    public function register(Request $request)
   {
       //Validate the incoming request using the already included validator method
       $this->validator($request->all())->validate();

       // Save the registration data in an array
       $registration_data = $request->all();

       // Initialise the 2FA class
       $google2fa = new Google2FA();

       // Add the secret key to the registration data
       $registration_data["google2fa_secret"] = $google2fa->generateSecretKey();
       // Save the registration data to the user session for just the next request
       $request->session()->flash('registration_data', $registration_data);
//$request->session()->put('registration_data', $registration_data);
         //session('registration_data',$registration_data);
       // Generate the QR image. This is the image the user will scan with their app
        // to set up two factor authentication
      //  $google2fa->setAllowInsecureCallToGoogleApis(true);
      //  print_r($registration_data); die;

       $QR_Image = $google2fa->getQRCodeInline(
           config('app.name'),
           $registration_data['email'],
           $registration_data['google2fa_secret']
       );

       // Pass the QR barcode image to our view
       return view('google2fa.register', ['QR_Image' => $QR_Image, 'secret' => $registration_data['google2fa_secret']]);
   }

   public function completeRegistration(Request $request)
    {
        // add the session data back to the request input
      //if($request)
      //  $request->merge(session('registration_data'));
        // Call the default laravel authentication
        //return $this->registration($request);
        $data  = session('registration_data');
        $user = $this->create($data);
    //    dispatch(new \App\Jobs\SendEmail($user, 'WelcomeEmail'));
        $request->first_name = $data['name'];
        $request->email = $data['email'];
        $request->password = $data['password'];
        $request->google2fa_secret = $data['google2fa_secret'];
        $request->role = 2;

        return $this->registered($request, $user)
            ?: redirect($this->redirectPath());

    }
}
