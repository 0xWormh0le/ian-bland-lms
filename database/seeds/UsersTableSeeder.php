<?php

use Illuminate\Database\Seeder;
use App\User;
class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $datas = [
            [
                'first_name' => 'Super',
                'last_name' => 'Admin',
                'company_id' => null,
                'role' => 0,
                'role_id' => 0,
                'language' => 'en',
                'email' => 'superadmin@lms.dev',
                'password' => '123456',

            ]
        ];

        foreach($datas as $data)
        {
            $exist = User::whereEmail($data['email'])->first();
            if($exist)
                $user = User::find($exist->id);
            else
                $user = new User;
            $user->first_name = $data['first_name'];
            $user->last_name = $data['last_name'];
            $user->company_id = $data['company_id'];
            $user->role = $data['role'];
            $user->role_id = $data['role_id'];
            $user->language = $data['language'];
            $user->email = $data['email'];
            $user->password = bcrypt($data['password']);
            $user->save();
        }
    }
}
