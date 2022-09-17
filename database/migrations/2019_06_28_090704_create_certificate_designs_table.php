<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCertificateDesignsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('certificate_designs', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name', 191);
			$table->string('orientation', 20);
			$table->string('pagesize', 20);
			$table->string('background', 191)->nullable();
			$table->string('thumbnail', 191)->nullable();
			$table->text('content')->nullable();
			$table->boolean('draft')->default(1);
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
		Schema::drop('certificate_designs');
	}

}
