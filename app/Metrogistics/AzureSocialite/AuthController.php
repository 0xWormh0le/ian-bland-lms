<?php

namespace App\Metrogistics\AzureSocialite;

use Illuminate\Routing\Controller;
use Laravel\Socialite\Facades\Socialite;
use App\Exceptions\SSOException;

class AuthController extends Controller
{
    public function redirectToOauthProvider()
    {
        return Socialite::driver('azure-oauth')->redirect();
    }

    public function handleOauthResponse()
    {
        try {
            $user = Socialite::driver('azure-oauth')->user();

            $authUser = $this->findOrCreateUser($user);

            if (!$authUser->active) {
                throw new SSOException('auth.failed');
            }
            
            auth()->login($authUser, true);

            // session([
            //     'azure_user' => $user
            // ]);

            return redirect(config('azure-oath.redirect_on_login'));
        } catch (\Laravel\Socialite\Two\InvalidStateException $e) {
            return redirect('/login');
        }
    }

    protected function findOrCreateUser($user)
    {
        $user_class = config('azure-oath.user_class');
        
        $duplicate = $user_class::where([
            ['email', '=', $user->email],
            [config('azure-oath.user_id_field'), '<>', $user->id]
        ])->count();

        if ($duplicate) {
            throw new SSOException('auth.account_duplicate');
        }

        if (empty($user->email)) {
            $user->email = str_replace('_', '@', explode('#EXT#', $user->userPrincipalName)[0]);
        }

        if (!filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
            throw new SSOException('auth.sso_invalid_email');
        }
        
        $authUser = $user_class::where(config('azure-oath.user_id_field'), $user->id)->first();
        
        if ($authUser) {
            return $authUser;
        }

        $UserFactory = new UserFactory();

        return $UserFactory->convertAzureUser($user);
    }
}
