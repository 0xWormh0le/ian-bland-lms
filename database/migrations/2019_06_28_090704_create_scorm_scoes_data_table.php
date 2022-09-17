<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateScormScoesDataTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('scorm_scoes_data', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('scoid')->nullable();
			$table->integer('scormid')->nullable();
			$table->string('elementname')->nullable();
			$table->text('elementvalue')->nullable();
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
		Schema::drop('scorm_scoes_data');
	}

}
