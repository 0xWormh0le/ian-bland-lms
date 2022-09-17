<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMyCertificatesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('my_certificates', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('user_id');
			$table->integer('course_id');
			$table->integer('design_id');
			$table->date('certified_date');
			$table->date('expiry_date')->nullable();
			$table->boolean('is_valid')->default(0);
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
		Schema::drop('my_certificates');
	}

}
