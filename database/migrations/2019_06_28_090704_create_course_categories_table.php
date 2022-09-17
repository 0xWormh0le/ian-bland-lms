<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCourseCategoriesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('course_categories', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('title', 191);
			$table->string('slug', 191)->nullable();
			$table->integer('parent')->default(0);
			$table->timestamps();
			$table->softDeletes();
			$table->integer('deleted_by')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('course_categories');
	}

}
