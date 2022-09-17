<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateScormSeqRulecondTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('scorm_seq_rulecond', function(Blueprint $table)
		{
			$table->increments('rulecondid');
			$table->bigInteger('scoid')->unsigned()->index();
			$table->bigInteger('scormid')->unsigned()->index();
			$table->bigInteger('ruleconditionsid')->unsigned()->index();
			$table->string('referencedobjective');
			$table->float('measurethreshold');
			$table->string('operator');
			$table->string('conditions');
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
		Schema::drop('scorm_seq_rulecond');
	}

}
