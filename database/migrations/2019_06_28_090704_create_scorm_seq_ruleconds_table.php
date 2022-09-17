<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateScormSeqRulecondsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('scorm_seq_ruleconds', function(Blueprint $table)
		{
			$table->increments('ruleconditionsid');
			$table->bigInteger('scoid')->unsigned()->index();
			$table->bigInteger('scormid')->unsigned()->index();
			$table->string('conditioncombination');
			$table->integer('ruletype');
			$table->string('action');
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
		Schema::drop('scorm_seq_ruleconds');
	}

}
