<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCompaniesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('companies', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('company_name', 191);
			$table->string('slug', 191)->nullable();
			$table->string('email', 191)->nullable();
			$table->integer('max_users')->nullable();
			$table->string('active_from', 128)->nullable();
			$table->string('active_to', 128)->nullable();
			$table->string('colour_theme', 30)->nullable();
			$table->string('active_menu_hover', 6)->nullable();
			$table->string('top_bar', 6)->nullable();
			$table->string('top_bar_text', 6)->nullable();
			$table->string('text_primary', 6)->nullable();
			$table->string('active_menu', 6)->nullable();
			$table->string('top_heading', 191)->nullable();
			$table->string('logo', 191)->nullable();
			$table->string('timezone', 191)->nullable();
			$table->string('language', 191)->nullable();
			$table->boolean('active')->default(1);
			$table->integer('created_by')->nullable();
			$table->integer('updated_by')->nullable();
			$table->integer('deleted_by')->nullable();
			$table->timestamps();
			$table->softDeletes();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('companies');
	}

}
