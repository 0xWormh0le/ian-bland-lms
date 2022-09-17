<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCompanyCertificateConfigTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('company_certificate_config', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('company_id');
			$table->integer('validity_years')->nullable();
			$table->integer('validity_months')->nullable();
			$table->integer('validity_weeks')->nullable();
			$table->integer('validity_days')->nullable();
			$table->string('signer', 191)->nullable();
			$table->string('position', 191)->nullable();
			$table->text('signature')->nullable();
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
		Schema::drop('company_certificate_config');
	}

}
