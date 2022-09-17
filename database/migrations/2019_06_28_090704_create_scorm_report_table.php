<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateScormReportTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('scorm_report', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('user')->unsigned()->index();
			$table->integer('course')->unsigned()->index();
			$table->string('complete_status');
			$table->string('satisfied_status');
			$table->string('score');
			$table->string('total_time');
			$table->string('attempt');
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
		Schema::drop('scorm_report');
	}

}
