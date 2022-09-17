<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateScormConfigurationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('scorm_configurations', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('scormEngineServiceUrl');
			$table->string('appId');
			$table->string('securityKey');
			$table->string('originString');
			$table->string('proxy')->nullable();
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
		Schema::drop('scorm_configurations');
	}

}
