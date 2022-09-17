<?php

namespace App\Metrogistics\AzureSocialite;

use Illuminate\Support\Arr;
use Laravel\Socialite\Two\User;
use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\ProviderInterface;

class AzureOauthProvider extends AbstractProvider implements ProviderInterface
{
    const IDENTIFIER = 'AZURE_OAUTH';
    protected $scopes = ['User.Read'];
    protected $scopeSeparator = ' ';

    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase(
            sprintf('https://login.microsoftonline.com/%s/oauth2/v2.0/authorize', config('azure-oath.credentials.tenant_id')),
            $state
        );
    }

    protected function getTokenUrl()
    {
        return sprintf('https://login.microsoftonline.com/%s/oauth2/v2.0/token', config('azure-oath.credentials.tenant_id'));
    }

    protected function getTokenFields($code)
    {
        return array_merge(parent::getTokenFields($code), [
            'grant_type' => 'authorization_code'//,
            // 'resource' => 'https://graph.microsoft.com',
        ]);
    }

    protected function getUserByToken($token)
    {
        $response = $this->getHttpClient()->get('https://graph.microsoft.com/v1.0/me/', [
            'headers' => [
                'Authorization' => 'Bearer '.$token,
            ],
        ]);

        return json_decode($response->getBody(), true);
    }

    public function user()
    {
        if ($this->hasInvalidState()) {
            throw new \Laravel\Socialite\Two\InvalidStateException;
        }

        $response = $this->getAccessTokenResponse($this->getCode());

        $user = $this->mapUserToObject($this->getUserByToken(
            $token = Arr::get($response, 'access_token')
        ));

        $user->idToken = Arr::get($response, 'id_token');
        $user->expiresAt = time() + Arr::get($response, 'expires_in');

        return $user->setToken($token)
                    ->setRefreshToken(Arr::get($response, 'refresh_token'));
    }

    protected function mapUserToObject(array $user)
    {
        $name_split = explode(' ', $user['displayName']);
        $split_count = count($name_split);

        return (new User())->setRaw($user)->map([
            'id'                => $user['id'],
            'name'              => $user['displayName'],
            'email'             => $user['mail'],
            
            'first_name'        => $name_split[0],
            'last_name'         => $split_count > 0 ? $name_split[$split_count - 1] : '',

            'businessPhones'    => $user['businessPhones'],
            'displayName'       => $user['displayName'],
            'givenName'         => $user['givenName'],
            'jobTitle'          => $user['jobTitle'],
            'mail'              => $user['mail'],
            'mobilePhone'       => $user['mobilePhone'],
            'officeLocation'    => $user['officeLocation'],
            'preferredLanguage' => $user['preferredLanguage'],
            'surname'           => $user['surname'],
            'userPrincipalName' => $user['userPrincipalName'],
        ]);
    }
}
