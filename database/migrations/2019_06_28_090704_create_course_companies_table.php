<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCourseCompaniesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('course_companies', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('course_id');
			$table->integer('company_id');
			$table->integer('active')->default(1);
			$table->string('deadline', 191)->nullable();
			$table->integer('notification_reminder')->nullable();
			$table->string('completion_notification')->nullable()->default('1');
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
		Schema::drop('course_companies');
	}

}
