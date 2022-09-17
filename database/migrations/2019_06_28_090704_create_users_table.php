<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('company_id')->nullable();
			$table->string('first_name', 191);
			$table->string('last_name', 191)->nullable();
			$table->string('email', 191)->unique();
			$table->string('password', 191)->nullable();
			$table->string('azure_id')->nullable();
			$table->integer('role');
			$table->boolean('active')->default(1);
			$table->integer('team_id')->nullable();
			$table->integer('role_id')->nullable();
			$table->boolean('is_verified')->default(0);
			$table->boolean('is_otp_verified')->default(0);
			$table->boolean('is_suspended')->default(0);
			$table->string('avatar', 191)->nullable();
			$table->string('language', 191)->nullable();
			$table->string('remember_token', 100)->nullable();
			$table->text('google2fa_secret')->nullable();
			$table->boolean('google2fa_enable')->default(0);
			$table->timestamps();
			$table->integer('created_by')->nullable();
			$table->integer('updated_by')->nullable();
			$table->softDeletes();
			$table->dateTime('last_login_at')->nullable();
			$table->string('last_login_ip', 191)->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('users');
	}

}
