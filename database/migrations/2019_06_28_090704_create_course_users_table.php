<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCourseUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('course_users', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('course_id');
			$table->integer('user_id');
			$table->integer('course_member_id')->nullable();
			$table->string('role', 20);
			$table->integer('active')->default(1);
			$table->dateTime('enrol_date')->nullable();
			$table->integer('enrolled_by')->nullable();
			$table->boolean('completed')->default(0);
			$table->decimal('completion_percentage', 6)->default(0.00);
			$table->date('completion_date')->nullable();
			$table->date('start_date')->nullable();
			$table->integer('self_enroll')->default(0);
			$table->integer('notify')->nullable()->default(0);
			$table->dateTime('course_reminder_date')->nullable();
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
		Schema::drop('course_users');
	}

}
