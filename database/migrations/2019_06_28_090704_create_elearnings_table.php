<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateElearningsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('elearnings', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('course_id');
			$table->integer('module_id');
			$table->string('title', 191);
			$table->text('description')->nullable();
			$table->integer('scorm_id')->nullable();
			$table->boolean('course_stream')->default(0);
			$table->timestamps();
			$table->softDeletes();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('elearnings');
	}

}
