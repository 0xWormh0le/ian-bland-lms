<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateElearningUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('elearning_users', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('course_id');
			$table->integer('company_id');
			$table->integer('elearning_id');
			$table->integer('user_id');
			$table->boolean('is_presenter')->default(0);
			$table->integer('module_id')->nullable();
			$table->string('scorm_regid', 191)->nullable();
			$table->string('complete_status', 191)->nullable();
			$table->string('satisfied_status', 191)->nullable();
			$table->dateTime('completion_date')->nullable();
			$table->string('score', 191)->nullable();
			$table->string('total_time', 191)->nullable();
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
		Schema::drop('elearning_users');
	}

}
