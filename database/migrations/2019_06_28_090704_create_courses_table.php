<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCoursesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('courses', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('title', 191);
			$table->string('slug', 191)->nullable();
			$table->text('description')->nullable();
			$table->string('image', 191)->nullable();
			$table->date('deadline_date')->nullable();
			$table->integer('category_id')->nullable();
			$table->integer('sub_category_id')->nullable();
			$table->string('duration', 191)->nullable();
			$table->string('course_type', 191)->nullable();
			$table->string('language', 191)->nullable();
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
		Schema::drop('courses');
	}

}
