<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateScormSeqMapinfoTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('scorm_seq_mapinfo', function(Blueprint $table)
		{
			$table->increments('mapinfoid');
			$table->integer('scoid')->unsigned()->index();
			$table->integer('scormid')->unsigned()->index();
			$table->integer('objectiveid')->unsigned()->index();
			$table->string('targetobjectiveid')->nullable();
			$table->boolean('readsatisfiedstatus')->nullable();
			$table->boolean('readnormalizedmeasure')->nullable();
			$table->boolean('writesatisfiedstatus')->nullable();
			$table->boolean('writenormalizedmeasure')->nullable();
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
		Schema::drop('scorm_seq_mapinfo');
	}

}
