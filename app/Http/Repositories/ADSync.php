<?php

namespace App\Http\Repositories;

use App\User;
use App\Role;
use App\Job;
use App\Company;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;


class ADSync
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client;
    }

    protected function accessTokenRequest($tenant_id, $client_id, $client_secret)
    {
        $res = '';

        try {
            $res = $this->client->post(
                sprintf('https://login.microsoftonline.com/%s/oauth2/v2.0/token', $tenant_id), [
                    'form_params' => [
                        'client_id' => $client_id,
                        'client_secret' => $client_secret,
                        'scope' => 'https://graph.microsoft.com/.default',
                        'grant_type' => 'client_credentials'
                    ]
                ]
            );
        } catch (RequestException $e) {
            $desc = json_decode($e->getResponse()->getBody()->getContents())->error_description;
            $end = strpos($desc, "\r\n");
            $reason = substr($desc, 0, $end);

            throw new Exception($reason);
        }

        return json_decode(($res->getBody()->getContents()))->access_token;
    }

    protected function usersRequest($token)
    {
        $next_link = 'https://graph.microsoft.com/v1.0/users?$select=givenName,surName,id,mail,displayName,userPrincipalName,department&$top=999';
        $users = collect([]);

        try {
            while ($next_link) {
                $res = $this->client->get($next_link, [
                    'headers' => [
                        'Authorization' => 'Bearer '.$token,
                    ]
                ]);

                $result = json_decode($res->getBody()->getContents());
                $users = $users->merge($result->value);

                if (isset($result->{'@odata.nextLink'})) {
                    $next_link = $result->{'@odata.nextLink'};
                } else {
                    $next_link = false;
                }
            }
        } catch (RequestException $e) {
            throw new Exception(trans("modules.permission_error"));
        }

        return $users;
    }

    protected function deleteRequest($id, $token)
    {
        $this->client->delete('https://graph.microsoft.com/v1.0/users/'.$id, [
            'headers' => [ 'Authorization' => 'Bearer '.$token ]
        ]);
    }

    protected function inviteRequest($email, $token)
    {
        try {
            $res = $this->client->post('https://graph.microsoft.com/v1.0/invitations/', [
                'headers' => [
                    'Authorization' => 'Bearer '.$token,
                ],
                'json' => [
                    'invitedUserEmailAddress' => $email,
                    'inviteRedirectUrl' => config('app.url')
                ]
            ]);
        } catch (RequestException $e) {
            return NULL;
            // throw new Exception;
        }

        return json_decode($res->getBody()->getContents())->invitedUser->id;
    }

    protected function handleUserInfo($users)
    {
        return $users->map(function ($value) {
            $user = [
                'azure_id'      => $value->id,
                'first_name'    => $value->givenName,
                'last_name'     => $value->surname,
                'email'         => $value->mail,
                'department'    => $value->department
            ];

            if (empty($user['first_name'])) {
                $dn = $value->displayName;
                $at = strpos($dn, '@');
                
                if ($at !== false) {
                    $dn = substr($dn, 0, $at);
                }

                $names = explode('.', $dn);
                $user['first_name'] = $names[0];
                
                if (count($names) > 1) {
                    $user['last_name'] = $names[1];
                }
            }

            if (empty($user['email'])) {
                $ext_splits = explode('#EXT#', $value->userPrincipalName);
                if (count($ext_splits) > 1) {
                    $user['email'] = str_replace('_', '@', explode('#EXT#', $value->userPrincipalName)[0]);
                } else {
                    $user['email'] = $value->userPrincipalName;
                }
            }

            return $user;
        });
    }

    protected function diffByEmail($foo, $bar, $field)
    {
        $res = [];

        foreach ($foo as $f) {
            $exist = false;
            foreach ($bar as $b) {
                if ($f['email'] == $b['email']) {
                    $exist = true;
                    break;
                }
            }
            if (!$exist) {
                $res[] = $f['azure_id'];
            }
        }

        return $res;
    }

    protected function getUserAzureId($user, $sso_users, $access_token)
    {
        foreach ($sso_users as $u) {
            if ($user['email'] == $u['email']) {
                return $u['azure_id'];
            }
        }

        $azure_id = $this->inviteRequest($user['email'], $access_token);
        return $azure_id;
    }

    /*
     *  @params
     *      $users:         users registered at company admin AD
     *      $sso_users:     users registered at LMS AD
     *      $access_token:  token to access LMS AD
     *      $job_id:        Job ID in Laravel queue
     */

    protected function syncOneCompanyAdmin($users, $company, $sso_users, $access_token, $job_id = NULL)
    {
        $users = $users->toArray();
        $company->sync_total = count($users);
        $company->save();

        // Sync users on AD

        try {
            // Invite users if not listed in organzation active directory
            foreach ($users as $key => &$user) {
                if ($key % 50 == 0) {
                    $company->sync_processed = $key;
                    $company->save();

                    if (!is_null($job_id) && $this->deletedFromQueue($job_id)) {
                        return;
                    }
                }

                $user['azure_id'] = $this->getUserAzureId($user, $sso_users, $access_token);
            }
            unset($user);
        } catch (Exception $e) {
            throw new Exception(trans('modules.ad_sync_fail'));
        }

        $company->sync_processed = $company->sync_total;
        $company->save();

        $users = collect($users)->filter(function ($user) {
            return !empty($user['azure_id']);
        });

        // Sync users on DB

        $learner_role = Role::where('is_learner', 1)->first();
        $company_id = $company->id;

        foreach ($users as $user) {
            $email_duplicate = User::where('email', $user['email'])->first();
            $update = User::where(function ($query) use ($user) {
                $query->where('email', $user['email'])
                    ->where('azure_id', '<>', $user['azure_id'])
                    ->whereNotNull('azure_id');
            })
            ->orWhere(function ($query) use ($user) {
                $query->where('email', '<>', $user['email'])
                    ->where('azure_id', $user['azure_id']);
            });

            if ($update->count() > 0) {
                $update->forceDelete();
            } else if ($email_duplicate) {
                if (is_null($email_duplicate->azure_id)) {
                    $email_duplicate->company_id   = $company_id;
                    $email_duplicate->azure_id     = $user['azure_id'];
                    $email_duplicate->first_name   = $user['first_name'];
                    $email_duplicate->last_name    = $user['last_name'];
                    $email_duplicate->email        = $user['email'];
                    $email_duplicate->department   = $user['department'];
                    $email_duplicate->active       = true;
                    $email_duplicate->role = 1;
                    $email_duplicate->role_id = optional($learner_role)->id;
                    $email_duplicate->save();
                }
                continue;
            }

            User::onlyTrashed()->where('email', $user['email'])->forceDelete();

            $new_user = new User;
            $new_user->company_id   = $company_id;
            $new_user->azure_id     = $user['azure_id'];
            $new_user->first_name   = $user['first_name'];
            $new_user->last_name    = $user['last_name'];
            $new_user->email        = $user['email'];
            $new_user->department   = $user['department'];
            $new_user->active       = true;
            $new_user->role = 1;
            $new_user->role_id = optional($learner_role)->id;
            $new_user->save();
        }

        $db_azure_ids = User::where('company_id', $company_id)
                            ->whereNotNull('azure_id')
                            ->get()
                            ->pluck('azure_id');
        $ad_azure_ids = $users->pluck('azure_id');

        User::whereIn('azure_id', $db_azure_ids->diff($ad_azure_ids))->forceDelete();
    }

    protected function getUsers($tenant_id, $client_id, $client_secret, $exclude_admin = false)
    {
        // Get access token to use when calling Graph API
        $token = $this->accessTokenRequest($tenant_id, $client_id, $client_secret);

        // Get users registered to company admin AD
        $users = $this->usersRequest($token);

        if ($exclude_admin) {
            // Filter admin user
            $users = $users->filter(function ($user) {
                return isset($user->mail) || strpos($user->userPrincipalName, '#EXT#@') === false;
            });
        }

        $users = $this->handleUserInfo($users);

        return compact('token', 'users');
    }

    private function deletedFromQueue($job_id)
    {
        return Job::where('queue', 'adsync')
                ->where('id', $job_id)->count() == 0;
    }

    public function checkAzure($tenant_id, $client_id, $client_secret, &$reason)
    {
        try {
            $this->accessTokenRequest($tenant_id, $client_id, $client_secret);
        } catch (Exception $e) {
            $reason = $e->getMessage();
            return false;
        }
        return true;
    }

    public function sync($tenant_id, $client_id, $client_secret, $company_id, $job_id)
    {
        $company = Company::find($company_id);
        $max_users = $company->max_users;
        $company->sync_processed = -1; // Status: Getting users from Azure AD
        $company->save();

        $users = $this->getUsers($tenant_id, $client_id, $client_secret);
        $users = $users['users'];

        if ($this->deletedFromQueue($job_id)) {
            return;
        }

        if (!is_null($max_users)) {
            $users = $users->take($max_users);
        }

        try {
            $res = $this->getUsers(
                config('azure-oath.credentials.tenant_id'),
                config('azure-oath.credentials.client_id'),
                config('azure-oath.credentials.client_secret'),
                true
            );

            $token = $res['token'];
            $sso_users = $res['users'];
        } catch (Exception $e) {
            throw new Exception(trans('modules.ad_sync_fail'));
        }

        // ini_set('max_execution_time', $users->count());

        $this->syncOneCompanyAdmin($users, $company, $sso_users, $token, $job_id);
    }

    public function syncAll()
    {
        try {
            $users = $this->getUsers(
                config('azure-oath.credentials.tenant_id'),
                config('azure-oath.credentials.client_id'),
                config('azure-oath.credentials.client_secret'),
                true
            );
            $sso_users = $users['users'];
            $token = $users['token'];
        } catch (Exception $e) { }

        // Get company admins
        $company_admins = User::with('azure')->where([
            ['company_id', '>', 0],
            ['role_id', 1]
        ])->get();

        $users = collect([]);

        foreach ($company_admins as $ca) {
            $azure = $ca->azure;
            $max_users = $ca->company->max_users;

            if (isset($azure)) {
                try {
                    $company = $ca->company;
                    $company->sync_processed = -1;
                    $company->save();

                    $res = $this->getUsers($azure->tenant_id, $azure->client_id, $azure->client_secret);
                    
                    if (!is_null($max_users)) {
                        $res['users'] = $res['users']->take($max_users);
                    }

                    $users = $users->merge($res['users']);

                    $this->syncOneCompanyAdmin(
                        $res['users'],
                        $company,
                        $sso_users,
                        $token
                    );
                } catch (Exception $e) { }
            }
        }

        // Get what to remove from current organzation active directory
        $remove = $this->diffByEmail($sso_users, $users, 'azure_id');

        try {
            foreach ($remove as $id) {
                $this->deleteRequest($id, $token);
            }
        } catch (Exception $e) { }
    }
}
