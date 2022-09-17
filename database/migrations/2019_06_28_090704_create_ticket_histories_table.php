<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTicketHistoriesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('ticket_histories', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('ticket_id');
			$table->string('action', 20);
			$table->string('ticket_status', 20)->nullable();
			$table->integer('user_id')->nullable();
			$table->integer('response_id')->nullable();
			$table->string('comments')->nullable();
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
		Schema::drop('ticket_histories');
	}

}
