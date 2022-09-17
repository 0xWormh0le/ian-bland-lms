<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateScormSeqObjectiveTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('scorm_seq_objective', function(Blueprint $table)
		{
			$table->increments('seqobjectiveid');
			$table->integer('scoid')->unsigned()->index();
			$table->integer('scorm')->unsigned()->index();
			$table->boolean('primaryobj')->nullable();
			$table->string('objectiveid')->nullable();
			$table->boolean('satisfiedbymeasure')->nullable();
			$table->float('minnormalizedmeasure')->nullable();
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
		Schema::drop('scorm_seq_objective');
	}

}
