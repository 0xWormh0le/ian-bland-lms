<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateScormSeqRolluprulecondTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('scorm_seq_rolluprulecond', function(Blueprint $table)
		{
			$table->increments('rollupruleconditionid');
			$table->integer('scoid')->unsigned()->index();
			$table->integer('scormid')->unsigned()->index();
			$table->integer('rollupruleid')->unsigned()->index();
			$table->string('operator')->nullable();
			$table->string('conditions')->nullable();
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
		Schema::drop('scorm_seq_rolluprulecond');
	}

}
