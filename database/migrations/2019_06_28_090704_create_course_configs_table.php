<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCourseConfigsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('course_configs', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('course_id');
			$table->string('transversal_rule', 191)->nullable();
			$table->string('completion_rule', 191)->nullable();
			$table->string('completion_modules', 191)->nullable();
			$table->decimal('completion_percentage')->nullable();
			$table->string('learning_path', 191)->nullable();
			$table->boolean('get_certificate')->default(0);
			$table->string('certificate_name', 191)->nullable();
			$table->integer('certificate_design_id')->nullable();
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
		Schema::drop('course_configs');
	}

}
