<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateScormScoesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('scorm_scoes', function(Blueprint $table)
		{
			$table->increments('scoid');
			$table->integer('scormid')->unsigned()->nullable()->index();
			$table->string('manifest')->nullable();
			$table->string('organization')->nullable();
			$table->string('parent')->nullable();
			$table->string('identifier')->nullable();
			$table->string('launch')->nullable();
			$table->string('scormtype')->nullable();
			$table->string('title')->nullable();
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
		Schema::drop('scorm_scoes');
	}

}
