<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateScormTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('scorm', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('org_id')->unsigned()->index();
			$table->integer('course_id')->unsigned()->index();
			$table->string('scormname')->nullable();
			$table->string('reference')->nullable();
			$table->string('filesize')->nullable();
			$table->string('repository')->nullable();
			$table->string('format', 50)->nullable();
			$table->string('targetid')->nullable();
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
		Schema::drop('scorm');
	}

}
