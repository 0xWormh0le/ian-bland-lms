<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCourseResultsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('course_results', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('courseuser_id');
			$table->integer('module_id');
			$table->string('scorm_regid', 191)->nullable();
			$table->string('complete_status', 191)->nullable();
			$table->string('satisfied_status', 191)->nullable();
			$table->dateTime('completion_date')->nullable();
			$table->string('score', 191)->nullable();
			$table->time('total_time')->nullable();
			$table->string('result_note', 191)->nullable();
			$table->string('scorm_regid_ref', 191)->nullable();
			$table->integer('notify')->nullable()->default(0);
			$table->integer('created_by')->nullable();
			$table->integer('updated_by')->nullable();
			$table->integer('deleted_by')->nullable();
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
		Schema::drop('course_results');
	}

}
