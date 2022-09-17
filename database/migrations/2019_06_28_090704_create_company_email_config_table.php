<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCompanyEmailConfigTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('company_email_config', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('company_id');
			$table->string('mail_from_name_custom', 191)->nullable();
			$table->string('mail_driver', 191)->nullable();
			$table->string('from_address', 191)->nullable();
			$table->string('from_name', 191)->nullable();
			$table->string('smtp_host', 191)->nullable();
			$table->string('smtp_port', 191)->nullable();
			$table->string('smtp_username', 191)->nullable();
			$table->string('smtp_password', 191)->nullable();
			$table->string('mailgun_domain', 191)->nullable();
			$table->string('mailgun_secret', 191)->nullable();
			$table->string('sparkpost_secret', 191)->nullable();
			$table->timestamps();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('company_email_config');
	}

}
