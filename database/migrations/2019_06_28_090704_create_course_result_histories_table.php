<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCourseResultHistoriesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('course_result_histories', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('courseresult_id');
			$table->string('complete_status', 191)->nullable();
			$table->string('satisfied_status', 191)->nullable();
			$table->dateTime('completion_date')->nullable();
			$table->string('score', 191)->nullable();
			$table->string('total_time', 191)->nullable();
			$table->string('result_note', 191)->nullable();
			$table->integer('attempt')->nullable();
			$table->integer('created_by')->nullable();
			$table->integer('updated_by')->nullable();
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
		Schema::drop('course_result_histories');
	}

}
