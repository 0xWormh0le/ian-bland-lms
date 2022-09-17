<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSysconfigTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sysconfig', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('colour_theme', 191)->nullable();
			$table->string('active_menu_hover', 6)->nullable();
			$table->string('top_bar', 6)->nullable();
			$table->string('top_bar_text', 6)->nullable();
			$table->string('text_primary', 6)->nullable();
			$table->string('active_menu', 6)->nullable();
			$table->string('top_heading', 191)->nullable();
			$table->string('logo', 191)->nullable();
			$table->integer('updated_by')->nullable();
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
		Schema::drop('sysconfig');
	}

}
