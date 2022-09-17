<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateScormScoesTrackTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('scorm_scoes_track', function(Blueprint $table)
		{
			$table->increments('id');
			$table->bigInteger('scoid')->unsigned()->index();
			$table->bigInteger('userid')->unsigned()->index();
			$table->bigInteger('scormid')->unsigned()->index();
			$table->bigInteger('attempt')->nullable();
			$table->string('elementname')->nullable();
			$table->text('elementvalue')->nullable();
			$table->string('objectiveid')->nullable();
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
		Schema::drop('scorm_scoes_track');
	}

}
