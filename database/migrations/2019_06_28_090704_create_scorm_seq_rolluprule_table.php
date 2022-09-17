<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateScormSeqRollupruleTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('scorm_seq_rolluprule', function(Blueprint $table)
		{
			$table->increments('rollupruleid');
			$table->integer('scoid')->unsigned()->index();
			$table->integer('scormid')->unsigned()->index();
			$table->string('childactivityset')->nullable();
			$table->string('minimumcount')->nullable();
			$table->float('minimumpercent')->nullable();
			$table->string('conditioncombination')->nullable();
			$table->string('action')->nullable();
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
		Schema::drop('scorm_seq_rolluprule');
	}

}
